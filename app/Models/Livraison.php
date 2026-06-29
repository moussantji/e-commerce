<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\photos as Photos;

class Livraison extends Model
{
    protected $fillable = [
        'method_name',
        'method',
        'delivery_time',
        'delivery_time_min',
        'delivery_time_max',
        'delivery_time_unit',
        'price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Commandes::class, 'livraison_id');
    }

    public function photos()
    {
        return $this->hasMany(Photos::class, 'livraison_id');
    }

    /**
     * @param UploadedFile $files
     */
    public function attachfiles(?array $files)
    {
        $pictures = [];
        if ($files !== null) {
            foreach ($files as $file) {
                if ($file->getError()) {
                    continue;
                }
                $filename = $file->store('Livraison/' . $this->id, 'public');
                $pictures[] = [
                    'filename' => $filename,
                ];
            }
        }
        if (count($pictures) > 0) {
            $this->photos()->createMany($pictures);
        }
    }

    public function getPhoto(): ?Photos
    {
        return $this->photos()->where('livraison_id', $this->id)->first();
    }
}
