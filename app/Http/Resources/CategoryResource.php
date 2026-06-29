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
            'image' => $img ? rtrim(config('app.url'), '/') . '/' . ltrim($img, '/') : null,
            'products_count' => (int) ($this->products_count ?? 0),
        ];
    }
}
