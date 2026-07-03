<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Caracteristiques;
use App\Models\Categories;
use App\Models\Livraison;
use App\Models\Paiements;
use App\Models\Produits;
use App\Models\PromoCode;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Gestion (CRUD) du catalogue et des paramètres par l'administrateur depuis
 * l'app mobile : produits, catégories, caractéristiques, marques, tags,
 * méthodes de livraison, méthodes de paiement, coupons et utilisateurs.
 *
 * Un seul contrôleur pilote toutes les ressources via une configuration
 * déclarative (voir resources()), pour rester cohérent et facile à étendre.
 */
class AdminCatalogController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless(optional($request->user())->role === 'admin', 403, 'Accès réservé aux administrateurs.');
    }

    /** Récupère la configuration d'une ressource ou renvoie 404. */
    private function resource(string $key): array
    {
        $all = $this->resources();
        abort_unless(isset($all[$key]), 404, 'Ressource inconnue.');

        return $all[$key];
    }

    /** Liste paginée d'une ressource (avec recherche). */
    public function index(Request $request, string $resource)
    {
        $this->ensureAdmin($request);
        $cfg = $this->resource($resource);

        $model = $cfg['model'];
        $query = $model::query();

        if (!empty($cfg['with'])) {
            $query->with($cfg['with']);
        }

        $search = trim((string) $request->query('search', ''));
        if ($search !== '' && !empty($cfg['searchable'])) {
            $query->where(function ($q) use ($cfg, $search) {
                foreach ($cfg['searchable'] as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        [$orderCol, $orderDir] = $cfg['order'] ?? ['id', 'desc'];
        $query->orderBy($orderCol, $orderDir);

        $items = $query->paginate((int) $request->query('per_page', 30));

        return response()->json([
            'data' => $items->getCollection()->map($cfg['map']),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /** Création d'un enregistrement. */
    public function store(Request $request, string $resource)
    {
        $this->ensureAdmin($request);
        $cfg = $this->resource($resource);

        $rules = $cfg['rules']($request, null);
        $data = $request->validate($rules);
        $data = isset($cfg['prepare']) ? $cfg['prepare']($data, null) : $data;

        $model = $cfg['model'];
        $item = $model::create($data);

        return response()->json([
            'message' => 'Créé avec succès.',
            'data' => $cfg['map']($item->fresh($cfg['with'] ?? [])),
        ], 201);
    }

    /** Mise à jour d'un enregistrement. */
    public function update(Request $request, string $resource, $id)
    {
        $this->ensureAdmin($request);
        $cfg = $this->resource($resource);

        $model = $cfg['model'];
        $item = $model::findOrFail($id);

        $rules = $cfg['rules']($request, $item);
        $data = $request->validate($rules);
        $data = isset($cfg['prepare']) ? $cfg['prepare']($data, $item) : $data;

        $item->update($data);

        return response()->json([
            'message' => 'Mis à jour avec succès.',
            'data' => $cfg['map']($item->fresh($cfg['with'] ?? [])),
        ]);
    }

    /** Suppression d'un enregistrement. */
    public function destroy(Request $request, string $resource, $id)
    {
        $this->ensureAdmin($request);
        $cfg = $this->resource($resource);

        $model = $cfg['model'];
        $item = $model::findOrFail($id);

        // Un admin ne peut pas se supprimer lui-même.
        if ($resource === 'users' && (int) $item->id === (int) $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }

        $item->delete();

        return response()->json(['message' => 'Supprimé avec succès.']);
    }

    /**
     * Options pour les listes déroulantes des formulaires admin
     * (catégories parentes, catégories/marques de produit...).
     */
    public function options(Request $request)
    {
        $this->ensureAdmin($request);

        return response()->json([
            'categories' => Categories::orderBy('name')->get(['id', 'name'])
                ->map(fn ($c) => ['value' => $c->id, 'label' => $c->name]),
            'brands' => Brand::orderBy('name')->get(['id', 'name'])
                ->map(fn ($b) => ['value' => $b->id, 'label' => $b->name]),
        ]);
    }

    // ---------------------------------------------------------------------
    // Configuration déclarative des ressources gérables.
    // ---------------------------------------------------------------------
    private function resources(): array
    {
        return [
            // -------------------- Catégories --------------------
            'categories' => [
                'model' => Categories::class,
                'searchable' => ['name'],
                'order' => ['name', 'asc'],
                'rules' => fn (Request $r, $item) => [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'parent_id' => 'nullable|integer|exists:categories,id',
                    'is_active' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['slug'] = Str::slug($data['name']) . ($item ? '' : '-' . Str::lower(Str::random(4)));
                    $data['is_active'] = $data['is_active'] ?? true;
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                    'parent_id' => $m->parent_id,
                    'is_active' => (bool) $m->is_active,
                ],
            ],

            // -------------------- Marques --------------------
            'brands' => [
                'model' => Brand::class,
                'searchable' => ['name'],
                'order' => ['name', 'asc'],
                'rules' => fn (Request $r, $item) => [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'website' => 'nullable|string|max:255',
                    'is_active' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['is_active'] = $data['is_active'] ?? true;
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                    'website' => $m->website,
                    'is_active' => (bool) $m->is_active,
                ],
            ],

            // -------------------- Tags --------------------
            'tags' => [
                'model' => Tag::class,
                'searchable' => ['name'],
                'order' => ['name', 'asc'],
                'rules' => fn (Request $r, $item) => [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ],
                'prepare' => function (array $data, $item) {
                    $data['slug'] = Str::slug($data['name']) . ($item ? '' : '-' . Str::lower(Str::random(4)));
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                ],
            ],

            // -------------------- Caractéristiques --------------------
            'caracteristiques' => [
                'model' => Caracteristiques::class,
                'searchable' => ['name', 'type'],
                'order' => ['name', 'asc'],
                'rules' => fn (Request $r, $item) => [
                    'name' => 'required|string|max:255',
                    'type' => 'nullable|string|max:255',
                    'unite' => 'nullable|string|max:60',
                    'is_filterable' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['is_filterable'] = $data['is_filterable'] ?? false;
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'type' => $m->type,
                    'unite' => $m->unite,
                    'is_filterable' => (bool) $m->is_filterable,
                ],
            ],

            // -------------------- Méthodes de livraison --------------------
            'shipping-methods' => [
                'model' => Livraison::class,
                'searchable' => ['method_name'],
                'order' => ['id', 'desc'],
                'rules' => fn (Request $r, $item) => [
                    'method_name' => 'required|string|max:255',
                    'price' => 'required|numeric|min:0',
                    'delivery_time_min' => 'nullable|integer|min:0',
                    'delivery_time_max' => 'nullable|integer|min:0',
                    'description' => 'nullable|string',
                    'is_active' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['is_active'] = $data['is_active'] ?? true;
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'method_name' => $m->method_name,
                    'price' => (float) $m->price,
                    'delivery_time_min' => $m->delivery_time_min,
                    'delivery_time_max' => $m->delivery_time_max,
                    'delivery_time' => trim(
                        ($m->delivery_time_min ? $m->delivery_time_min : '')
                        . ($m->delivery_time_max ? '-' . $m->delivery_time_max : '')
                        . ($m->delivery_time_min || $m->delivery_time_max
                            ? ' ' . ($m->delivery_time_unit ?? 'jours')
                            : '')
                    ),
                    'description' => $m->description,
                    'is_active' => (bool) $m->is_active,
                ],
            ],

            // -------------------- Méthodes de paiement --------------------
            'payment-methods' => [
                'model' => Paiements::class,
                'searchable' => ['method_name', 'provider_name'],
                'order' => ['sort_order', 'asc'],
                'rules' => fn (Request $r, $item) => [
                    'method_name' => 'required|string|max:255',
                    'provider_name' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'instructions' => 'nullable|string',
                    'account_number' => 'nullable|string|max:120',
                    'is_active' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['is_active'] = $data['is_active'] ?? true;
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'method_name' => $m->method_name,
                    'provider_name' => $m->provider_name,
                    'description' => $m->description,
                    'instructions' => $m->instructions,
                    'account_number' => $m->account_number,
                    'is_active' => (bool) $m->is_active,
                ],
            ],

            // -------------------- Coupons / codes promo --------------------
            'coupons' => [
                'model' => PromoCode::class,
                'searchable' => ['code'],
                'order' => ['id', 'desc'],
                'rules' => fn (Request $r, $item) => [
                    'code' => ['required', 'string', 'max:60', Rule::unique('promo_codes', 'code')->ignore($item?->id)],
                    'type' => 'required|in:percentage,fixed',
                    'value' => 'required|numeric|min:0',
                    'starts_at' => 'nullable|date',
                    'expires_at' => 'nullable|date|after_or_equal:starts_at',
                    'usage_limit' => 'nullable|integer|min:1',
                    'is_active' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['code'] = Str::upper($data['code']);
                    $data['starts_at'] = $data['starts_at'] ?? now();
                    $data['expires_at'] = $data['expires_at'] ?? now()->addMonth();
                    $data['is_active'] = $data['is_active'] ?? true;
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'code' => $m->code,
                    'type' => $m->type,
                    'value' => (float) $m->value,
                    'starts_at' => optional($m->starts_at)->format('Y-m-d'),
                    'expires_at' => optional($m->expires_at)->format('Y-m-d'),
                    'usage_limit' => $m->usage_limit,
                    'usage_count' => $m->usage_count,
                    'is_active' => (bool) $m->is_active,
                ],
            ],

            // -------------------- Produits --------------------
            'products' => [
                'model' => Produits::class,
                'with' => ['category', 'brand'],
                'searchable' => ['name', 'sku'],
                'order' => ['id', 'desc'],
                'rules' => fn (Request $r, $item) => [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'price' => 'required|numeric|min:0',
                    'sale_price' => 'nullable|numeric|min:0',
                    'stock' => 'nullable|integer|min:0',
                    'category_id' => 'nullable|integer|exists:categories,id',
                    'brand_id' => 'nullable|integer|exists:brands,id',
                    'is_active' => 'nullable|boolean',
                ],
                'prepare' => function (array $data, $item) {
                    $data['is_active'] = $data['is_active'] ?? true;
                    $data['stock'] = $data['stock'] ?? 0;
                    // La colonne `description` est NOT NULL sans valeur par défaut.
                    if (empty($item)) {
                        $data['description'] = $data['description'] ?? '';
                        if (empty($data['sku'] ?? null)) {
                            $data['sku'] = 'SKU-' . Str::upper(Str::random(8));
                        }
                    }
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                    'price' => (float) $m->price,
                    'sale_price' => $m->sale_price !== null ? (float) $m->sale_price : null,
                    'stock' => (int) $m->stock,
                    'category_id' => $m->category_id,
                    'category' => optional($m->category)->name,
                    'brand_id' => $m->brand_id,
                    'brand' => optional($m->brand)->name,
                    'is_active' => (bool) $m->is_active,
                    'image' => $m->image,
                ],
            ],

            // -------------------- Utilisateurs --------------------
            'users' => [
                'model' => User::class,
                'searchable' => ['name', 'email', 'tel'],
                'order' => ['id', 'desc'],
                'rules' => fn (Request $r, $item) => [
                    'name' => 'required|string|max:255',
                    'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($item?->id)],
                    'tel' => 'nullable|string|max:30',
                    'role' => 'required|in:admin,customer',
                    'status' => 'nullable|string|max:30',
                    'password' => $item ? 'nullable|string|min:6' : 'required|string|min:6',
                ],
                'prepare' => function (array $data, $item) {
                    if (!empty($data['password'])) {
                        $data['password'] = Hash::make($data['password']);
                    } else {
                        unset($data['password']);
                    }
                    $data['status'] = $data['status'] ?? 'active';
                    return $data;
                },
                'map' => fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                    'tel' => $m->tel,
                    'role' => $m->role,
                    'status' => $m->status,
                    'wallet_balance' => (float) ($m->wallet_balance ?? 0),
                ],
            ],
        ];
    }
}
