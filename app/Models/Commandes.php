<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commandes extends Model
{
    protected $fillable = [
        'user_id',
        'paiement_id',
        'livraison_id',
        'promo_code_id',
        'numero_commande',
        'statut',
        'date_commande',
        'sous_total',
        'frais_livraison',
        'remise',
        'promo_discount',
        'total',
        'adresse_facturation',
        'adresse_livraison',
        'notes',
        'date_en_attente',
        'date_traitement',
        'date_expedition',
        'date_livraison',
        'date_annulation',
    ];

    protected $casts = [
        'adresse_facturation' => 'array',
        'adresse_livraison' => 'array',
        'date_commande' => 'datetime',
        'date_en_attente' => 'datetime',
        'date_traitement' => 'datetime',
        'date_expedition' => 'datetime',
        'date_livraison' => 'datetime',
        'date_annulation' => 'datetime',
        'sous_total' => 'float',
        'frais_livraison' => 'float',
        'remise' => 'float',
        'promo_discount' => 'float',
        'total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiements::class, 'paiement_id', 'id');
    }

    public function livraison(): BelongsTo
    {
        return $this->belongsTo(Livraison::class, 'livraison_id', 'id');
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produits::class, 'commande_produit', 'commande_id', 'produit_id')
            ->withPivot(['quantite', 'prix_unitaire', 'total']);
    }
    
    // Alias pour la relation produits (pour la rétrocompatibilité)
    public function products(): BelongsToMany
    {
        return $this->produits();
    }
}
