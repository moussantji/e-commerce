<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'tel',
        'role',
        'status',
        'date_naiss',
        'lieu_naiss',
        'pays',
        'region',
        'adresse',
        'social_links',
        'last_login',
        'last_activity'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login' => 'datetime',
        'last_activity' => 'datetime',
        'date_naiss' => 'date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['full_name'];

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->prenom ?? '') . ' ' . $this->name);
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Commandes::class, 'user_id');
    }

    /**
     * Get the user's last order.
     */
    public function getLastOrderAttribute()
    {
        return $this->orders()->latest()->first();
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @deprecated Use the "casts" property
     *
     * @var array<int, string>
     */
    protected $dates = [
        'last_login',
        'last_activity',
        'date_naiss',
        'created_at',
        'updated_at',
        'email_verified_at'
    ];
}
