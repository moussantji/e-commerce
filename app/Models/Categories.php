<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'is_active',
        'parent_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = ['avg_rating', 'avis_count']; // ✅ Rend persistant

    public function products(): HasMany
    {
        return $this->hasMany(Produits::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Categories::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Categories::class, 'parent_id');
    }
    public function getPhoto(): ?Photos
    {
        return $this->photos()->where('categories_id', $this->id)->first();
    }

    public function getProductsCountAttribute()
    {
        $categoryIds = collect([$this->id]);

        // Récupère TOUS les IDs enfants (récursif)
        $categoryIds = $categoryIds->merge(
            $this->children()->with('children')->get()
                ->pluck('id')
                ->merge(
                    $this->children()->with('children')->get()
                        ->pluck('id')
                )
        );

        return Produits::whereIn('category_id', $categoryIds)->count();
    }

    public function pluckAllCategoryIds($ids = null)
    {
        $ids = $ids ?? collect([$this->id]);

        // Ajoute les enfants DIRECTS déjà chargés (plus rapide)
        $childrenIds = $this->children->pluck('id');
        $ids = $ids->merge($childrenIds);

        // Récursif pour CHAQUE enfant
        foreach ($this->children as $child) {
            $childIds = $child->pluckAllCategoryIds();
            $ids = $ids->merge($childIds);
        }

        return $ids->unique(); // Évite doublons
    }

    public function getAvgRatingAttribute()
    {
        $allCategoryIds = $this->pluckAllCategoryIds()->toArray();

        $avisCount = DB::table('produits')
            ->join('avis_clients', 'produits.id', '=', 'avis_clients.produits_id')
            ->whereIn('produits.category_id', $allCategoryIds)
            ->count();


        if ($avisCount > 0) {
            return round(
                DB::table('produits')
                    ->join('avis_clients', 'produits.id', '=', 'avis_clients.produits_id')
                    ->whereIn('produits.category_id', $allCategoryIds)
                    ->avg('avis_clients.nb_etoiles'),
                1
            );
        }

        return 5;
    }

    public function getAvisCountAttribute()
    {
        $allCategoryIds = $this->pluckAllCategoryIds()->toArray();

        return DB::table('produits')
            ->join('avis_clients', 'produits.id', '=', 'avis_clients.produits_id')
            ->whereIn('produits.category_id', $allCategoryIds)
            ->count();
    }

    // app/Models/Categories.php
    public function getRouteKeyName()
    {
        return 'slug';
    }





    public function photos()
    {
        return $this->hasMany(Photos::class);
    }
}
