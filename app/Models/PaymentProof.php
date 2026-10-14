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

    /** Chemins des captures (colonne JSON). */
    public function photoPaths(): array
    {
        $phs = $this->photos ?? [];
        if (is_string($phs)) {
            $phs = json_decode($phs, true) ?: [];
        }

        return is_array($phs) ? array_values($phs) : [];
    }

    /**
     * URLs via getImageUrl (Glide, servi par Laravel comme les images
     * produits) : pas d'accès direct /storage, donc pas de 403 sur mutualisé.
     */
    public function photoThumbUrl(int $index): string
    {
        $paths = $this->photoPaths();
        if (!isset($paths[$index]) || !is_string($paths[$index])) {
            return '';
        }

        return (new photos(['filename' => $paths[$index]]))->getImageUrl(72, 72);
    }

    /** Image en grand (largeur 1200, ratio conservé ou original en secours). */
    public function photoFullUrl(int $index): string
    {
        $paths = $this->photoPaths();
        if (!isset($paths[$index]) || !is_string($paths[$index])) {
            return '';
        }

        return (new photos(['filename' => $paths[$index]]))->getImageUrl(1200);
    }
}
