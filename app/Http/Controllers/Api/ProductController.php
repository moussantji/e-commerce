<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Categories;
use App\Models\Produits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Produits::query()
            ->where('is_active', true)
            ->with(['category', 'photos'])
            ->withCount('reviews')
            ->withAvg('reviews', 'nb_etoiles');

        $this->applyFilters($query, $request);

        // Tri (recommander / populaires / prix)
        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'popular' => $query->orderByDesc('reviews_count'),
            default => $query->latest(),
        };
        // Tri secondaire déterministe -> évite les doublons entre pages
        $query->orderByDesc('id');

        $products = $query->paginate((int) $request->query('per_page', 15));

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Produits::with([
            'category',
            'photos',
            'brand',
            'caracteristiques',
            'reviews' => fn ($q) => $q
                ->with(['user', 'response', 'photos'])
                ->latest()
                ->limit(10),
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'nb_etoiles')
            ->findOrFail($id);

        // Produits similaires (même catégorie, en stock) — comme le site web
        $similar = Produits::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['photos', 'category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'nb_etoiles')
            ->limit(10)
            ->get();

        return (new ProductResource($product))->additional([
            'similar' => ProductResource::collection($similar),
        ]);
    }

    /**
     * Facettes de filtre disponibles pour le contexte courant (recherche/catégorie).
     * Reproduit les filtres du site web : catégories, marques, caractéristiques
     * (Couleur, Matériau, Alimentation...), fourchette de prix.
     */
    public function filters(Request $request)
    {
        // Requête de base SANS les sélections de facettes (search + category seulement)
        $base = Produits::query()->where('is_active', true);

        if (!$request->boolean('include_out_of_stock')) {
            $base->where('stock', '>', 0);
        }
        if ($search = $request->query('search')) {
            $this->applySearch($base, $search);
        }
        if ($categoryId = $request->query('category_id')) {
            $base->whereIn('category_id', $this->categoryIds($categoryId));
        }

        $ids = (clone $base)->pluck('id');

        // Marques disponibles
        $brands = [];
        if (Schema::hasColumn('produits', 'brand_id') && Schema::hasTable('brands')) {
            $brands = DB::table('produits as p')
                ->join('brands as b', 'b.id', '=', 'p.brand_id')
                ->whereIn('p.id', $ids)
                ->where('b.is_active', true)
                ->select('b.id', 'b.name')
                ->distinct()
                ->orderBy('b.name')
                ->get();
        }

        // Caractéristiques groupées par type (Color, Material, Power Supply...)
        $caracteristiques = [];
        if (Schema::hasTable('produit_caracteristique') && $ids->isNotEmpty()) {
            $rows = DB::table('produit_caracteristique as pc')
                ->join('caracteristiques as c', 'c.id', '=', 'pc.caracteristiques_id')
                ->whereIn('pc.produits_id', $ids)
                ->whereNotNull('c.type')
                ->where('c.type', '!=', '')
                ->select('c.type', 'pc.value')
                ->distinct()
                ->get();

            $caracteristiques = $rows
                ->groupBy('type')
                ->map(fn ($g) => $g->pluck('value')->filter()->unique()->values())
                ->filter(fn ($values) => $values->isNotEmpty())
                ->map(fn ($values, $type) => [
                    'type' => $type,
                    'label' => ucfirst($type),
                    'values' => $values,
                ])
                ->values();
        }

        // Sous-catégories du contexte
        $categories = [];
        if ($categoryId = $request->query('category_id')) {
            $categories = Categories::where('is_active', true)
                ->where(fn ($q) => $q->where('id', $categoryId)->orWhere('parent_id', $categoryId))
                ->select('id', 'name', 'slug')
                ->get();
        } elseif ($ids->isNotEmpty()) {
            $catIds = (clone $base)->pluck('category_id')->filter()->unique();
            $categories = Categories::whereIn('id', $catIds)
                ->where('is_active', true)
                ->select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();
        }

        // Fourchette de prix (prix effectif = sale_price sinon price)
        $priceStats = (clone $base)
            ->selectRaw('MIN(COALESCE(sale_price, price)) as min_price, MAX(COALESCE(sale_price, price)) as max_price')
            ->first();

        return response()->json([
            'total' => $ids->count(),
            'categories' => $categories,
            'brands' => $brands,
            'caracteristiques' => $caracteristiques,
            'price' => [
                'min' => (float) ($priceStats->min_price ?? 0),
                'max' => (float) ($priceStats->max_price ?? 0),
            ],
        ]);
    }

    /** Applique tous les filtres (mêmes règles que le composant web Produits). */
    private function applyFilters($query, Request $request): void
    {
        // Rupture de stock
        if (!$request->boolean('include_out_of_stock')) {
            $query->where('stock', '>', 0);
        }

        // Recherche texte (nom / description / sku)
        if ($search = $request->query('search')) {
            $this->applySearch($query, $search);
        }

        // Catégorie (+ sous-catégories)
        if ($categoryId = $request->query('category_id')) {
            $query->whereIn('category_id', $this->categoryIds($categoryId));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // En promotion uniquement
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price');
        }

        // Marques (par nom, comme le web)
        $brands = array_filter((array) $request->query('brands', []));
        if (!empty($brands)) {
            $query->whereHas('brand', fn ($q) => $q->whereIn('name', $brands));
        }

        // Caractéristiques dynamiques : ?carac[Color][]=Rouge&carac[Material][]=Métal
        $carac = $request->query('carac', []);
        if (is_array($carac)) {
            foreach ($carac as $type => $values) {
                $values = array_filter((array) $values);
                if (empty($values)) {
                    continue;
                }
                $query->whereHas('caracteristiques', function ($q) use ($type, $values) {
                    $q->where('caracteristiques.type', 'LIKE', "%{$type}%")
                        ->whereIn('produit_caracteristique.value', $values);
                });
            }
        }

        // Fourchette de prix (prix effectif)
        if (is_numeric($min = $request->query('min_price'))) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [(float) $min]);
        }
        if (is_numeric($max = $request->query('max_price'))) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [(float) $max]);
        }

        // Note minimale
        if (is_numeric($rating = $request->query('rating'))) {
            $query->whereHas('reviews', function ($q) use ($rating) {
                $q->groupBy('produits_id')
                    ->havingRaw('AVG(nb_etoiles) >= ?', [(float) $rating]);
            });
        }
    }

    private function applySearch($query, string $search): void
    {
        $escaped = addcslashes($search, '%_');
        $query->where(function ($sub) use ($escaped) {
            $sub->where('name', 'LIKE', "%{$escaped}%")
                ->orWhere('description', 'LIKE', "%{$escaped}%")
                ->orWhere('sku', 'LIKE', "%{$escaped}%");
        });
    }

    private function categoryIds($categoryId): \Illuminate\Support\Collection
    {
        return Categories::where('id', $categoryId)
            ->orWhere('parent_id', $categoryId)
            ->pluck('id');
    }
}
