<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Commandes;
use App\Models\Produits;
use App\Models\Tag;
use App\Models\User;
use App\Support\ImageUploads;
use App\Support\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Espace vendeur de l'application mobile.
 *
 * Reprend les fonctionnalités de l'espace vendeur du site web :
 * tableau de bord (produits, commandes, revenus, stock bas), gestion de ses
 * propres produits (création, modification, suppression, photos) et liste des
 * commandes contenant ses produits. Toutes les requêtes sont strictement
 * limitées au vendeur authentifié.
 */
class SellerController extends Controller
{
    private function seller(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user && $user->role === 'vendeur',
            403,
            'Accès réservé aux vendeurs.',
        );

        return $user;
    }

    /** Tableau de bord : indicateurs, dernières commandes et meilleures ventes. */
    public function dashboard(Request $request)
    {
        $seller = $this->seller($request);

        $productsQuery = Produits::where('vendeur_id', $seller->id);
        $productCount = (clone $productsQuery)->count();
        $lowStock = (clone $productsQuery)->where('stock', '<=', 5)->count();

        $orderIds = $this->sellerOrderIds($seller->id);

        $revenue = (float) DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('produits.vendeur_id', $seller->id)
            ->sum('commande_produit.total');

        $recentOrders = Commandes::with(['user', 'produits.photos'])
            ->whereIn('id', $orderIds)
            ->latest()
            ->take(5)
            ->get();

        $topProducts = Produits::select('produits.id', 'produits.name')
            ->selectRaw('COALESCE(SUM(commande_produit.quantite), 0) as vendus')
            ->leftJoin('commande_produit', 'commande_produit.produit_id', '=', 'produits.id')
            ->where('produits.vendeur_id', $seller->id)
            ->groupBy('produits.id', 'produits.name')
            ->orderByDesc('vendus')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'products' => $productCount,
                'orders' => $orderIds->count(),
                'revenue' => $revenue,
                'low_stock' => $lowStock,
            ],
            'recent_orders' => $recentOrders
                ->map(fn (Commandes $order) => $this->orderPayload($order, $seller->id))
                ->values(),
            'top_products' => $topProducts->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sold' => (int) ($product->vendus ?? 0),
            ])->values(),
        ]);
    }

    /** Catégories, marques et tags disponibles pour le formulaire produit. */
    public function options(Request $request)
    {
        $this->seller($request);

        return response()->json([
            'categories' => Categories::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($category) => [
                    'value' => $category->id,
                    'label' => $category->name,
                ])
                ->values(),
            'brands' => Brand::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($brand) => [
                    'value' => $brand->id,
                    'label' => $brand->name,
                ])
                ->values(),
            'tags' => Tag::orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($tag) => [
                    'value' => $tag->id,
                    'label' => $tag->name,
                ])
                ->values(),
        ]);
    }

    /** Liste paginée des produits du vendeur connecté. */
    public function products(Request $request)
    {
        $seller = $this->seller($request);

        $query = Produits::with(['category', 'brand', 'photos', 'tags'])
            ->where('vendeur_id', $seller->id);

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate((int) $request->query('per_page', 30));

        return response()->json([
            'data' => $products->getCollection()
                ->map(fn (Produits $product) => $this->productPayload($product))
                ->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /** Crée un produit appartenant au vendeur connecté. */
    public function storeProduct(Request $request)
    {
        $seller = $this->seller($request);
        $data = $request->validate($this->productRules(null));

        $tags = $data['tags'] ?? [];
        unset($data['tags'], $data['images'], $data['remove_images'], $data['uploaded_images']);

        $data['vendeur_id'] = $seller->id;
        $data['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;
        $data['stock'] = $data['stock'] ?? 0;
        $data['description'] = $data['description'] ?? '';

        // SKU vide : on laisse le modèle générer PRD-00001.
        if (array_key_exists('sku', $data) && blank($data['sku'])) {
            unset($data['sku']);
        }

        $product = Produits::create($data);
        $product->tags()->sync($tags);
        $this->handleImages($request, $product);

        return response()->json([
            'message' => 'Produit créé avec succès.',
            'data' => $this->productPayload($this->freshProduct($product)),
        ], 201);
    }

    /** Met à jour un produit du vendeur connecté. */
    public function updateProduct(Request $request, $id)
    {
        $seller = $this->seller($request);
        $product = $this->ownedProduct($seller->id, $id);

        $data = $request->validate($this->productRules($product));

        $tags = $data['tags'] ?? [];
        unset($data['tags'], $data['images'], $data['remove_images'], $data['uploaded_images']);

        $data['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : $product->is_active;
        if (array_key_exists('description', $data) && $data['description'] === null) {
            $data['description'] = '';
        }
        if (array_key_exists('sku', $data) && blank($data['sku'])) {
            unset($data['sku']);
        }

        $product->update($data);

        if ($request->has('tags') || $request->boolean('tags_present')) {
            $product->tags()->sync($tags);
        }

        $this->handleImages($request, $product);

        return response()->json([
            'message' => 'Produit mis à jour.',
            'data' => $this->productPayload($this->freshProduct($product)),
        ]);
    }

    /** Supprime un produit du vendeur connecté (et ses photos). */
    public function destroyProduct(Request $request, $id)
    {
        $seller = $this->seller($request);
        $product = $this->ownedProduct($seller->id, $id);

        foreach ($product->photos()->get() as $photo) {
            $photo->delete();
        }
        $product->tags()->detach();
        $product->delete();

        return response()->json(['message' => 'Produit supprimé.']);
    }

    /** Commandes contenant au moins un produit du vendeur connecté. */
    public function orders(Request $request)
    {
        $seller = $this->seller($request);
        $orderIds = $this->sellerOrderIds($seller->id);

        $query = Commandes::with(['user', 'produits.photos'])
            ->whereIn('id', $orderIds)
            ->latest();

        if ($request->filled('status')) {
            $query->where('statut', $request->query('status'));
        }

        $orders = $query->paginate((int) $request->query('per_page', 15));

        return response()->json([
            'data' => $orders->getCollection()
                ->map(fn (Commandes $order) => $this->orderPayload($order, $seller->id))
                ->values(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /** Identifiants des commandes contenant les produits du vendeur. */
    private function sellerOrderIds(int $sellerId)
    {
        return DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('produits.vendeur_id', $sellerId)
            ->distinct()
            ->pluck('commande_produit.commande_id');
    }

    private function ownedProduct(int $sellerId, $id): Produits
    {
        $product = Produits::where('vendeur_id', $sellerId)->find($id);

        abort_unless($product, 403, 'Ce produit ne vous appartient pas.');

        return $product;
    }

    /** Règles de validation communes à la création et à la modification. */
    private function productRules(?Produits $product): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('produits', 'sku')->ignore($product?->id),
            ],
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:4096',
            'uploaded_images' => 'nullable|array|max:10',
            'uploaded_images.*' => 'string|max:500',
            'image' => 'nullable|image|max:4096',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'remove_image' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Retire les photos demandées puis ajoute les nouvelles (10 maximum).
     * L'image principale (`produits.image`) est toujours la première photo.
     */
    private function handleImages(Request $request, Produits $product): void
    {
        if ($request->boolean('remove_image')) {
            foreach ($product->photos()->get() as $photo) {
                $photo->delete();
            }
        } elseif ($request->filled('remove_images')) {
            $ids = array_filter(array_map('intval', (array) $request->input('remove_images', [])));
            if (! empty($ids)) {
                foreach ($product->photos()->whereIn('id', $ids)->get() as $photo) {
                    $photo->delete();
                }
            }
        }

        $files = [];
        if ($request->hasFile('images')) {
            $files = array_filter(
                (array) $request->file('images'),
                fn ($file) => $file && ! $file->getError(),
            );
        }

        // Compatibilité avec l'ancien champ `image` (une seule photo).
        $legacy = $request->file('image');
        if ($legacy && ! $legacy->getError()) {
            foreach ($product->photos()->get() as $photo) {
                $photo->delete();
            }
            $files = [$legacy];
        }

        $remaining = max(0, 10 - $product->photos()->count());
        foreach (array_slice(array_values($files), 0, $remaining) as $file) {
            $filename = $file->store('Produits/'.$product->id, 'public');
            $product->photos()->create(['filename' => $filename]);
        }

        // Images déjà téléversées en JSON/base64, dans l'ordre choisi.
        $remaining = max(0, 10 - $product->photos()->count());
        $uploadedPaths = array_slice(
            array_values((array) $request->input('uploaded_images', [])),
            0,
            $remaining,
        );
        foreach ($uploadedPaths as $path) {
            if (
                ! is_string($path)
                || ! ImageUploads::ownedBy($request->user(), $path)
                || ImageUploads::isAttached($path)
            ) {
                continue;
            }

            $product->photos()->create(['filename' => $path]);
        }

        $first = $product->photos()->orderBy('id')->first();
        $product->forceFill(['image' => $first?->filename])->save();
    }

    private function freshProduct(Produits $product): Produits
    {
        return $product->fresh(['category', 'brand', 'photos', 'tags']);
    }

    /** Représentation d'un produit pour le formulaire et la liste mobile. */
    private function productPayload(Produits $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'sale_price' => $product->sale_price !== null ? (float) $product->sale_price : null,
            'stock' => (int) $product->stock,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'category' => optional($product->category)->name,
            'brand_id' => $product->brand_id,
            'brand' => optional($product->brand)->name,
            'is_active' => (bool) $product->is_active,
            'image' => $this->photoUrl($product),
            'images' => $this->photoList($product),
            'tags' => $product->relationLoaded('tags')
                ? $product->tags->pluck('id')->values()
                : $product->tags()->pluck('tags.id')->values(),
        ];
    }

    /** Représentation d'une commande, limitée aux articles du vendeur. */
    private function orderPayload(Commandes $order, int $sellerId): array
    {
        $items = $order->produits
            ->where('vendeur_id', $sellerId)
            ->map(function (Produits $product) {
                $quantity = (int) ($product->pivot->quantite ?? 1);
                $unitPrice = (float) ($product->pivot->prix_unitaire ?? 0);
                $lineTotal = (float) ($product->pivot->total ?? ($quantity * $unitPrice));

                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $this->photoUrl($product),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            })
            ->values();

        return [
            'id' => $order->id,
            'numero' => $order->numero_commande ?? ('#'.$order->id),
            'client' => optional($order->user)->name ?? 'Client',
            'client_tel' => optional($order->user)->tel,
            'statut' => $order->statut,
            'statut_label' => OrderStatus::label($order->statut),
            'total' => (float) $order->total,
            'seller_total' => (float) $items->sum('line_total'),
            'date' => optional($order->created_at)->format('d/m/Y H:i'),
            'items' => $items,
        ];
    }

    /** Photos d'un produit avec identifiant et URL absolue. */
    private function photoList(Produits $product): array
    {
        $photos = $product->relationLoaded('photos')
            ? $product->photos
            : $product->photos()->orderBy('id')->get();

        return $photos->sortBy('id')->map(fn ($photo) => [
            'id' => $photo->id,
            'url' => $this->abs($photo->getImageUrl(400, 400)),
        ])->values()->all();
    }

    /** URL absolue de la première photo d'un produit. */
    private function photoUrl(Produits $product): ?string
    {
        if ($product->relationLoaded('photos') && $product->photos->isNotEmpty()) {
            $photo = $product->photos->sortBy('id')->first();
        } else {
            $photo = $product->getPhoto();
        }

        return $photo ? $this->abs($photo->getImageUrl(400, 400)) : null;
    }

    /** Transforme un chemin relatif en URL absolue joignable depuis le mobile. */
    private function abs(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return rtrim(request()->getSchemeAndHttpHost(), '/').'/'.ltrim($path, '/');
    }
}
