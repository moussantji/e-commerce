<?php

namespace App\Livewire\Admin;

use DB;
use stdClass;
use App\Models\Tag;
use App\Models\Brand;
use App\Models\photos;
use Livewire\Component;
use App\Models\Produits;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Models\Caracteristiques;
use App\Models\Categories as Category;

class EditProduct extends Component
{
    use WithFileUploads;

    public $produit_id;
    public $produit;

    // Mêmes propriétés que AddProduct
    public $name, $description, $sku, $regular_price, $sale_price;
    public $images = [], $old_images;
    public $stock_quantity, $stock_actuel, $stock_transit = 0, $stock_total = 0;
    public $last_restock, $produit_created = true;
    public $shipping_type = 'phoenix';
    public $fragile = false, $biodegradable = false, $frozen = false, $frozen_temp = '';
    public $category_id, $brand_id, $vendeur_id, $is_active = true, $is_featured = false;
    public $selected_tags = [];
    public $variant_options = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regular_price' => 'required|numeric|min:100',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('produits', 'sku')->ignore($this->produit_id), // ✅ dynamique
            ],
            'stock_actuel' => 'nullable|integer|min:0',
            'selected_tags' => 'nullable|array',
            'selected_tags.*' => 'exists:tags,id',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
        'vendeur_id' => 'nullable|exists:users,id',
            'images.*' => 'nullable|image|max:2048',
            'variant_options.*.type' => 'nullable|string',
            'variant_options.*.values_input' => 'nullable|string',
        ];
    }

    public function mount($produit_id)
    {
        $this->produit_id = $produit_id;
        $this->loadProduct();
        $this->addVariantOption();


        // ✅ Charge les CARACTÉRISTIQUES existantes
        $this->loadCaracteristiques();
    }


    public function loadProduct()
    {
        $this->produit = Produits::with(['tags', 'photos'])->findOrFail($this->produit_id);

        $this->name = $this->produit->name;
        $this->description = $this->produit->description;
        $this->sku = $this->produit->sku;
        $this->regular_price = $this->produit->price;
        $this->sale_price = $this->produit->sale_price;
        $this->stock_actuel = $this->produit->stock;
        $this->category_id = $this->produit->category_id;
        $this->brand_id = $this->produit->brand_id;
        $this->vendeur_id = $this->produit->vendeur_id;
        $this->is_active = $this->produit->is_active;
        $this->is_featured = $this->produit->is_featured;

        // ✅ Pré-charge TAGS depuis product_tag
        $this->selected_tags = $this->produit->tags->pluck('id')->toArray();

        // ✅ COLLECTION Photos directement (pas toArray() !)
        $this->old_images = $this->produit->photos; // Collection Eloquent
    }

    protected function loadCaracteristiques()
    {
        // ✅ TOUJOURS array, JAMAIS null
        $this->variant_options = [];

        // ✅ Charge avec relation explicite
        $this->produit->load('caracteristiques');

        // ✅ Group par type (plusieurs valeurs possibles par type)
        $grouped = $this->produit->caracteristiques
            ->groupBy('type')
            ->map(function ($caracs) {
                $values = $caracs->pluck('pivot.value')->filter()->toArray();
                return [
                    'type' => $caracs->first()->type,
                    'values_input' => implode(', ', $values),
                    'values' => $values
                ];
            });

        // ✅ Convertit en array simple
        $this->variant_options = $grouped->values()->toArray();

        // ✅ Si vide, ajoute 1 option par défaut
        if (empty($this->variant_options)) {
            $this->variant_options = [[
                'type' => '',
                'values_input' => '',
                'values' => []
            ]];
        }
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
        $this->variant_options[] = [
            'type' => '',
            'values_input' => '',
            'values' => []
        ];
    }

    public function deletePhoto($photoId)
    {
        // Supprime de DB
        Photos::find($photoId)?->delete();

        // Recharge SEULEMENT les photos DB
        $this->old_images = $this->produit->fresh()->photos;
    }



    public function removeVariantOption($index)
    {
        unset($this->variant_options[$index]);
        $this->variant_options = array_values($this->variant_options);
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function addStock()
    {
        $this->stock_actuel += $this->stock_quantity;
        $this->stock_total += $this->stock_quantity;
        $this->last_restock = now();
    }

    public function update()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->regular_price,
            'sale_price' => $this->sale_price ? (int) $this->sale_price : null,
            'stock' => (int) $this->stock_actuel,
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'vendeur_id' => $this->vendeur_id ?: null,
        ];

        // ✅ ATTACHE les images + ANTI-DOUBLONS
        if ($this->images) {
            $this->produit->attachFiles($this->images);

            // ✅ 1. VIDE les images temporaires
            $this->images = [];

            // ✅ 2. Recharge les photos DB
            $this->old_images = $this->produit->fresh()->photos;
        }

        $this->produit->update($data);

        // Tags (sélection multiple du formulaire) — sync même vide pour retirer.
        $this->produit->tags()->sync($this->selected_tags ?? []);

        // ✅ SUPPRIME anciennes caractéristiques
        $this->produit->caracteristiques()->detach();

        // ✅ Ajoute nouvelles
        foreach ($this->variant_options as $option) {
            if (!empty($option['type']) && !empty($option['values'])) {
                $caracteristique = Caracteristiques::firstOrCreate(
                    ['type' => $option['type']],
                    ['name' => ucfirst($option['type']), 'is_filterable' => true]
                );

                foreach ($option['values'] as $value) {
                    $this->produit->caracteristiques()->attach($caracteristique->id, [
                        'value' => trim($value)
                    ]);
                }
            }
        }

        session()->flash('success', 'Produit mis à jour !');
    }

    public function render()
    {

        return view('livewire.admin.edit-product', [
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'vendeurs' => \App\Models\User::where('role', 'vendeur')->orderBy('name')->get(),
            'all_tags' => Tag::orderBy('name')->get(),  // ✅ Objets pour vue
            'caracteristiques_list' => Caracteristiques::orderBy('name')->get(),
        ]);
    }
}
