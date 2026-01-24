<?php

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Brand;
use Livewire\Component;
use App\Models\Categories;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Produits as Products;

class Produits extends Component
{
    use WithPagination;  // ← CETTE LIGNE DOIT ÊTRE APRÈS le use

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



    public function mount($category = null, $search = null, $tag = null)
    {
        // ✅ 1. Catégorie depuis URL
        if (request()->filled('category')) {
            $categorySlug = request('category');

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

        // Applique recherche dans filtres si pas de category
        if ($this->search && !$category) {
            $this->filters['search'] = $this->search;
        }
        // ✅ Recherche
        if ($search) {
            $this->filters['search'] = $search;
        }

        // 🔥 Tag
        if ($tag) {
            $this->filters['tag'] = $tag;
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
        // Logique toggle (comme avant)
        if (in_array($produitId, $this->wishlistItems)) {
            auth()->user()->wishlistProducts()->detach($produitId);
            $this->wishlistItems = array_diff($this->wishlistItems, [$produitId]);
        } else {
            auth()->user()->wishlistProducts()->attach($produitId);
            $this->wishlistItems[] = $produitId;
        }
    }

    // ✅ SUPPRIMEZ COMPLETEMENT CETTE LIGNE
    // protected $queryString = ['filters'];

    public function updatedFilters()
    {
        $this->cleanEmptyFilters();  // ← NOUVEAU
        $this->resetPage();
    }

    public function clearFilters()
    {
        // Reset TOUS les filtres à leurs valeurs par défaut
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
        ];

        // Reset pagination
        $this->resetPage();

        // Émettre un événement pour feedback visuel (optionnel)
        $this->dispatch('filters-cleared');
        return redirect()->route('products');
    }


    /** 🚀 MAGIC : Nettoie TOUS les filtres vides */
    private function cleanEmptyFilters()
    {
        // Reset les valeurs par défaut
        $defaultFilters = [
            'category_ids' => [],
            'availability' => ['in_stock' => false, 'pre_book' => false, 'out_of_stock' => false],
            'couleur' => [],
            'brands' => [],
            'displayType' => [],
            'condition' => [],
            'delivery' => [],
            'campaign' => [],
            'warranty' => [],
            'warrantyType' => [],
            'certification' => [],
            'min_price' => '',
            'max_price' => '',
            'rating' => '',
            'search' => '',
            'tag' => [],
        ];

        // Seulement garder les valeurs MODIFIÉES (non par défaut)
        foreach ($this->filters as $key => $value) {
            if ($value === $defaultFilters[$key]) {
                $this->filters[$key] = $defaultFilters[$key];
            }
        }
    }

    public function render()
    {
        // ✅ VOTRE CODE EST CORRECT - Produits::with() marche parfaitement
        $query = Products::with(['brand', 'category', 'photos', 'caracteristiques'])
            ->where('is_active', true);

        $this->applyFilters($query);
        $products = $query->paginate(12);

        return view('livewire.produits', [
            'products' => $query->paginate(12),
            'brands_list' => $this->brands // ← Passer aux vues
        ]);
    }

    private function applyFilters($query)
    {
        // ✅ SÉCURISÉ : isset() avant utilisation
        if (isset($this->filters['category_ids']) && !empty($this->filters['category_ids'])) {
            $query->whereIn('category_id', $this->filters['category_ids']);
        }
        // ✅ CORRECTION 1: Cast en float pour price
        if ($this->filters['availability']['in_stock']) {
            $query->where('stock', '>', 0);
        }
        if ($this->filters['availability']['out_of_stock']) {
            $query->where('stock', 0);
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
            $query->where('sale_price', '>=', (float)$this->filters['min_price']);
        }
        if (!empty($this->filters['max_price']) && is_numeric($this->filters['max_price'])) {
            $query->where('sale_price', '<=', (float)$this->filters['max_price']);
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

    // ✅ Updated pour recherche
    public function updatedSearch()
    {
        $this->filters['search'] = $this->search;
        $this->updatedFilters();
    }
}
