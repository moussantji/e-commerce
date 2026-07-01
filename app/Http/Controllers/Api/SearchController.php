<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Produits;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Recherche mobile — reproduit la logique du composant Livewire SearchEcommerce
 * (produits, catégories, marques, tags) et l'expose via l'API.
 */
class SearchController extends Controller
{
    /**
     * Suggestions de recherche.
     * - Avec ?q= : suggestions filtrées (comme la navbar web).
     * - Sans q  : mots-clés populaires (catégories + tags) pour l'écran "Rechercher et Trouver".
     */
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([
                'query' => '',
                'suggestions' => $this->popular(),
            ]);
        }

        // Échappe les jokers LIKE (% et _) pour éviter les faux positifs
        $term = '%' . addcslashes($q, '%_') . '%';

        // 1. PRODUITS (name)
        $products = Produits::where('name', 'LIKE', $term)
            ->where('is_active', true)
            ->with('photos')
            ->limit(4)
            ->get()
            ->map(fn ($product) => [
                'type' => 'produit',
                'title' => $product->name,
                'query' => $product->name,
                'id' => $product->id,
                'slug' => method_exists($product, 'getSlug') ? $product->getSlug() : null,
                'image' => $this->productImage($product),
            ]);

        // 2. CATÉGORIES
        $categories = Categories::where('name', 'LIKE', $term)
            ->where('is_active', true)
            ->limit(3)
            ->get()
            ->map(fn ($category) => [
                'type' => 'categorie',
                'title' => $category->name,
                'query' => $category->name,
                'id' => $category->id,
                'slug' => $category->slug,
                'count' => $category->products()->count(),
            ]);

        // 3. MARQUES
        $brands = collect();
        if (Schema::hasTable('brands')) {
            $brands = Brand::where('name', 'LIKE', $term)
                ->where('is_active', true)
                ->limit(2)
                ->get()
                ->map(fn ($brand) => [
                    'type' => 'marque',
                    'title' => $brand->name,
                    'query' => $brand->name,
                    'id' => $brand->id,
                    'slug' => $brand->slug,
                ]);
        }

        // 4. TAGS (seulement ceux avec des produits)
        $tags = collect();
        if (Schema::hasTable('tags')) {
            $tags = Tag::where('name', 'LIKE', $term)
                ->whereHas('produits')
                ->withCount('produits')
                ->limit(2)
                ->get()
                ->map(fn ($t) => [
                    'type' => 'tag',
                    'title' => $t->name,
                    'query' => $t->name,
                    'id' => $t->id,
                    'slug' => $t->slug,
                    'count' => $t->produits_count,
                ]);
        }

        // 5. RECHERCHE GÉNÉRALE (tous les résultats)
        $all = [[
            'type' => 'tous',
            'title' => $q,
            'query' => $q,
        ]];

        $suggestions = collect($products)
            ->merge($categories)
            ->merge($brands)
            ->merge($tags)
            ->merge($all)
            ->take(10)
            ->values();

        return response()->json([
            'query' => $q,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Mots-clés populaires (état par défaut, sans recherche saisie).
     * Catégories les plus fournies + tags les plus utilisés.
     */
    public function popular()
    {
        $categories = Categories::where('is_active', true)
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit(8)
            ->get()
            ->map(fn ($c) => [
                'type' => 'categorie',
                'title' => $c->name,
                'query' => $c->name,
                'id' => $c->id,
                'slug' => $c->slug,
                'count' => $c->products_count,
            ]);

        $tags = collect();
        if (Schema::hasTable('tags')) {
            $tags = Tag::whereHas('produits')
                ->withCount('produits')
                ->orderByDesc('produits_count')
                ->limit(8)
                ->get()
                ->map(fn ($t) => [
                    'type' => 'tag',
                    'title' => $t->name,
                    'query' => $t->name,
                    'id' => $t->id,
                    'slug' => $t->slug,
                    'count' => $t->produits_count,
                ]);
        }

        return $categories->merge($tags)->take(12)->values();
    }

    /** URL absolue de l'image principale d'un produit (comme ProductResource). */
    private function productImage(Produits $product): ?string
    {
        $photo = method_exists($product, 'getPhoto') ? $product->getPhoto() : null;
        $path = $photo ? $photo->getImageUrl(300, 300) : asset('assets/img/products/1.png');

        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return rtrim(request()->getSchemeAndHttpHost(), '/') . '/' . ltrim($path, '/');
    }
}
