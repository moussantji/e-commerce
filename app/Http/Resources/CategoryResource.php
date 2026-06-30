<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use League\Glide\Urls\UrlBuilderFactory;

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
     * URL absolue d'une image de bannière, servie via Glide (redimensionnée),
     * comme le champ `image` ci-dessus.
     */
    private function bannerUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }

        $glide = UrlBuilderFactory::create('/images/', config('glide.key'))
            ->getUrl($filename, ['w' => 800, 'h' => 600, 'fit' => 'crop']);

        return rtrim(config('app.url'), '/') . '/' . ltrim($glide, '/');
    }
}
