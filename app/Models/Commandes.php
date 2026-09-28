<?php

namespace App\Models;

use App\Notifications\OrderStatusNotification;
use App\Support\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class Commandes extends Model
{
    /**
     * Notifie le client (base + email) à chaque changement de statut.
     */
    protected static function booted(): void
    {
        static::updated(function (Commandes $order) {
            if ($order->wasChanged('statut') && $order->user) {
                try {
                    $order->user->notify(
                        new OrderStatusNotification($order, $order->status_label),
                    );
                } catch (\Throwable $e) {
                    Log::warning('Notification changement de statut échouée : ' . $e->getMessage());
                }
            }
        });
    }
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
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produits::class, 'commande_produit', 'commande_id', 'produit_id')
            ->withPivot(['quantite', 'prix_unitaire', 'total', 'options']);
    }

    // Alias pour la relation produits (pour la rétrocompatibilité)
    public function products(): BelongsToMany
    {
        return $this->produits();
    }

    // Add to your Commandes model
    public function getIsCancelledAttribute(): bool
    {
        return OrderStatus::normalize($this->statut) === OrderStatus::ANNULE;
    }

    public function getStatusLabelAttribute(): string
    {
        return OrderStatus::label($this->statut);
    }

    /** Classe de couleur Bootstrap / badge Phoenix du statut (site web). */
    public function getStatusBadgeClassAttribute(): string
    {
        return OrderStatus::badgeClass($this->statut);
    }

    /** Icône Feather du statut. */
    public function getStatusIconAttribute(): string
    {
        return OrderStatus::icon($this->statut);
    }

    /** Couleur hexadécimale du statut. */
    public function getStatusColorAttribute(): string
    {
        return OrderStatus::color($this->statut);
    }
    public function getTotalAttribute()
    {
        $total = $this->sous_total + $this->frais_livraison;
        if ($this->promo_code_id && $this->promoCode) {
            $total -= $this->promoCode->calculateDiscount($this->sous_total);
        }
        return $total - ($this->remise ?? 0);
    }

    public function items()
    {
        return $this->hasMany(CommandeProduit::class, 'commande_id');
    }

    public function isPaid()
    {
        return in_array(OrderStatus::normalize($this->statut), [OrderStatus::PAYEE, OrderStatus::EXPEDIE, OrderStatus::LIVRE], true);
    }
}
