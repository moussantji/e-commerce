<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'image' => $this->abs($img),
            'banner_images' => array_values(array_filter([
                $this->abs($this->bannerPath($this->banner_image_1)),
                $this->abs($this->bannerPath($this->banner_image_2)),
            ])),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'products_count' => (int) ($this->products_count ?? 0),
        ];
    }

    /** Chemin relatif (/storage/...) de l'image de bannière sur le disque public.
     *  On reste volontairement relatif pour que abs() applique l'hôte de la requête
     *  (Storage::url() préfixerait avec APP_URL=localhost, injoignable sur mobile). */
    private function bannerPath(?string $filename): ?string
    {
        return $filename ? '/storage/' . ltrim($filename, '/') : null;
    }

    /** URL absolue basée sur l'hôte de la requête (joignable depuis l'app mobile). */
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
