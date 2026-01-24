<?php

namespace App\Livewire;

use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Models\Produits;
use App\Models\Categories;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SearchEcommerce extends Component
{
    public $search = '';
    public $suggestions = [];

    public function updatedSearch()
    {
        $this->generateSuggestions();
    }

    public function generateSuggestions()
    {
        if (strlen($this->search) < 1) {
            $this->suggestions = [];
            return;
        }

        $term = '%' . $this->search . '%';

        // 1. PRODUITS (name)
        $products = Produits::where('name', 'LIKE', $term)
            ->where('is_active', true)
            ->limit(3)
            ->get()
            ->map(fn($product) => [
                'title' => $product->name,
                'type' => 'Produit',
                'url' => route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]),
                'query' => $product->name
            ]);

        // 2. CATÉGORIES (Categories)
        $categories = Categories::where('name', 'LIKE', $term)
            ->limit(3)
            ->get()
            ->map(fn($category) => [
                'title' => $category->name,
                'type' => 'Catégorie',
                'url' => '/produits?category=' . urlencode($category->slug),
                'query' => $category->name,
                'count' => $category->products()->count()
            ]);

        // 3. BRANDS (Brand)
        $brands = collect();
        if (Schema::hasTable('brands')) {
            $brands = Brand::where('name', 'LIKE', $term)
                ->limit(2)
                ->get()
                ->map(fn($brand) => [
                    'title' => $brand->name,
                    'type' => 'Marque',
                    'url' => '/produits?brands=' . urlencode($brand->id),
                    'query' => $brand->name
                ]);
        }

        $tags = Tag::where('name', 'LIKE', $term)
            ->whereHas('produits')  // ✅ SEULEMENT tags avec produits
            ->withCount('produits')  // ✅ COUNT réel
            ->limit(2)
            ->get()
            ->map(fn($t) => [
                'title' => $t->name,
                'type' => 'Tag',
                'url' => route('products', ['tag' => $t->slug]),
                'count' => $t->produits_count  // ✅ VRAI count
            ]);

        // 5. RECHERCHE GÉNÉRALE
        $search = [
            [
                'title' => $this->search,
                'type' => 'Tous les résultats',
                'url' => '/produits?q=' . urlencode($this->search),
                'query' => $this->search
            ]
        ];

        $this->suggestions = collect($products)
            ->merge($categories)
            ->merge($brands)
            ->merge($tags)
            ->merge($search)
            ->take(8)
            ->values()
            ->toArray();
    }

    public function search()
    {
        return redirect()->to('/produits?q=' . urlencode($this->search));
    }

    public function clear()
    {
        $this->search = '';
        $this->suggestions = [];
    }

    public function render()
    {
        return view('livewire.search-ecommerce');
    }
}
