<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AvisClient;
use App\Models\Produits;
use App\Models\ReviewReply;
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

        // Toutes les réponses des avis de la page, en une requête.
        $reviewIds = $reviews->getCollection()->pluck('id')->all();
        $allReplies = empty($reviewIds)
            ? collect()
            : ReviewReply::with('user')
                ->whereIn('avis_client_id', $reviewIds)
                ->orderBy('created_at')
                ->get();

        $repliesByReview = $allReplies
            ->groupBy('avis_client_id')
            ->map(fn ($group) => $group->groupBy(fn ($reply) => (int) ($reply->parent_id ?? 0)));

        return response()->json([
            'data' => $reviews->getCollection()->map(
                fn ($r) => $this->format($r, $repliesByReview->get($r->id, collect()))
            ),
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

    /**
     * Répondre à un avis, ou à une réponse existante (réponses imbriquées).
     */
    public function storeReply(Request $request, $productId, $reviewId)
    {
        $product = Produits::findOrFail($productId);

        $review = AvisClient::where('produits_id', $product->id)->findOrFail($reviewId);

        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer',
        ]);

        $parentId = null;
        if (!empty($data['parent_id'])) {
            $parent = ReviewReply::where('avis_client_id', $review->id)
                ->find($data['parent_id']);

            if (!$parent) {
                return response()->json([
                    'message' => 'La réponse parente est invalide.',
                ], 422);
            }

            $parentId = $parent->id;
        }

        $reply = ReviewReply::create([
            'avis_client_id' => $review->id,
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'message' => trim($data['message']),
        ]);

        $reply->load('user');

        return response()->json([
            'message' => 'Réponse publiée.',
            'data' => $this->formatReply($reply),
        ], 201);
    }

    private function format(AvisClient $r, $repliesByParent = null): array
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
            'replies' => $repliesByParent
                ? $this->buildReplyTree($repliesByParent, 0)
                : [],
        ];
    }

    private function formatReply(ReviewReply $reply): array
    {
        return [
            'id' => $reply->id,
            'parent_id' => $reply->parent_id,
            'author' => optional($reply->user)->name ?? 'Client',
            'role' => optional($reply->user)->role,
            'message' => $reply->message,
            'date' => optional($reply->created_at)->diffForHumans(),
            'replies' => [],
        ];
    }

    private function buildReplyTree($repliesByParent, int $parentId): array
    {
        return collect($repliesByParent->get($parentId, collect()))
            ->map(function (ReviewReply $reply) use ($repliesByParent) {
                return array_merge($this->formatReply($reply), [
                    'replies' => $this->buildReplyTree($repliesByParent, $reply->id),
                ]);
            })
            ->values()
            ->all();
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
