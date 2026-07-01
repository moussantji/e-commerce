<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

/**
 * Liste de souhaits (favoris) — mobile.
 * S'appuie sur User::wishlistProducts() (table wishlist_user_produit).
 */
class WishlistController extends Controller
{
    /** Liste des produits favoris de l'utilisateur. */
    public function index(Request $request)
    {
        $products = $request->user()
            ->wishlistProducts()
            ->with(['photos', 'category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'nb_etoiles')
            ->get();

        return ProductResource::collection($products);
    }

    /** Vérifie si un produit est en favori. */
    public function check(Request $request, $productId)
    {
        $favorited = $request->user()
            ->wishlistProducts()
            ->where('produits_id', $productId)
            ->exists();

        return response()->json(['favorited' => $favorited]);
    }

    /** Ajoute / retire un produit des favoris (toggle). */
    public function toggle(Request $request, $productId)
    {
        $user = $request->user();
        $exists = $user->wishlistProducts()->where('produits_id', $productId)->exists();

        if ($exists) {
            $user->wishlistProducts()->detach($productId);
            $favorited = false;
        } else {
            $user->wishlistProducts()->attach($productId);
            $favorited = true;
        }

        return response()->json([
            'favorited' => $favorited,
            'count' => $user->wishlistProducts()->count(),
        ]);
    }

    /** Retire explicitement un produit des favoris. */
    public function destroy(Request $request, $productId)
    {
        $request->user()->wishlistProducts()->detach($productId);

        return response()->json([
            'favorited' => false,
            'count' => $request->user()->wishlistProducts()->count(),
        ]);
    }
}
