<?php

namespace App\Livewire;

use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Models\Categories;
use Illuminate\Support\Facades\Log;
use App\Models\Produits as Products;
use App\Notifications\ProductFavoriNotification;
use Illuminate\Container\Attributes\Auth;

class Produits extends Component
{

    public array $filters = [
        'category_ids' => [],  // ✅ OBLIGATOIRE
        'availability' => ['in_stock' => false, 'pre_book' => false, 'out_of_stock' => false],
        'couleur' => [],
        'brands' => [],
        'min_price' => '',
        'max_price' => '',
        'rating' => '',
        'displayType' => [],
        'condition' => [],
        'delivery' => [],
        'campaign' => [],
        'warranty' => [],
        'warrantyType' => [],
        'certification' => [],
        'search' => '',   // ✅ Ajouté
        'tag' => '',      // 🔥 NOUVEAU
    ];

    public $brands = []; // Liste des marques dynamiques

    public $wishlistItems = []; // Liste des produits en wishlist

    public $searchSuggestions = [];

    // ✅ AJOUTE ÇA pour la recherche
    public $search = '';

    public $tag = [];

    /** Nombre de produits affichés (chargement progressif au scroll). */
    public int $perPage = 12;

    /** Tri du catalogue : populaire | prix-asc | prix-desc | note. */
    public string $sort = 'populaire';

    /** Filtres rapides du nouveau template. */
    public bool $promoOnly = false;
    public bool $favOnly = false;

    /** Charge la tranche suivante de produits (déclenché au scroll). */
    public function loadMore(): void
    {
        $this->perPage += 12;
    }



    public function mount($category = null, $search = null, $tag = null)
    {
        // ✅ 1. Catégorie depuis param Livewire OU query string (?category=slug)
        $categorySlug = $category ?? request('category');

        if ($categorySlug) {
            $categoryModel = Categories::where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();

            // ✅ 404 si catégorie inexistante !
            if (!$categoryModel) {
                abort(404, 'Catégorie non trouvée');
            }

            $this->filters['category_ids'] = $categoryModel->pluckAllCategoryIds()->toArray();
        }
        if (request()->has('brands')) {
            $brandIds = is_array(request('brands')) ? request('brands') : [request('brands')];
            // ✅ EXACTEMENT comme demandé : [3] → ["name"]
            $brandNames = Brand::whereIn('id', $brandIds)
                ->pluck('name')
                ->toArray();

            $this->filters['brands'] = $brandNames; // ["Dell"]
        }

        // ✅ Brands avec ID et slug pour URL
        $this->brands = Brand::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($brand) {
                return [$brand->id => $brand->name];
            })
            ->toArray();
        // ✅ Recherche depuis navbar
        $this->search = $search ?? request('q', '');

        // Applique recherche dans filtres (même combinée à une catégorie)
        if ($this->search) {
            $this->filters['search'] = $this->search;
        }

        // 🔥 Tag
        if ($tag) {
            $this->filters['tag'] = $tag;
        }

        // ✅ Promos depuis ?promo=1
        if (request()->filled('promo')) {
            $this->promoOnly = true;
        }

        $this->loadWishlist();
    }

    private function loadWishlist()
    {
        if (auth()->check()) {
            $this->wishlistItems = auth()->user()
                ->wishlistProducts()
                ->pluck('produits_id')
                ->toArray();
        }
    }

    public function toggleWishlist($produitId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Products::findOrFail($produitId); // ✅ Par ID
        // Logique toggle (comme avant)
        if (in_array($produitId, $this->wishlistItems)) {
            auth()->user()->wishlistProducts()->detach($produitId);
            $this->wishlistItems = array_diff($this->wishlistItems, [$produitId]);
        } else {
            auth()->user()->wishlistProducts()->attach($produitId);
            $this->wishlistItems[] = $produitId;
            $user = Auth()->user();

            $user->notify(new ProductFavoriNotification(
                auth()->user(),  // Qui ajoute
                $product         // Produit
            ));
        }
    }

    // ✅ SUPPRIMEZ COMPLETEMENT CETTE LIGNE
    // protected $queryString = ['filters'];

    public function updatedFilters($value = null, $key = null)
    {
        $this->perPage = 12;          // on repart du début à chaque changement de filtre
    }

    public function updatedSort()
    {
        $this->perPage = 12;
    }

    public function updatedPromoOnly()
    {
        $this->perPage = 12;
    }

    public function updatedFavOnly()
    {
        $this->perPage = 12;
    }

    public function clearFilters()
    {
        // Reset TOUS les filtres à leurs valeurs par défaut
        $this->reset(['search', 'sort', 'promoOnly', 'favOnly', 'perPage']);
        $this->search = '';
        $this->sort = 'populaire';
        $this->promoOnly = false;
        $this->favOnly = false;
        $this->perPage = 12;
        $this->filters = [
            'category_ids' => [],
            'availability' => ['in_stock' => false, 'pre_book' => false, 'out_of_stock' => false],
            'couleur' => [],
            'brands' => [],
            'min_price' => '',
            'max_price' => '',
            'rating' => '',
            'displayType' => [],
            'condition' => [],
            'delivery' => [],
            'campaign' => [],
            'warranty' => [],
            'warrantyType' => [],
            'certification' => [],
            'search' => '',
            'tag' => '',
        ];

        // Émettre un événement pour feedback visuel (optionnel)
        $this->dispatch('filters-cleared');
    }

    public function render()
    {
        // ✅ VOTRE CODE EST CORRECT - Produits::with() marche parfaitement
        $query = Products::with(['brand', 'category', 'photos', 'caracteristiques'])
            ->where('is_active', true);

        $this->applyFilters($query);

        // Filtres rapides du template : promotion + favoris.
        if ($this->promoOnly) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }
        if ($this->favOnly && auth()->check()) {
            $ids = auth()->user()->wishlistProducts()->pluck('produits_id')->toArray();
            $query->whereIn('id', $ids ?: [0]);
        }

        // Tri du catalogue.
        match ($this->sort) {
            'prix-asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'prix-desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'note' => $query->withAvg('avisClients as avg_note', 'nb_etoiles')->orderByDesc('avg_note'),
            default => $query->latest('id'),
        };

        // Chargement progressif : on ne charge que `perPage` produits, et on
        // indique s'il en reste (pour déclencher le chargement au scroll).
        $total = (clone $query)->count();
        $products = $query->take($this->perPage)->get();

        return view('livewire.produits', [
            'products' => $products,
            'brands_list' => $this->brands,
            'hasMore' => $total > $this->perPage,
        ]);
    }

    private function applyFilters($query)
    {
        // ✅ SÉCURISÉ : isset() avant utilisation
        if (isset($this->filters['category_ids']) && !empty($this->filters['category_ids'])) {
            $query->whereIn('category_id', $this->filters['category_ids']);
        }
        // ✅ Dispo : si les 2 cases sont cochées on ne filtre pas (sinon WHERE contradictoire => 0 résultat)
        $inStock = $this->filters['availability']['in_stock'] ?? false;
        $outOfStock = $this->filters['availability']['out_of_stock'] ?? false;
        if ($inStock && !$outOfStock) {
            $query->where('stock', '>', 0);
        } elseif ($outOfStock && !$inStock) {
            $query->where('stock', '<=', 0);
        }

        if (!empty($this->filters['brands'])) {
            $query->whereHas('brand', function ($q) {
                $q->whereIn('name', $this->filters['brands']);
            });
        }

        // ✅ CORRECTION 2: Gestion des inputs vides
        $caracFilters = ['couleur', 'displayType', 'condition', 'delivery', 'campaign', 'warranty', 'warrantyType', 'certification'];
        foreach ($caracFilters as $filter) {
            if (!empty($this->filters[$filter])) {
                $query->whereHas('caracteristiques', function ($q) use ($filter) {
                    $q->where('type', 'LIKE', "%{$filter}%")
                        ->whereIn('value', $this->filters[$filter]);
                });
            }
        }

        if (!empty($this->filters['min_price']) && is_numeric($this->filters['min_price'])) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [(float) $this->filters['min_price']]);
        }
        if (!empty($this->filters['max_price']) && is_numeric($this->filters['max_price'])) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [(float) $this->filters['max_price']]);
        }
        // Rating - Filtrer par note minimum
        if ($this->filters['rating']) {
            $query->whereHas('avisClients', function ($q) {
                $q->where('nb_etoiles', '=', $this->filters['rating'])
                    ->groupBy('produits_id')
                    ->havingRaw('AVG(nb_etoiles) >= ?', [$this->filters['rating']]);
            });
        }

        // ✅ Recherche texte
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $query->where(function ($sub) {
                $search = $this->filters['search'];
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }
        // 🔥 ✅ TAGS (NOUVEAU)
        if (isset($this->filters['tag']) && !empty($this->filters['tag'])) {
            $query->whereHas('tags', function ($q) {
                $q->where('slug', 'LIKE', "%{$this->filters['tag']}%");
            });
        }
    }

    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    // ✅ Updated pour recherche
    public function updatedSearch()
    {
        $this->filters['search'] = $this->search;
        $this->updatedFilters();
    }
}
