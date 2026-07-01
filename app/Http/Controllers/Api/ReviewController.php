<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvisClient;
use App\Models\Produits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Avis clients (mobile) — reproduit submitReview() du composant web ProduitDetail.
 */
class ReviewController extends Controller
{
    /** Liste paginée des avis d'un produit. */
    public function index(Request $request, $productId)
    {
        $reviews = AvisClient::where('produits_id', $productId)
            ->with(['user', 'response', 'photos'])
            ->latest()
            ->paginate((int) $request->query('per_page', 10));

        return response()->json([
            'data' => $reviews->getCollection()->map(fn ($r) => $this->format($r)),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /** Créer un avis pour un produit. */
    public function store(Request $request, $productId)
    {
        $product = Produits::findOrFail($productId);

        $data = $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'comment' => 'nullable|string|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:20480',
        ]);

        $user = $request->user();

        // Un seul avis par utilisateur et par produit
        $existing = AvisClient::where('produits_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Vous avez déjà laissé un avis pour ce produit.',
            ], 422);
        }

        $review = AvisClient::create([
            'produits_id' => $product->id,
            'user_id' => $user->id,
            'nb_etoiles' => $data['rating'],
            'note' => $data['rating'],
            'commentaire' => $data['comment'] ?? null,
        ]);

        // Photos jointes (optionnel) — même mécanisme que le web
        if ($request->hasFile('photos')) {
            $review->attachfiles($request->file('photos'));
        }

        // Invalide le cache des notes (même clé que le web)
        Cache::forget("product_{$product->id}_ratings");

        $review->load(['user', 'response', 'photos']);

        return response()->json([
            'message' => 'Merci pour votre avis !',
            'data' => $this->format($review),
        ], 201);
    }

    private function format(AvisClient $r): array
    {
        return [
            'id' => $r->id,
            'author' => optional($r->user)->name ?? 'Client',
            'rating' => (int) ($r->nb_etoiles ?? 0),
            'comment' => $r->commentaire,
            'date' => optional($r->created_at)->diffForHumans(),
            'images' => $r->relationLoaded('photos')
                ? $r->photos->map(fn ($p) => $this->abs($p->getImageUrl(300, 300)))->values()
                : [],
            'response' => $r->relationLoaded('response') && $r->response ? [
                'message' => $r->response->message,
                'date' => optional($r->response->created_at)->diffForHumans(),
            ] : null,
        ];
    }

    private function abs(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return rtrim(request()->getSchemeAndHttpHost(), '/') . '/' . ltrim($path, '/');
    }
}
