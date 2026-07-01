<?php

namespace App\Models;

use App\Support\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    protected $table = 'payment_proofs';

    protected $fillable = [
        'user_id', 'order_id', 'provider', 'phone', 'amount', 'photos', 'status', 'notes'
    ];

    protected $casts = [
        'photos' => 'array',
        'amount' => 'decimal:2'
    ];

    /** Libellé français du statut (identique au site et à l'app mobile). */
    public function getStatusLabelAttribute(): string
    {
        return PaymentStatus::label($this->status);
    }

    /** Classe de couleur Bootstrap du statut (site web). */
    public function getStatusBadgeClassAttribute(): string
    {
        return PaymentStatus::badgeClass($this->status);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Commandes::class, 'order_id');
    }
}
