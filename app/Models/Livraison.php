<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livraison extends Model
{
    protected $fillable = [
        'carrier_name',
        'method',
        'delivery_time',
        'price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Commandes::class, 'shipping_id');
    }
}
