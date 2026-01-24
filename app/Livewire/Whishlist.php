<?php

namespace App\Livewire;

use App\Models\Paniers;
use Livewire\Component;
use App\models\Produits as Product;
use Illuminate\Support\Facades\Auth;

class Whishlist extends Component
{
    public function removeFromWishlist($produitId)
    {
        auth()->user()->wishlistProducts()->detach($produitId);
        $this->dispatch('wishlist-updated');
        session()->flash('message', 'Produit retiré de la wishlist !');
        redirect()->route('favoris');
    }

    public function addToCart($productId): void  // ← SUPPRIMEZ $quantity
    {
        $quantity = 1;
        $product = Product::findOrFail($productId);
        $prixUnitaire = $product->price; // ou votre champ prix

        // 1. Récupère / crée le panier
        // 2. Récupère / crée le panier actif
        $panier = Paniers::firstOrCreate(
            [
                'status' => 'actif',
                'user_id' => Auth::id()
            ],
            [
                'status' => 'actif',
                'user_id' => Auth::id(),
            ]
        );

        // ✅ Récupère le pivot directement depuis la relation chargée
        $pivotExistant = $panier->products()
            ->where('produits_id', $productId)
            ->first()
            ?->pivot;

        if ($pivotExistant) {
            // MODIF DIRECTE sur l'objet Pivot (en mémoire)
            $pivotExistant->quantite += $quantity;
            $pivotExistant->total_ligne += ($quantity * $prixUnitaire);

            // ⚠️ IMPORTANT: syncChanges() pour persister en DB
            $pivotExistant->save();
        } else {
            // Nouveau produit
            $panier->products()->attach($productId, [
                'quantite' => $quantity,
                'prix_unitaire' => $prixUnitaire,
                'total_ligne' => $quantity * $prixUnitaire,
            ]);
        }

        $this->removeFromWishlist($productId);
        session()->flash('message', "✅ {$quantity}x {$product->nom} ajouté !");
        redirect()->route('panier');
    }

    public function render()
    {
        $products = auth()->user()->wishlistProducts()
            ->with(['photos', 'brand', 'caracteristiques'])
            ->get();

        $wishlistCount = auth()->user()->wishlistProducts()->count();

        return view('livewire.whishlist', compact('products', 'wishlistCount'));
    }
}
