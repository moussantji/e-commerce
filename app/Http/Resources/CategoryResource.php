<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $photo = method_exists($this->resource, 'getPhoto') ? $this->getPhoto() : null;
        $img = $photo ? $photo->getImageUrl(400, 400) : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $img ? rtrim(config('app.url'), '/') . '/' . ltrim($img, '/') : null,
            'banner_images' => array_values(array_filter([
                $this->bannerUrl($this->banner_image_1),
                $this->bannerUrl($this->banner_image_2),
            ])),
            'products_count' => (int) ($this->products_count ?? 0),
        ];
    }

    /**
     * Construit l'URL publique absolue d'une image de bannière.
     */
    private function bannerUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $url = Storage::disk('public')->url($path);

        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        return rtrim(config('app.url'), '/') . '/' . ltrim($url, '/');
    }
}
