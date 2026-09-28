<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeProduit extends Model
{
    use HasFactory;

    protected $table = 'commande_produit';
    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'total',
        'options'
    ];

    public function commande()
    {
        return $this->belongsTo(Commandes::class, 'commande_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produits::class, 'produit_id');
    }
}
