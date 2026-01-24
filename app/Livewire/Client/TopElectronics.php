<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Produits;
use App\Models\Categories;

class TopElectronics extends Component
{
    public $category_id = '';

    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    public function render()
    {
        $categories = Categories::where('is_active', true)->get();

        $topelectronic = Produits::where('is_active', true)
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->with(['reviews'])
            ->limit(8)
            ->get();
        return view('livewire.client.top-electronics', compact('topelectronic', 'categories'));
    }
}
