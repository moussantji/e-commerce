<?php

namespace App\Livewire;

use App\Models\Paniers;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use App\Models\Produits as Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProduitDetail extends Component
{
    public Product $product;

    public array $images = [];
    public $couleurs;


    public int $quantity = 1;

    public float $ratingAvg = 0.0;
    public int $ratingCount = 0;

    public array $similarProducts = [];
    public array $bundleItems = [];

    #[Url(except: null)]
    public ?string $slug = null;

    public function mount(?Product $product = null, ?string $slug = null): void
    {
        // Vérifie si le produit existe vraiment en DB
        if (!$product || !$product->exists) {
            if ($slug) {
                $this->product = Product::with(['photos', 'avisClients', 'caracteristiques'])
                    ->where('slug', $slug)->firstOrFail();
                $this->couleurs = $this->product->caracteristiques
                    ->where('type', 'couleur');
            } else {
                $this->product = Product::with(['photos', 'avisClients', 'caracteristiques'])
                    ->latest('id')->firstOrFail();
                $this->couleurs = $this->product->caracteristiques
                    ->where('type', 'couleur');
            }
        } else {
            $this->loadProduct($product);
        }

        $this->setRatings();
        $this->hydrateImages();
        $this->loadSimilarAndBundles();
    }

    private function loadProduct($product)
    {
        // Produit valide → reload complet avec relations
        $this->product = Product::with(['photos', 'avisClients', 'caracteristiques'])
            ->findOrFail($product->id);
        $this->couleurs = $this->product->caracteristiques
            ->where('type', 'couleur');
    }
    private function setRatings(): void
    {
        if (!method_exists($this->product, 'avisClients')) {
            $this->ratingAvg = 0;
            $this->ratingCount = 0;
            return;
        }

        // Cache les ratings pour 1h
        $cacheKey = "product_{$this->product->id}_ratings";
        $ratings = Cache::remember($cacheKey, 3600, function () {
            return [
                'avg' => (float) $this->product->avisClients()->avg('nb_etoiles'),
                'count' => (int) $this->product->avisClients()->count()
            ];
        });

        $this->ratingAvg = $ratings['avg'] ?? 0;
        $this->ratingCount = $ratings['count'] ?? 0;
    }

    public function hydrateImages(): void
    {
        $this->images = [];

        // Product images from photos relation or images array / single image column
        $productImages = collect();
        if ($this->product->relationLoaded('photos')) {
            $productImages = $this->product->photos;
        } elseif (method_exists($this->product, 'photos')) {
            $productImages = $this->product->photos()->get();
        } elseif (!empty($this->product->images)) {
            $productImages = collect($this->product->images);
        }

        // Utilise getImageUrl(350,350) dans le foreach comme demandé
        foreach ($productImages as $img) {
            $this->images[] = $img->getImageUrl(350, 350);
        }
        // Image unique en fallback
        if (!empty($this->product->image) && !in_array($this->imageUrl($this->product->image), $this->images)) {
            $this->images[] = $this->imageUrl($this->product->image);
        }

        // Ensure at least one placeholder and deduplicate
        if (empty($this->images)) {
            $this->images[] = asset('assets/img/products/1.png');
        }
        $this->images = array_values(array_unique(array_filter($this->images)));
    }


    protected function imageUrl(?string $path): string
    {
        if (!$path) return asset('assets/img/products/1.png');
        if (str_starts_with($path, 'http')) return $path;
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }
        return asset(trim($path, '/'));
    }


    public function incrementQuantity($id): void
    {
        $this->quantity++;
        $this->loadProduct($this->product);
        // ✅ RÉEXÉCUTE Phoenix après rerendu
        $this->dispatch('product-details-reinit');
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart($productId, $quantity): void  // ← SUPPRIMEZ $quantity
    {
        $product = Product::findOrFail($productId);
        $prixUnitaire = $product->price; // ou votre champ prix

        // 1. Récupère / crée le panier
        // 2. Récupère / crée le panier actif
        $panier = Paniers::firstOrCreate(
            [
                'status' => 'actif',
                'user_id' => Auth::id()
            ],
            [
                'status' => 'actif',
                'user_id' => Auth::id(),
            ]
        );

        // ✅ Récupère le pivot directement depuis la relation chargée
        $pivotExistant = $panier->products()
            ->where('produits_id', $productId)
            ->first()
            ?->pivot;

        if ($pivotExistant) {
            // MODIF DIRECTE sur l'objet Pivot (en mémoire)
            $pivotExistant->quantite += $quantity;
            $pivotExistant->total_ligne += ($quantity * $prixUnitaire);

            // ⚠️ IMPORTANT: syncChanges() pour persister en DB
            $pivotExistant->save();
        } else {
            // Nouveau produit
            $panier->products()->attach($productId, [
                'quantite' => $quantity,
                'prix_unitaire' => $prixUnitaire,
                'total_ligne' => $quantity * $prixUnitaire,
            ]);
        }
        session()->flash('message', "✅ {$quantity}x {$product->nom} ajouté !");
        redirect()->route('panier');
    }


    public function toggleWishlist(): void
    {
        $this->dispatch('wishlist:toggle', productId: $this->product->id);
        session()->flash('wishlist_message', 'Liste de souhaits mise à jour');
    }

    public function getPrice(): array
    {
        $price = (float) ($this->product->sale_price ?? $this->product->price ?? 0);
        $original = (float) ($this->product->price ?? $price);
        return [$price, $original];
    }

    public function getInStockProperty(): bool
    {
        return (int)($this->product->stock ?? 0) > 0;
    }

    public function loadSimilarAndBundles(): void
    {
        $query = Product::query()->where('id', '!=', $this->product->id);
        if (isset($this->product->brand_id)) {
            $query->where('brand_id', $this->product->brand_id);
        }
        $similar = $query->latest('id')->limit(10)->get();
        $this->similarProducts = $similar->map(function ($p) {
            $firstPhoto = method_exists($p, 'photos') ? optional($p->photos()->first())->filename : null;
            $img = $this->imageUrl($firstPhoto ?? $p->image ?? null);
            return [
                'id' => $p->id,
                'name' => $p->name ?? 'Produit',
                'price' => (float)($p->sale_price ?? $p->price ?? 0),
                'original' => (float)($p->price ?? 0),
                'img' => $img,
                'slug' => $p->slug ?? $p->id,
            ];
        })->all();

        $this->bundleItems = array_slice($this->similarProducts, 0, 3);
    }

    public function render()
    {
        [$price, $original] = $this->getPrice();
        $inStock = $this->getInStockProperty();
        return view('livewire.produit-detail', [
            'price' => $price,
            'original' => $original,
            'inStock' => $inStock,
        ]);
    }
}
