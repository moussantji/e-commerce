<?php

namespace App\Livewire\Admin;

use DB;
use App\Models\Tag;
use App\Models\Brand;
use Livewire\Component;
use App\Models\Produits;
use Livewire\WithFileUploads;
use App\Models\Caracteristiques;
use Illuminate\Support\Facades\Log;
use App\Models\Categories as Category;

class AddProduct extends Component
{
    use WithFileUploads;

    // Champs principaux
    public $name, $description, $sku, $category_id, $brand_id;
    public $images = [];
    public $selected_tags = [];
    public $is_active = true, $is_featured = false;

    // Inventaire
    public $regular_price = 0, $sale_price = 0;
    public $stock_quantity = 0, $stock_actuel = 0;
    public $stock_transit = 0, $stock_total = 0;
    public $last_restock = null;
    public $produit_created = false;

    // Livraison & Attributs
    public $shipping_type = 'phoenix';
    public $fragile = false, $biodegradable = false;
    public $frozen = false, $frozen_temp = '';

    // Variants
    public $variant_options = [];

    // 📍 AJOUTE CES PROPRIÉTÉS
    public $showCategoryModal = false, $newCategoryName = '';
    public $showBrandModal = false, $newBrandName = '';
    public $showTagModal = false, $newTagName = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'sku' => 'nullable|string|max:100|unique:produits,sku',
        'category_id' => 'nullable|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'regular_price' => 'required|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0',
        'stock_quantity' => 'nullable|integer|min:0',
        'tags' => 'array',
        'tags.*' => 'exists:tags,id',
        'variant_options.*.values_input' => 'nullable|string',
        'variant_options.*.type' => 'nullable|string',
        'images' => 'nullable|array|max:10',  // max 10 images
        'images.*' => 'image|max:2048',       // 2Mo max par image
    ];

    public function mount()
    {
        // $this->addVariantOption();
        // ✅ 1 SEULE option (select avec TOUTES les caractéristiques)
        $this->variant_options = [[
            'type' => '',
            'values_input' => '',
            'values' => []
        ]];
    }

    public function updatedVariantOptions($value, $key)
    {
        if (str_contains($key, '.values_input')) {
            [$indexStr] = explode('.', $key, 2);
            $index = (int) $indexStr;
            $values = array_filter(array_map('trim', explode(',', $value)));
            $this->variant_options[$index]['values'] = $values;
        }
    }

    public function addVariantOption()
    {
        $this->variant_options[] = [];
    }

    public function removeVariantOption($index)
    {
        unset($this->variant_options[$index]);
        $this->variant_options = array_values($this->variant_options);
    }

    public function removeImage($index)
    {
        $this->images = array_values(array_filter($this->images, fn($img, $key) => $key != $index, ARRAY_FILTER_USE_BOTH));
    }

    public function addStock()
    {
        $this->stock_actuel += $this->stock_quantity;
        $this->stock_total += $this->stock_quantity;
        $this->last_restock = now();
    }

    public function submit()
    {
        $this->validate();

        $produit = Produits::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->regular_price,           // decimal(10,2)
            'sale_price' => $this->sale_price ? $this->sale_price : null,
            'stock' => (int) ($this->stock_quantity ?? 0),
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
        ]);

        // Tags
        if (!empty($this->tags)) {
            $produit->tags()->sync($this->tags);
        }
        // ✅ ATTACHE les images avec ta méthode
        if ($this->images) {
            $produit->attachFiles($this->images);
        }

        // Active inventaire
        $this->produit_created = true;
        $this->stock_actuel = $this->stock_quantity ?? 0;
        foreach ($this->variant_options as $option) {
            if (!empty($option['type']) && !empty($option['values'])) {
                foreach ($option['values'] as $value) {
                    $caracteristique = Caracteristiques::firstOrCreate(
                        ['type' => $option['type'], 'name' => ucfirst($option['type'])],
                        ['unite' => '', 'is_filterable' => true]
                    );

                    // ✅ Utilise les bons noms de colonnes
                    $produit->caracteristiques()->attach($caracteristique->id, [
                        'value' => $value
                    ]);
                }
            }
        }

        session()->flash('success', 'Produit créé avec succès !');
        $this->resetExcept(['produit_created']);
    }

    public function render()
    {
        return view('livewire.admin.add-product', [
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'all_tags' => Tag::orderBy('name')->get(),  // ✅ Objets pour vue
            // ✅ Passe les caractéristiques pour le select
            'caracteristiques_list' => Caracteristiques::orderBy('name')->get(),
        ]);
    }
}
