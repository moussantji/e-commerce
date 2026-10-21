<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $photo = $this->getPhoto();
        $images = $this->relationLoaded('photos') ? $this->photos : collect();

        $price = (float) $this->price;
        $salePrice = $this->sale_price ? (float) $this->sale_price : null;
        $discount = ($salePrice && $price > 0 && $salePrice < $price)
            ? (int) round((($price - $salePrice) / $price) * 100)
            : 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => method_exists($this->resource, 'getSlug') ? $this->getSlug() : null,
            'description' => $this->description,
            'price' => $price,
            'sale_price' => $salePrice,
            'discount_percent' => $discount,
            'stock' => (int) $this->stock,
            'in_stock' => (int) $this->stock > 0,
            'sku' => $this->sku,
            'is_featured' => (bool) $this->is_featured,
            'image' => $this->abs($photo ? $photo->getImageUrl(600, 600) : asset('assets/img/products/1.png')),
            'images' => $images->map(fn ($p) => $this->abs($p->getImageUrl(600, 600)))->values(),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'brand' => $this->whenLoaded('brand', fn () => $this->brand ? [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ] : null),
            // Spécifications (caractéristiques) : nom + valeur + unité
            'specifications' => $this->whenLoaded('caracteristiques', fn () => $this->caracteristiques->map(fn ($c) => [
                'name' => $c->name,
                'type' => $c->type,
                'value' => $c->pivot->value ?? null,
                'unite' => $c->unite,
            ])->values()),
            // Avis clients (liste) + réponses imbriquées
            'reviews' => $this->whenLoaded('reviews', fn () => $this->reviews
                ->map(fn ($r) => $this->formatReview($r))
                ->values()),
            'rating_avg' => round((float) ($this->reviews_avg_nb_etoiles ?? 0), 1),
            'rating_count' => (int) ($this->reviews_count ?? 0),
        ];
    }

    private function formatReview($review): array
    {
        $allReplies = $review->relationLoaded('replies') ? $review->replies : collect();
        $repliesByParent = $allReplies->groupBy(fn ($reply) => (int) ($reply->parent_id ?? 0));

        return [
            'id' => $review->id,
            'author' => optional($review->user)->name ?? 'Client',
            'rating' => (int) ($review->nb_etoiles ?? 0),
            'comment' => $review->commentaire,
            'date' => optional($review->created_at)->diffForHumans(),
            'images' => $review->relationLoaded('photos')
                ? $review->photos->map(fn ($p) => $this->abs($p->getImageUrl(300, 300)))->values()
                : [],
            'response' => $review->relationLoaded('response') && $review->response ? [
                'message' => $review->response->message,
                'date' => optional($review->response->created_at)->diffForHumans(),
            ] : null,
            'replies' => $this->buildReplyTree($repliesByParent, 0),
        ];
    }

    private function buildReplyTree($repliesByParent, int $parentId): array
    {
        return collect($repliesByParent->get($parentId, collect()))
            ->map(function ($reply) use ($repliesByParent) {
                return [
                    'id' => $reply->id,
                    'parent_id' => $reply->parent_id,
                    'author' => optional($reply->user)->name ?? 'Client',
                    'role' => optional($reply->user)->role,
                    'message' => $reply->message,
                    'date' => optional($reply->created_at)->diffForHumans(),
                    'replies' => $this->buildReplyTree($repliesByParent, $reply->id),
                ];
            })
            ->values()
            ->all();
    }

    /** Transforme un chemin relatif en URL absolue basée sur l'hôte de la requête
     *  (indispensable côté mobile : émulateur/téléphone n'atteignent pas APP_URL=localhost). */
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
