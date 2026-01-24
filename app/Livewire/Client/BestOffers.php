<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Produits;

class BestOffers extends Component
{

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
