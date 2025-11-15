<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paniers extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Produits::class, 'panier_produit')
            ->withPivot(['quantity', 'unit_price']);
    }

    public function getTotalAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->unit_price;
        });
    }
    
    /**
     * Formate un montant en FCFA
     */
    public function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
    
    /**
     * Accesseur pour le total formaté en FCFA
     */
    public function getTotalFormattedAttribute()
    {
        return $this->formatFcfa($this->total);
    }
    
    /**
     * Accesseur pour le prix unitaire formaté en FCFA
     */
    public function getUnitPriceFormatted($price)
    {
        return $this->formatFcfa($price);
    }
    
    /**
     * Accesseur pour le sous-total d'une ligne formaté en FCFA
     */
    public function getLineTotalFormatted($quantity, $unitPrice)
    {
        return $this->formatFcfa($quantity * $unitPrice);
    }
}
