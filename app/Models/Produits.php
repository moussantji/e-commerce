<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tag;

class Produits extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'image',
        'images',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'images' => 'array'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Caracteristiques::class, 'produit_caracteristique')
            ->withPivot('value');
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Paniers::class, 'panier_produit')
            ->withPivot(['quantity', 'unit_price']);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Commandes::class, 'commande_produit')
            ->withPivot(['quantity', 'unit_price', 'subtotal']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AvisClient::class, 'product_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag', 'produit_id', 'tag_id');
    }
    
    // Alias pour la relation commandes (pour la rétrocompatibilité)
    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commandes::class, 'commande_produit', 'produit_id', 'commande_id')
            ->withPivot(['quantite', 'prix_unitaire', 'total']);
    }
}
