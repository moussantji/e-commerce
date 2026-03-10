<?php

namespace App\Models;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Commandes::class, 'order_id');
    }
}
