<?php

namespace App\Livewire\Client;

use App\Models\Produits;
use App\Notifications\ProductFavoriNotification;
use Livewire\Component;

class InfiniteProducts extends Component
{
    public int $perPage = 12;

    public array $wishlistItems = [];

    public function mount(): void
    {
        $this->loadWishlist();
    }

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    private function loadWishlist(): void
    {
        if (auth()->check()) {
            $this->wishlistItems = auth()->user()
                ->wishlistProducts()
                ->pluck('produits_id')
                ->toArray();
        }
    }

    public function toggleWishlist($produitId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Produits::findOrFail($produitId);

        if (in_array($produitId, $this->wishlistItems)) {
            auth()->user()->wishlistProducts()->detach($produitId);
            $this->wishlistItems = array_diff($this->wishlistItems, [$produitId]);
        } else {
            auth()->user()->wishlistProducts()->attach($produitId);
            $this->wishlistItems[] = $produitId;
            auth()->user()->notify(new ProductFavoriNotification(
                auth()->user(),
                $product
            ));
        }
    }

    public function render()
    {
        $query = Produits::with(['brand', 'category', 'photos'])
            ->where('is_active', true)
            ->latest();

        // Défilement infini : on ne charge que `perPage` produits,
        // et on indique s'il en reste pour charger la suite au scroll.
        $total = (clone $query)->count();
        $products = $query->take($this->perPage)->get();

        return view('livewire.client.infinite-products', [
            'products' => $products,
            'hasMore' => $total > $this->perPage,
        ]);
    }
}
