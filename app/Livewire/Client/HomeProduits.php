<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Produits;
use App\Models\Categories;
use App\Models\Brand;
use Illuminate\Support\Str;

class HomeProduits extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $category_id = '';
    public $is_active = '';

    // Formulaire création/édition
    public $editingProduit = null;
    public $name, $description, $price, $sale_price = 0, $stock, $sku;
    public $category_id_edit, $brand_id, $images = [];
    public $is_active_edit = true, $is_featured = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'category_id_edit' => 'required|exists:categories,id',
        'images.*' => 'image|max:2048', // 2MB
    ];

    public function updatedImages()
    {
        $this->validateOnly('images.*');
    }

    public function render()
    {
        $query = Produits::with(['category', 'brand'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->is_active !== '', fn($q) => $q->where('is_active', $this->is_active));

        $produits = $query->latest()->paginate(10);

        return view('livewire.admin.produits', [
            'produits' => $produits,
            'categories' => Categories::pluck('name', 'id'),
            'brands' => Brand::pluck('name', 'id')
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->editingProduit = null;
    }

    public function edit(Produits $produit)
    {
        $this->editingProduit = $produit;
        $this->name = $produit->name;
        $this->description = $produit->description;
        $this->price = $produit->price;
        $this->sale_price = $produit->sale_price ?? 0;
        $this->stock = $produit->stock;
        $this->sku = $produit->sku;
        $this->category_id_edit = $produit->category_id;
        $this->brand_id = $produit->brand_id;
        $this->is_active_edit = $produit->is_active;
        $this->is_featured = $produit->is_featured ?? false;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingProduit) {
            $produit = $this->editingProduit;
            $produit->update([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'sale_price' => $this->sale_price ?: null,
                'stock' => $this->stock,
                'sku' => $this->sku,
                'category_id' => $this->category_id_edit,
                'brand_id' => $this->brand_id,
                'is_active' => $this->is_active_edit,
                'is_featured' => $this->is_featured,
            ]);
        } else {
            $produit = Produits::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'price' => $this->price,
                'sale_price' => $this->sale_price ?: null,
                'stock' => $this->stock,
                'sku' => $this->sku,
                'category_id' => $this->category_id_edit,
                'brand_id' => $this->brand_id,
                'is_active' => $this->is_active_edit,
                'is_featured' => $this->is_featured,
            ]);
        }

        // Upload images
        if ($this->images) {
            $produit->attachfiles($this->images);
        }

        session()->flash('message', $this->editingProduit ? 'Produit mis à jour !' : 'Produit créé !');
        $this->resetForm();
    }

    public function delete(Produits $produit)
    {
        $produit->delete();
        session()->flash('message', 'Produit supprimé !');
    }

    public function toggleActive(Produits $produit)
    {
        $produit->update(['is_active' => !$produit->is_active]);
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->sale_price = 0;
        $this->stock = 0;
        $this->sku = '';
        $this->category_id_edit = '';
        $this->brand_id = '';
        $this->images = [];
        $this->is_active_edit = true;
        $this->is_featured = false;
    }
}
