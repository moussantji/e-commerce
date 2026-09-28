<?php

namespace App\Models;

use App\Models\photos;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

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
        'ville',
        'region',
        'latitude',
        'longitude',
        'adresse',
        'social_links',
        'last_activity',
        'provider',
        'provider_id',
        'wallet_balance',
        'expo_push_token',
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
        'adresse'      => 'array',
        'social_links' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /** L'utilisateur est-il administrateur ? */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

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
     *
     */
    public function getId()
    {
        return $this->id;
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

    // User
    // app/Models/Produit.php
    public function photos()
    {
        return $this->hasMany(Photos::class);
    }

    /**
     * @param UploadedFile $files
     */
    public function attachfiles(?array $files)
    {
        $pictures = [];
        if ($files !== null) {
            foreach ($files as $file) {
                if ($file->getError()) {
                    continue;
                }
                $filename = $file->store('User/' . $this->id, 'public');
                $pictures[] = [
                    'filename' => $filename,
                ];
            }
        }
        if (count($pictures) > 0) {
            $this->photos()->createMany($pictures);
        }
    }

    public function getPhoto(): ?Photos
    {
        return $this->photos()->where('user_id', $this->id)->first();
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Produits::class, 'wishlist_user_produit', 'user_id', 'produits_id');
    }

    public function wishlistCount()
    {
        return $this->wishlistProducts()->count();
    }

    public function panier()
    {
        return $this->hasOne(Paniers::class, 'user_id');
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
