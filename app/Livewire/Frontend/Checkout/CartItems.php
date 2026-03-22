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
        $produits = Produits::whereHas('commandes', function ($q) {
            $q->where('commandes.id', $this->commande->id);
        })->get();

        $commandeProduits = DB::table('commande_produit')
            ->where('commande_id', $this->commande->id)
            ->get()
            ->keyBy('produit_id');

        $this->orderItems = $produits->map(function ($produit) use ($commandeProduits) {
            $cp = $commandeProduits->get($produit->id);

            if ($cp) {
                $produit->quantite = $cp->quantite;
                $produit->total = $cp->total;
            } else {
                $produit->quantite = 1;
                $produit->total = $produit->prix ?? 0;
            }

            return $produit;
        });
    }


    public function render()
    {
        return view('livewire.frontend.checkout.cart-items');
    }
}
