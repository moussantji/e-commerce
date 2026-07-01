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
        'status',
        'date_maj',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_maj' => 'datetime',
    ];

    /**
     * Garantit que date_maj (NOT NULL) est toujours renseigné, quel que soit
     * le chemin de création (web, API, firstOrCreate...).
     */
    protected static function booted(): void
    {
        static::creating(function (Paniers $panier) {
            if (empty($panier->date_maj)) {
                $panier->date_maj = now();
            }
        });

        static::saving(function (Paniers $panier) {
            if (empty($panier->date_maj)) {
                $panier->date_maj = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Produits::class, 'panier_produit', 'paniers_id', 'produits_id')
            ->withPivot(['quantite', 'prix_unitaire','total_ligne']);
    }

    public function getTotalAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->quantite * $product->pivot->unit_prix_unitaire;
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
