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
    protected $fillable = ['filename', 'user_id', 'produit_id', 'payment_id', 'livraison_id','brand_id'];

    protected static function booted(): void
    {
        static::deleting(function (Photos $picture) {
            Storage::disk('public')->delete($picture->filename);
        });
    }



    public function getImageUrl(?int $width = null, ?int $height = null): string
    {
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
    public function brands() {
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
}
