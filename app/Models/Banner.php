<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title1',
        'title2',
        'percentage',
        'button_link',
        'is_active'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // ✅ SUPPRIME DOSSIER quand bannière supprimée
        static::deleting(function (Banner $banner) {
            $banner->photos->each(function ($photo) {
                $photo->delete(); // Déclenche suppression fichier
            });
        });
    }

    // Dans Banner.php
    public function getTitle2ShortAttribute(): string
    {
        return Str::limit($this->title2, 30, '...');
    }

    // Dans Banner.php
public function getTitle1ShortAttribute(): string
{
    return Str::limit($this->title1, 30, '...');
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
        if ($files !== null && is_array($files) && count(array_filter($files)) > 0) {
            // 1️⃣ Récupère TOUTES les anciennes photos
            $oldPhotos = $this->photos()->get();

            // 2️⃣ SUPPRIME FICHIERS PHYSIQUES
            foreach ($oldPhotos as $photo) {
                if (Storage::disk('public')->exists($photo->filename)) {
                    Storage::disk('public')->delete($photo->filename);
                }
            }
            $this->photos()->delete();
        }



        $pictures = [];
        if ($files !== null) {
            foreach ($files as $file) {
                // ✅ VÉRIFIER que c'est un UploadedFile valide AVANT getError()
                if (!$file instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }

                // ✅ Maintenant getError() est SÛR
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    continue; // Skip fichier avec erreur
                }
                $filename = $file->store('Banners/' . $this->id, 'public');
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
        return $this->photos()->where('banner_id', $this->id)->first();
    }
}
