<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paiements extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'status',
        'payment_date',
        'transaction_id',
        'payment_mode',
        'details'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'details' => 'array'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Commandes::class, 'payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
