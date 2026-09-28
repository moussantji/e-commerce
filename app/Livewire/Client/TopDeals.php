<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Produits;

class TopDeals extends Component
{
    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

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



    public function render()
    {
        $topDeals = Produits::where('is_active', true)
            ->with(['reviews', 'photos'])
            ->latest()
            ->get();

        return view('livewire.client.top-deals', compact('topDeals'));
    }
}
