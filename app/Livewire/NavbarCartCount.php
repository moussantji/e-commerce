<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NavbarCartCount extends Component
{
     public $cartCount = 0;

    // 🔥 ÉCOUTE événement du Cart
    protected $listeners = ['refresh-navbar-cart' => '$refresh'];

    public function render()
    {
        $this->cartCount = Auth::check() ?
            DB::table('panier_produit')
                ->join('paniers', 'panier_produit.paniers_id', '=', 'paniers.id')
                ->where('paniers.user_id', Auth::id())
                ->sum('panier_produit.quantite')
            : session('cart_count', 0);

        return view('livewire.navbar-cart-count');
    }
}
