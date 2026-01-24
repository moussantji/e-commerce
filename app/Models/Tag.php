<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produits::class, 'product_tag', 'tag_id', 'produit_id');
    }
}
