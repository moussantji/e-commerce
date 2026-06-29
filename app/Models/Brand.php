<?php

namespace App\Models;

use App\Models\Produits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\photos as Photos;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
                'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /*
     * Relation avec les produits de cette marque
     */
    public function products(): HasMany
    {
        return $this->hasMany(Produits::class);
    }

    /**
     * Génère un slug à partir du nom de la marque
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            $brand->slug = \Illuminate\Support\Str::slug($brand->name);
        });

        static::updating(function ($brand) {
            $brand->slug = \Illuminate\Support\Str::slug($brand->name);
        });
    }

    public function attachfiles(?array $files)
    {
        $pictures = [];
        if ($files !== null) {
            foreach ($files as $file) {
                if ($file->getError()) {
                    continue;
                }
                $filename = $file->store('Marques/' . $this->id, 'public');
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
        return $this->photos()->where('brand_id', $this->id)->first();
    }

    public function photos()
    {
        return $this->hasMany(Photos::class);
    }
}
