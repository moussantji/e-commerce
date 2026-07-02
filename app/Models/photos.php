<?php

namespace App\Models;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use League\Glide\Urls\UrlBuilderFactory;
use Intervention\Image\Drivers\Gd\Driver;
use League\Glide\Signatures\SignatureFactory;

class photos extends Model
{
    protected $fillable = ['filename', 'user_id', 'produit_id', 'payment_id', 'livraison_id', 'brand_id', 'banner_id','categories_id'];

    // Photos.php
    protected static function booted(): void
    {
        static::deleting(function (Photos $picture) {
            // Supprime fichier
            Storage::disk('public')->delete($picture->filename);

            // ✅ Récupère banner_id AVANT suppression
            $bannerId = $picture->banner_id;

            if ($bannerId) {
                // Compte les AUTRES photos (exclut lui-même)
                $otherPhotosCount = Photos::where('banner_id', $bannerId)
                    ->where('id', '!=', $picture->id)
                    ->count();

                if ($otherPhotosCount === 0) {
                    $directory = 'Banners/' . $bannerId;
                    Storage::disk('public')->exists($directory) &&
                        Storage::disk('public')->deleteDirectory($directory, true);
                }
            }
        });
    }





    public function getImageUrl(?int $width = null, ?int $height = null): string
    {
        // Image déjà hébergée via une URL absolue (CDN / source externe) :
        // on la renvoie telle quelle (le pipeline Glide/Storage ne s'applique
        // qu'aux fichiers locaux).
        $filename = (string) $this->filename;
        if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
            return $filename;
        }

        if ($width === null) {
            return Storage::disk('public')->url($this->filename);
        }
        $urlBuilder = UrlBuilderFactory::create('/images/', config('glide.key'));
        return $urlBuilder->getUrl($this->filename, ['w' => $width, 'h' => $height, 'fit' => 'crop']);
    }





    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function brands()
    {
        return $this->belongsTo(Brand::class);
    }
    public function produits()
    {
        return $this->belongsTo(Produits::class);
    }
    public function paiements()
    {
        return $this->belongsTo(Paiements::class);
    }
    public function livraison()
    {
        return $this->belongsTo(Livraison::class);
    }

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
    public function avisClient()
    {
        return $this->belongsTo(AvisClient::class);
    }
}
