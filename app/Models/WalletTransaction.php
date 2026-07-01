<?php

namespace App\Models;

use App\Support\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'method',
        'phone',
        'recipient_id',
        'status',
        'reference',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
