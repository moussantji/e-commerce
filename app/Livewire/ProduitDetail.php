<?php

namespace App\Livewire;

use App\Models\AvisClient;
use App\Models\Paniers;
use App\Models\Produits as Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProduitDetail extends Component
{
    use WithPagination;
    use WithFileUploads;
    public Product $product;

    public $images = [];
    public $avisimages = [];

    #[Url(except: null)]
    public string $tab = 'description';


    public int $quantity = 1;

    public bool $alwaysActive = true;

    public float $ratingAvg = 0.0;
    public int $ratingCount = 0;

    public $similarProducts;
    public $bundleItems;

    #[Url(except: null)]
    public ?string $slug = null;

    public $rating = 0;         // 0.5, 1, ..., 5 ← valeur JS du rater
    public $commentaire = '';
    public $anonyme = true;

    public $selectedItems = [];
    public $bundleTotal = 0;
    public $selectedItemsCount = 0;
    public $iswishlisted = false;



    protected $rules = [
        'rating'      => 'required|numeric|min:0.5|max:5',
        'commentaire' => 'nullable|string|max:1000',
        'avisimages.*' => 'image|max:20480', // Chaque image max 2MB
    ];

    protected $listeners = [
        'rater::value' => 'raterValue',
        'refreshBundle' => '$refresh',
    ];




    public function mount(?Product $product = null, ?string $slug = null): void
    {
        $this->alwaysActive = true;
        // Vérifie si le produit existe vraiment en DB
        if (!$product || !$product->exists) {
            if ($slug) {
                $this->product = Product::with(['photos', 'avisClients', 'caracteristiques'])
                    ->where('slug', $slug)->firstOrFail();
            } else {
                $this->product = Product::with(['photos', 'avisClients', 'caracteristiques'])
                    ->latest('id')->firstOrFail();
            }
        } else {
            $this->loadProduct($product);
        }

        $this->similarProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->limit(12)
            ->get();
        $this->setRatings();
        $this->hydrateImages();
        $this->loadBundleItems();
        $this->iswishlisted = Auth::check() && auth()->user()->wishlistProducts()->where('produits_id', $this->product->id)->exists();
    }

    private function loadProduct($product)
    {
        // Produit valide → reload complet avec relations
        $this->product = Product::with(['photos', 'avisClients', 'caracteristiques'])
            ->findOrFail($product->id);
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

    public function raterValue($value)
    {
        $this->rating = $value;
    }

    public function paginationView()
    {
        return 'components.pagination.custom-links';
    }

    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
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
            $this->images[] = $img->getImageUrl(600,600);
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

        // ✅ Garde stock : on ne peut pas ajouter plus que le stock disponible
        if ((int) $product->stock < (int) $quantity) {
            session()->flash('error', "Stock insuffisant pour {$product->name} (disponible : {$product->stock}).");
            return;
        }

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

    public function toggleWishlist($productId)
    {
        $user = auth()->user();
        if (!$user) return; // ✅ Sécurité

        $exists = $user->wishlistProducts()->where('produits_id', $productId)->exists();

        $productslug = Product::find($productId)->getSlug(); // ✅ Récupère le slug du produit
        if ($exists) {
            $user->wishlistProducts()->detach($productId); // ✅ SUPPRIME ligne DB
            return redirect()->route('produits.show', ['slug' => $productslug, 'id' => $productId])->with('success', 'Produit retiré de votre liste de souhaits !'); // ✅ Redirige vers la page des favoris après l'action

        } else {
            $user->wishlistProducts()->attach($productId); // ✅ AJOUTE ligne DB
            return redirect()->route('produits.show', ['slug' => $productslug, 'id' => $productId])->with('success', 'Produit ajouté à votre liste de souhaits !'); // ✅ Redirige vers la page des favoris après l'action

        }

        // Livewire refresh automatique → vue mise à jour !

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

    public function loadBundleItems()
    {
        // Logique pour récupérer les produits complémentaires
        $this->bundleItems = Product::where('id', '!=', $this->product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $this->selectedItems = $this->bundleItems->pluck('id')->toArray();
        $this->calculateTotal();
    }

    public function toggleBundleItem($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $selected = collect($this->bundleItems)
            ->whereIn('id', $this->selectedItems);

        $this->bundleTotal = $selected->sum(fn($item) => $item->prix_promo ?? $item->price);

        // ✅ Force la mise à jour du compteur
        $this->selectedItemsCount = $selected->count();
    }

    public function addBundleToCart()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Connectez-vous pour ajouter au panier');
            return redirect()->route('login');
        }

        $userId = Auth::id();

        // 1. Récupère ou crée le PANIER ACTIF de l'utilisateur
        $panier = Paniers::where('user_id', $userId)
            ->where('status', 'actif')
            ->firstOrCreate([
                'user_id' => $userId,
                'status' => 'actif',
            ]);

        // 2. Ajoute chaque produit du bundle dans panier_produit
        foreach ($this->selectedItems as $itemId) {
            $product = $this->bundleItems->firstWhere('id', $itemId);
            if (!$product) continue;

            $prixUnitaire = $product->prix_promo ?? $product->price;

            // Vérifie si le produit existe déjà dans panier_produit
            $panierProduit = DB::table('panier_produit')
                ->where('paniers_id', $panier->id)
                ->where('produits_id', $itemId)
                ->first();

            if ($panierProduit) {
                // INCRÉMENTER la quantité
                DB::table('panier_produit')
                    ->where('paniers_id', $panier->id)
                    ->where('produits_id', $itemId)
                    ->increment('quantite', 1);
            } else {
                // NOUVEAU produit
                DB::table('panier_produit')->insert([
                    'paniers_id' => $panier->id,
                    'produits_id' => $itemId,
                    'quantite' => 1,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => $prixUnitaire,
                ]);
            }
        }

        // 3. Notifications
        $this->dispatch('bundle-added', [
            'count' => count($this->selectedItems),
            'total' => $this->bundleTotal
        ]);

        session()->flash('success', count($this->selectedItems) . ' articles ajoutés au panier !');
        return redirect()->route('panier');
    }

    public function getSelectedItemsCountProperty()
    {
        return count($this->selectedItems);
    }

    public function submitReview()
    {

        $review = AvisClient::create([
            'produits_id' => $this->product->id,
            'user_id'     => Auth::id(),
            'nb_etoiles'  => $this->rating,
            'note'        => $this->rating,
            'commentaire' => $this->commentaire,
        ]);

        // 🔥 INVALIDER LE CACHE IMMÉDIATEMENT
        $cacheKey = "product_{$this->product->id}_ratings";
        Cache::forget($cacheKey);

        // Recharger les ratings
        $this->setRatings();

        // Reset formulaire
        $this->rating = 0;
        $this->commentaire = '';
        $this->avisimages = [];

        // Attaque les images
        $review->attachfiles($this->avisimages);

        // Rediriger vers la même page (URL courante)
        return redirect(route('produits.show', ['slug' => $this->product->getSlug(), 'id' => $this->product->id]))
            ->with('success', 'Merci pour votre avis !');
    }

    public function render()
    {
        [$price, $original] = $this->getPrice();
        $inStock = $this->getInStockProperty();
        // ✅ Paginer les avis de ce produit
        $reviews = $this->product->avisClients()
            ->with(['user', 'response'])
            ->orderBy('created_at', 'desc')
            ->paginate(3);
        $couleurs = $this->product->caracteristiques
            ->where('type', 'couleur');
        return view('livewire.produit-detail', [
            'price' => $price,
            'original' => $original,
            'inStock' => $inStock,
            'reviews' => $reviews,
            'couleurs' => $couleurs,
        ]);
    }
}
