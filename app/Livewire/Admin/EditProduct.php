<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\Caracteristiques;
use App\Models\Categories as Category;
use App\Models\photos;
use App\Models\Produits;
use App\Models\Tag;
use App\Models\User;
use App\Support\ImageUploads;
use DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditProduct extends Component
{
    use WithFileUploads;

    public $produit_id;

    public $produit;

    // Mêmes propriétés que AddProduct
    public $name;

    public $description;

    public $sku;

    public $regular_price;

    public $sale_price;

    public $images = [];

    public $old_images;

    // Chemins des images déjà téléversées en JSON/base64 (sans multipart).
    public $uploaded_images = [];

    public $stock_quantity;

    public $stock_actuel;

    public $stock_transit = 0;

    public $stock_total = 0;

    public $last_restock;

    public $produit_created = true;

    public $shipping_type = 'phoenix';

    public $fragile = false;

    public $biodegradable = false;

    public $frozen = false;

    public $frozen_temp = '';

    public $category_id;

    public $brand_id;

    public $vendeur_id;

    public $is_active = true;

    public $is_featured = false;

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
            'uploaded_images' => 'nullable|array|max:10',
            'uploaded_images.*' => 'string|max:500',
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
                    'values' => $values,
                ];
            });

        // ✅ Convertit en array simple
        $this->variant_options = $grouped->values()->toArray();

        // ✅ Si vide, ajoute 1 option par défaut
        if (empty($this->variant_options)) {
            $this->variant_options = [[
                'type' => '',
                'values_input' => '',
                'values' => [],
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
            'values' => [],
        ];
    }

    public function deletePhoto($photoId)
    {
        // Supprime de DB
        photos::find($photoId)?->delete();

        // La première photo restante devient l'image principale.
        ImageUploads::syncPrimary($this->produit);

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

    /**
     * Reçoit les chemins renvoyés par le téléversement JSON et les garde en
     * attente jusqu'à l'enregistrement du produit.
     */
    public function addUploadedImages(array $paths): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $valid = [];
        foreach ($paths as $path) {
            if (
                is_string($path)
                && ImageUploads::ownedBy($user, $path)
                && ! ImageUploads::isAttached($path)
            ) {
                $valid[] = $path;
            }
        }

        // La galerie existante compte dans la limite de 10 photos.
        $existing = $this->produit?->photos()->count() ?? 0;
        $capacity = max(
            0,
            ImageUploads::MAX_PRODUCT_IMAGES - $existing - count($this->uploaded_images),
        );
        $this->uploaded_images = array_values(array_unique(array_merge(
            $this->uploaded_images,
            array_slice($valid, 0, $capacity),
        )));
    }

    public function removeUploadedImage(int $index): void
    {
        $path = $this->uploaded_images[$index] ?? null;
        if (! $path) {
            return;
        }

        unset($this->uploaded_images[$index]);
        $this->uploaded_images = array_values($this->uploaded_images);

        if ($user = auth()->user()) {
            ImageUploads::deletePending($user, $path);
        }
    }

    public function addStock()
    {
        $this->stock_actuel += $this->stock_quantity;
        $this->stock_total += $this->stock_quantity;
        $this->last_restock = now();
    }

    public function update()
    {
        // Normalise les selects vides ("", 0) en null AVANT validation + update.
        $this->category_id = $this->category_id ?: null;
        $this->brand_id = $this->brand_id ?: null;
        $this->vendeur_id = $this->vendeur_id ?: null;
        $this->sale_price = ($this->sale_price === '' || $this->sale_price === 0 || $this->sale_price === '0') ? null : $this->sale_price;
        $this->sku = $this->sku ?: null;

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
            'category_id' => $this->category_id ?: null,
            'brand_id' => $this->brand_id ?: null,
            'vendeur_id' => $this->vendeur_id ?: null,
        ];

        // ✅ ATTACHE les images + ANTI-DOUBLONS
        if ($this->images) {
            $this->produit->attachFiles($this->images);

            // ✅ 1. VIDE les images temporaires
            $this->images = [];
        }
        if ($this->uploaded_images && ($user = auth()->user())) {
            ImageUploads::attachToProduct($this->produit, $this->uploaded_images, $user);
            $this->uploaded_images = [];
        }

        // Recharge les photos DB après les éventuels ajouts.
        $this->old_images = $this->produit->fresh()->photos;

        $this->produit->update($data);

        // Tags (sélection multiple du formulaire) — sync même vide pour retirer.
        $this->produit->tags()->sync($this->selected_tags ?? []);

        // ✅ SUPPRIME anciennes caractéristiques
        $this->produit->caracteristiques()->detach();

        // ✅ Ajoute nouvelles
        foreach ($this->variant_options as $option) {
            if (! empty($option['type']) && ! empty($option['values'])) {
                $caracteristique = Caracteristiques::firstOrCreate(
                    ['type' => $option['type']],
                    ['name' => ucfirst($option['type']), 'is_filterable' => true]
                );

                foreach ($option['values'] as $value) {
                    $this->produit->caracteristiques()->attach($caracteristique->id, [
                        'value' => trim($value),
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
            'vendeurs' => User::where('role', 'vendeur')->orderBy('name')->get(),
            'all_tags' => Tag::orderBy('name')->get(),  // ✅ Objets pour vue
            'caracteristiques_list' => Caracteristiques::orderBy('name')->get(),
        ]);
    }
}
