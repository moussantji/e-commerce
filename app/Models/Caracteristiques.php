<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Caracteristiques extends Model
{
    protected $fillable = [
        'name',
        'type',
        'unite',
        'is_filterable'
    ];

    protected $casts = [
        'is_filterable' => 'boolean'
    ];

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produits::class, 'produit_caracteristique')
            ->withPivot('value');
    }
}
