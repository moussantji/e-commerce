<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $photo = $this->getPhoto();
        $images = $this->relationLoaded('photos') ? $this->photos : collect();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => method_exists($this->resource, 'getSlug') ? $this->getSlug() : null,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
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
            'rating_avg' => round((float) ($this->reviews_avg_nb_etoiles ?? 0), 1),
            'rating_count' => (int) ($this->reviews_count ?? 0),
        ];
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
