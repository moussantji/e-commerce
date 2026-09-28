<?php

namespace App\Livewire;

use App\Models\AvisClient;
use App\Models\Paniers;
use App\Models\Produits as Product;
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
    use WithPagination, WithFileUploads;
    public Product $product;

    public $images = [];

    #[Url(except: null)]
    public string $tab = 'desc';

    public $prevProduct = null;
    public $nextProduct = null;
    public int $vendus = 0;


    public bool $alwaysActive = true;

    public float $ratingAvg = 0.0;
    public int $ratingCount = 0;

    public $similarProducts;

    #[Url(except: null)]
    public ?string $slug = null;

    public $iswishlisted = false;

    public int $rating = 5;
    public string $commentaire = '';
    public array $avisPhotos = [];

    /** Options choisies sur la fiche (clé = type normalisé, valeur = valeur choisie). */
    public array $selectedOptions = [];

    /** Quantité choisie sur la fiche. */
    public int $quantity = 1;




    public function mount(?Product $product = null, ?string $slug = null): void
    {
        $this->alwaysActive = true;
        // Vérifie si le produit existe vraiment en DB
        if (!$product || !$product->exists) {
            if ($slug) {
                $this->product = Product::with(['photos', 'avisClients', 'caracteristiques', 'variants.options'])
                    ->where('slug', $slug)->firstOrFail();
            } else {
                $this->product = Product::with(['photos', 'avisClients', 'caracteristiques', 'variants.options'])
                    ->latest('id')->firstOrFail();
            }
        } else {
            $this->loadProduct($product);
        }

        $this->similarProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('is_active', true)
            ->limit(12)
            ->get();
        if ($this->similarProducts->count() < 4) {
            $need = 4 - $this->similarProducts->count();
            $others = Product::where('id', '!=', $this->product->id)
                ->whereNotIn('id', $this->similarProducts->pluck('id')->push($this->product->id)->all())
                ->where('is_active', true)
                ->orderBy('id', 'desc')
                ->limit($need)
                ->get();
            $this->similarProducts = $this->similarProducts->concat($others);
        }
        $this->setRatings();
        $this->hydrateImages();
        $this->loadSiblings();
        $this->initSelectedOptions();
        $this->vendus = (int) DB::table('commande_produit')->where('produit_id', $this->product->id)->sum('quantite');
        $this->iswishlisted = Auth::check() && auth()->user()->wishlistProducts()->where('produits_id', $this->product->id)->exists();
    }

    private function loadSiblings(): void
    {
        $group = Product::where('category_id', $this->product->category_id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name']);
        if ($group->count() < 2) {
            $group = Product::where('is_active', true)->orderBy('id')->get(['id', 'name']);
        }
        if ($group->count() < 2) {
            return;
        }
        $ids = $group->pluck('id')->all();
        $pos = array_search($this->product->id, $ids, true);
        if ($pos === false) {
            return;
        }
        $prev = $group[($pos - 1 + count($ids)) % count($ids)];
        $next = $group[($pos + 1) % count($ids)];
        $this->prevProduct = ['id' => $prev->id, 'name' => $prev->name, 'slug' => $prev->getSlug(), 'pos' => $pos + 1, 'total' => count($ids)];
        $this->nextProduct = ['id' => $next->id, 'name' => $next->name, 'slug' => $next->getSlug()];
    }

    private function loadProduct($product)
    {
        // Produit valide → reload complet avec relations
        $this->product = Product::with(['photos', 'avisClients', 'caracteristiques', 'variants.options'])
            ->findOrFail($product->id);
    }

    /**
     * Types sélectionnables à l'achat (options qui varient selon le produit :
     * stockage, RAM, taille, couleur...). Le reste (marque, matière, poids...)
     * reste affiché en info uniquement.
     */
    public const SELECTABLE_TYPES = [
        'couleur',
        'couleur_tissu',
        'stockage',
        'rom',
        'memoire',
        'ram',
        'taille',
        'pointure',
        'volume',
        'capacite',
    ];

    /**
     * Groupes d'options sélectionnables : uniquement les types nécessaires
     * selon le produit (ex: stockage/RAM/couleur pour un téléphone,
     * taille/couleur pour un vêtement).
     * Chaque groupe : ['key', 'label', 'type', 'values' => [['value','unite','display'], ...]]
     * Les variantes (product_variants / variant_options) sont fusionnées si présentes.
     */
    public function optionGroups(): array
    {
        $caracs = $this->product->relationLoaded('caracteristiques')
            ? $this->product->caracteristiques
            : $this->product->caracteristiques()->get();

        $groups = [];
        foreach ($caracs ?? [] as $c) {
            $key = mb_strtolower(trim((string) ($c->type ?? $c->name)));
            if ($key === '' || !in_array($key, self::SELECTABLE_TYPES, true)) {
                continue;
            }
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'key' => $key,
                    'label' => $c->name ?? ucfirst($key),
                    'type' => $c->type ?? $key,
                    'values' => [],
                ];
            }
            $val = trim((string) ($c->pivot->value ?? ''));
            if ($val === '') {
                continue;
            }
            $unite = trim((string) ($c->unite ?? ''));
            // Évite "256 Go Go" quand la valeur contient déjà l'unité.
            $display = $val;
            if ($unite !== '' && mb_stripos($val, $unite) === false) {
                $display .= ' ' . $unite;
            }
            $exists = false;
            foreach ($groups[$key]['values'] as $v) {
                if ($v['display'] === $display) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $groups[$key]['values'][] = ['value' => $val, 'unite' => $unite, 'display' => $display];
            }
        }

        // Fusion des variantes éventuelles (même clé de type).
        try {
            $variants = $this->product->relationLoaded('variants')
                ? $this->product->variants
                : $this->product->variants()->with('options')->get();
            foreach ($variants ?? [] as $variant) {
                foreach ($variant->options ?? [] as $opt) {
                    $key = mb_strtolower(trim((string) ($opt->type ?? '')));
                    if ($key === '' || !in_array($key, self::SELECTABLE_TYPES, true)) {
                        continue;
                    }
                    if (!isset($groups[$key])) {
                        $groups[$key] = ['key' => $key, 'label' => ucfirst($opt->type), 'type' => $opt->type, 'values' => []];
                    }
                    $display = trim((string) ($opt->value ?? ''));
                    if ($display === '') {
                        continue;
                    }
                    $exists = false;
                    foreach ($groups[$key]['values'] as $v) {
                        if ($v['display'] === $display) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $groups[$key]['values'][] = ['value' => $display, 'unite' => '', 'display' => $display];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Variantes indisponibles : on garde les caractéristiques simples.
        }

        // Ordre stable : couleur d'abord, puis stockage/ram/taille, puis le reste.
        $order = ['couleur' => 0, 'couleur_tissu' => 1, 'stockage' => 2, 'rom' => 3, 'memoire' => 4, 'ram' => 5, 'taille' => 6, 'pointure' => 7, 'volume' => 8, 'capacite' => 9];
        uksort($groups, function ($a, $b) use ($order) {
            return ($order[$a] ?? 10) <=> ($order[$b] ?? 10);
        });

        return array_values($groups);
    }

    private function initSelectedOptions(): void
    {
        $this->selectedOptions = [];
        foreach ($this->optionGroups() as $g) {
            if (!empty($g['values'])) {
                $this->selectedOptions[$g['key']] = $g['values'][0]['display'];
            }
        }
        if ($this->quantity < 1) {
            $this->quantity = 1;
        }
    }

    public function selectOption(string $type, string $value): void
    {
        $key = mb_strtolower(trim($type));
        foreach ($this->optionGroups() as $g) {
            if ($g['key'] === $key) {
                foreach ($g['values'] as $v) {
                    if ($v['display'] === $value || $v['value'] === $value) {
                        $this->selectedOptions[$key] = $v['display'];

                        return;
                    }
                }
            }
        }
    }

    private function maxQuantity(): int
    {
        return max(1, (int) ($this->product->stock ?? 1));
    }

    public function incrementQty(): void
    {
        $this->quantity = min($this->quantity + 1, $this->maxQuantity());
    }

    public function decrementQty(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function updatedQuantity($value): void
    {
        $this->quantity = min(max(1, (int) $value), $this->maxQuantity());
    }

    /**
     * Lien WhatsApp avec options + quantité choisies (bouton fiche produit).
     */
    public function whatsappOrderUrl(): string
    {
        return \App\Support\WhatsApp::orderUrl(
            $this->product,
            $this->selectedOptionsLabels(),
            max(1, (int) $this->quantity),
            $this->product->getPublicUrl()
        );
    }

    /**
     * Options avec libellés lisibles pour le panier / WhatsApp / récap.
     * ['Stockage' => '256 Go', ...]
     */
    public function selectedOptionsLabels(): array
    {
        $labels = [];
        foreach ($this->optionGroups() as $g) {
            $key = $g['key'];
            if (isset($this->selectedOptions[$key])) {
                $labels[$g['label']] = $this->selectedOptions[$key];
            }
        }

        return $labels;
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


    public function addToCart($productId, $quantity = null): void
    {
        $product = Product::findOrFail($productId);
        $quantity = max(1, (int) ($quantity ?? $this->quantity ?? 1));
        // Prix effectif : soldé si promo, sinon prix normal.
        $prixUnitaire = ($product->sale_price && (float) $product->sale_price < (float) $product->price)
            ? (float) $product->sale_price
            : (float) $product->price;

        // ✅ Garde stock : on ne peut pas ajouter plus que le stock disponible
        if ((int) $product->stock < $quantity) {
            session()->flash('error', "Stock insuffisant pour {$product->name} (disponible : {$product->stock}).");
            return;
        }

        // Options uniquement pour la fiche principale (pas les similaires).
        $options = ((int) $productId === (int) $this->product->id)
            ? $this->selectedOptionsLabels()
            : [];
        $optionsJson = !empty($options) ? json_encode($options, JSON_UNESCAPED_UNICODE) : null;

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
            if ($optionsJson) {
                $pivotExistant->options = $optionsJson;
            }

            // ⚠️ IMPORTANT: syncChanges() pour persister en DB
            $pivotExistant->save();
        } else {
            // Nouveau produit
            $panier->products()->attach($productId, [
                'quantite' => $quantity,
                'prix_unitaire' => $prixUnitaire,
                'total_ligne' => $quantity * $prixUnitaire,
                'options' => $optionsJson,
            ]);
        }
        $this->quantity = $quantity;
        session()->flash('message', "✅ {$quantity}x {$product->name} ajouté !");
        redirect()->route('panier');
    }

    public function toggleWishlist($productId)
    {
        $user = auth()->user();
        if (!$user) return; // ✅ Sécurité

        $exists = $user->wishlistProducts()->where('produits_id', $productId)->exists();

        if ($exists) {
            $user->wishlistProducts()->detach($productId);
            session()->flash('success', 'Produit retiré de votre liste de souhaits !');
        } else {
            $user->wishlistProducts()->attach($productId);
            session()->flash('success', 'Produit ajouté à votre liste de souhaits !');
        }

        // ✅ Reste sur la page (pas de redirect) — met à jour l'état local
        if ((int) $productId === (int) $this->product->id) {
            $this->iswishlisted = !$exists;
        }
        // Livewire refresh automatique → vue mise à jour !
    }

    public function submitReview(): void
    {
        if (!Auth::check()) {
            redirect()->route('login');
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
            'avisPhotos' => 'nullable|array|max:5',
            'avisPhotos.*' => 'image|max:10240',
        ], [
            'rating.required' => 'Choisissez une note.',
            'commentaire.max' => 'Commentaire trop long (1000 caractères max).',
            'avisPhotos.max' => '5 photos maximum.',
            'avisPhotos.*.image' => 'Chaque fichier doit être une image.',
            'avisPhotos.*.max' => 'Chaque photo doit faire moins de 10 Mo.',
        ]);

        if (trim($this->commentaire) === '' && empty($this->avisPhotos)) {
            $this->addError('commentaire', 'Écrivez un commentaire ou ajoutez une photo.');
            return;
        }

        $review = AvisClient::create([
            'produits_id' => $this->product->id,
            'user_id' => Auth::id(),
            'nb_etoiles' => $this->rating,
            'note' => $this->rating,
            'commentaire' => trim($this->commentaire) !== '' ? trim($this->commentaire) : null,
        ]);

        if (!empty($this->avisPhotos)) {
            $review->attachfiles($this->avisPhotos);
        }

        Cache::forget("product_{$this->product->id}_ratings");
        $this->setRatings();

        $this->reset(['commentaire', 'avisPhotos']);
        $this->rating = 5;
        $this->resetPage();
        $this->tab = 'avis';
        session()->flash('review_ok', 'Merci pour votre avis !');
    }

    public function removeAvisPhoto(int $index): void
    {
        if (isset($this->avisPhotos[$index])) {
            unset($this->avisPhotos[$index]);
            $this->avisPhotos = array_values($this->avisPhotos);
        }
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

    public function render()
    {
        [$price, $original] = $this->getPrice();
        $inStock = $this->getInStockProperty();
        // ✅ S'assure que les caractéristiques sont toujours chargées (même après refresh Livewire)
        $this->product->loadMissing(['caracteristiques', 'photos']);
        $allCaracs = $this->product->caracteristiques ?? collect();
        $couleurs = $allCaracs->filter(fn($c) => mb_strtolower(trim((string) ($c->type ?? ''))) === 'couleur')->values();
        $autresCaracs = $allCaracs->reject(fn($c) => mb_strtolower(trim((string) ($c->type ?? ''))) === 'couleur')->values();
        $optionGroups = $this->optionGroups();
        // Répare une sélection périmée après refresh (ex: produit changé).
        foreach ($optionGroups as $g) {
            if (!isset($this->selectedOptions[$g['key']]) && !empty($g['values'])) {
                $this->selectedOptions[$g['key']] = $g['values'][0]['display'];
            }
        }
        // ✅ Paginer les avis de ce produit
        $reviews = $this->product->avisClients()
            ->with(['user', 'response', 'photos'])
            ->orderBy('created_at', 'desc')
            ->paginate(3);
        return view('livewire.produit-detail', [
            'price' => $price,
            'original' => $original,
            'inStock' => $inStock,
            'reviews' => $reviews,
            'couleurs' => $couleurs,
            'autresCaracs' => $autresCaracs,
            'allCaracs' => $allCaracs,
            'optionGroups' => $optionGroups,
        ]);
    }
}
