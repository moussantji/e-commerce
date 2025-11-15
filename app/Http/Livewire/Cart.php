<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Paniers;
use App\Models\Produits;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{
    public $cart;
    public $total = 0;
    public $itemsCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        if (Auth::check()) {
            $this->cart = Paniers::with('products')->where('user_id', Auth::id())->first();
            
            if ($this->cart) {
                $this->total = $this->cart->total;
                $this->itemsCount = $this->cart->products->sum('pivot.quantity');
            }
        }
    }

    public function removeFromCart($productId)
    {
        if ($this->cart) {
            $this->cart->products()->detach($productId);
            $this->emit('cartUpdated');
            session()->flash('message', 'Produit retiré du panier');
        }
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($this->cart && $quantity > 0) {
            $this->cart->products()->updateExistingPivot($productId, ['quantity' => $quantity]);
            $this->emit('cartUpdated');
        }
    }

    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
