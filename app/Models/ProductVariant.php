<?php

namespace App\Models;

use App\Models\Produits;
use App\Models\VariantOption;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['produit_id', 'sku', 'price', 'stock', 'weight'];

    public function produit()
    {
        return $this->belongsTo(Produits::class, 'produit_id');
    }

    public function options()
    {
        return $this->hasMany(VariantOption::class, 'product_variant_id');
    }
}
