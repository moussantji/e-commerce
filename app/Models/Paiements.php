<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\photos as Photos;

class Paiements extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'method_name',
        'provider_name',
        'description',
        'instructions',
        'account_number',
        'fee',
        'fee_percentage',
        'logo',
        'is_active',
        'sort_order',
        'config',
        'status',
        'payment_date',
        'transaction_id',
        'payment_mode',
        'details'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'details' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /** URL du logo (via la relation photos), ou null. */
    public function logoUrl(): ?string
    {
        $photo = $this->photos()->first();
        return $photo ? $photo->getImageUrl(200, 200) : null;
    }

    /** Vrai s'il s'agit du paiement à la livraison (espèces). */
    public function isCashOnDelivery(): bool
    {
        return strtolower((string) $this->provider_name) === 'cash'
            || str_contains(strtolower((string) $this->method_name), 'livraison');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Commandes::class, 'paiement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
        if($files !== null)
        {    foreach($files as $file)
            {
                if($file->getError())
                {
                    continue;
                }
                $filename = $file->store('Paiement/'. $this->id, 'public');
                $pictures[] = [
                    'filename'=> $filename,
                ];
            }
        }
        if(count($pictures) > 0)
        {
            $this->photos()->createMany($pictures);
        }
    }

    public function getPhoto(): ?Photos
    {
        return $this->photos()->where('paiements_id', $this->id)->first();
    }
}
