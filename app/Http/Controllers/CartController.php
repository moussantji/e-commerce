<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Paniers;
use App\Models\Produits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view('cart', [
            'categories' => $categories
        ]);
    }

    public function addCart(Request $request, $produitId)
    {
        $user = Auth::user();

        $produit = Produits::findOrFail($produitId);
        $panier = $user->panier; // ou Panier::create() si pas de panier

        $ligne = DB::table('panier_produit')
            ->where('paniers_id', $panier->id)
            ->where('produits_id', $produitId)
            ->first();

        if ($ligne) {
            // Produit déjà dans le panier → on augmente la quantité
            DB::table('panier_produit')
                ->where('paniers_id', $panier->id)
                ->where('produits_id', $produitId)
                ->update([
                    'quantite'    => DB::raw('quantite + 1'),
                    'total_ligne' => DB::raw('prix_unitaire * quantite'),
                ]);
        } else {
            // On ajoute une nouvelle ligne
            DB::table('panier_produit')->insert([
                'paniers_id'     => $panier->id,
                'produits_id'    => $produitId,
                'quantite'       => 1,
                'prix_unitaire'  => $produit->price,
                'total_ligne'    => $produit->price,
            ]);
        }

        // Optionnel : retirer de la wishlist
        $user->wishlistProducts()->detach($produitId);

        return redirect()->back()->with('success', 'Produit ajouté au panier');
    }
}
