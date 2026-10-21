<?php

namespace App\Models;

use App\Models\photos as Photos;
use App\Models\ReviewReply;
use App\Models\ReviewResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvisClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'produits_id',
        'user_id',
        'note',
        'commentaire',
        'nb_etoiles'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Produits::class, 'produits_id');
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
        if ($files !== null) {
            foreach ($files as $file) {
                if ($file->getError()) {
                    continue;
                }
                $filename = $file->store('Avis/' . $this->id, 'public');
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
        return $this->photos()->where('avis_client_id', $this->id)->first();
    }

    public function response()
    {
        return $this->hasOne(ReviewResponse::class, 'avis_client_id');
    }

    /**
     * Toutes les réponses de cet avis (réponses directes et réponses imbriquées).
     * L'arborescence est reconstruite via parent_id dans les vues / l'API.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ReviewReply::class, 'avis_client_id')->oldest();
    }

    /** Réponses directes uniquement (parent_id = null). */
    public function rootReplies(): HasMany
    {
        return $this->hasMany(ReviewReply::class, 'avis_client_id')->whereNull('parent_id')->oldest();
    }
}
