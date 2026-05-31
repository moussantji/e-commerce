<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Produits;
use App\Notifications\ProductFavoriNotification;

class BestOffers extends Component
{
    public function toggleWishlist($productId)
    {
        $user = auth()->user();
        if (!$user) return; // ✅ Sécurité

        $exists = $user->wishlistProducts()->where('produits_id', $productId)->exists();

        $productslug = Produits::find($productId)->getSlug(); // ✅ Récupère le slug du produit
        if ($exists) {
            $user->wishlistProducts()->detach($productId); // ✅ SUPPRIME ligne DB
            return redirect()->route('produits.show', ['slug' => $productslug, 'id' => $productId])->with('success', 'Produit retiré de votre liste de souhaits !'); // ✅ Redirige vers la page des favoris après l'action

        } else {
            $user->wishlistProducts()->attach($productId); // ✅ AJOUTE ligne DB
            return redirect()->route('produits.show', ['slug' => $productslug, 'id' => $productId])->with('success', 'Produit ajouté à votre liste de souhaits !'); // ✅ Redirige vers la page des favoris après l'action

        }

        // Livewire refresh automatique → vue mise à jour !

    }

    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }


    public function render()
    {
        $bestOffers = Produits::where('is_active', true)
            ->whereNotNull('sale_price')
            ->orderBy('sale_price', 'asc')
            ->with(['reviews'])
            ->limit(8)
            ->get();

        return view('livewire.client.best-offers', compact('bestOffers'));
    }
}
