<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'starts_at',
        'expires_at',
        'usage_limit',
        'usage_count',
        'is_active',
        'is_automatic',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_automatic' => 'boolean',
    ];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commandes::class);
    }

    /** Clients à qui ce coupon a été attribué. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withPivot(['sent_at', 'used_at', 'source', 'last_sent_month'])
            ->withTimestamps();
    }

    /** Coupon utilisé pour la distribution automatique. */
    public function scopeAutomatic(Builder $query): Builder
    {
        return $query->where('is_automatic', true);
    }

    /** Recherche un code sans tenir compte de la casse. */
    public function scopeWhereCode(Builder $query, string $code): Builder
    {
        return $query->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($code))]);
    }

    /** Le coupon a-t-il déjà été attribué à ce client ? */
    public function isAssignedTo(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->users()->where('users.id', $user->id)->exists();
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        return $this->type === 'percentage'
            ? $amount * ($this->value / 100)
            : min($this->value, $amount);
    }
}
