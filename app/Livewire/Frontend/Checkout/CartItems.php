<?php

namespace App\Livewire\Frontend\Checkout;

use App\Models\Commandes;
use App\Models\Produits;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CartItems extends Component
{
    public $commande;
    public $orderItems = [];

    public function mount($commande)
    {
        $this->commande = $commande;
        $this->loadOrderItems();
    }

    private function loadOrderItems()
    {
        // ✅ Get Produits MODELS directly (not stdClass)
        $this->orderItems = Produits::whereHas('commandes', function ($q) {
            $q->where('commandes.id', $this->commande->id);
        })->get()->map(function ($produit) {
            // Add commande_produit data as custom attributes
            $cp = DB::table('commande_produit')
                ->where('commande_id', $this->commande->id)
                ->where('produit_id', $produit->id)
                ->first();

            if ($cp) {
                $produit->quantite = $cp->quantite;
                $produit->total = $cp->total;
            } else {
                $produit->quantite = 1;
                $produit->total = $produit->prix ?? 0;
            }

            return $produit; // ✅ Full Produits model with getPhoto() method
        });
    }


    public function render()
    {
        return view('livewire.frontend.checkout.cart-items');
    }
}
