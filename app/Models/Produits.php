<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\photos as Photos;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produits extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'sale_price',
        'stock',
        'category_id',
        'brand_id',
        'image',
        'images',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array'
    ];

    /**
     * Récupère la marque associée au produit
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function caracteristiques(): BelongsToMany
    {
        return $this->belongsToMany(Caracteristiques::class, 'produit_caracteristique')
            ->withPivot('value');
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Paniers::class, 'panier_produit')
            ->withPivot(['quantite', 'prix_unitaire', 'total_ligne']);
    }

    // Produit
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
                $filename = $file->store('Produits/' . $this->id, 'public');
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
        return $this->photos()->where('produits_id', $this->id)->first();
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Commandes::class, 'commande_produit')
            ->withPivot(['quantity', 'unit_price', 'subtotal']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AvisClient::class, 'produits_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag', 'produit_id', 'tag_id');
    }

    // Alias pour la relation commandes (pour la rétrocompatibilité)
    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commandes::class, 'commande_produit', 'produit_id', 'commande_id')
            ->withPivot(['quantite', 'prix_unitaire', 'total']);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getSlug()
    {
        return Str::slug($this->name);
    }

    public function couleurs()
    {
        return $this->belongsToMany(Caracteristiques::class, 'produit_caracteristique')
            ->where('caracteristiques.type', 'couleur')
            ->withPivot('value');
    }

    public function getColorsCountAttribute()
    {
        return $this->couleurs()->count();
    }

    // Ajoutez cette relation
    public function avisClients()
    {
        return $this->hasMany(AvisClient::class, 'produits_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->avisClients()->avg('nb_etoiles') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->avisClients()->count();
    }

    public function wishlistUsers()
    {
        return $this->belongsToMany(User::class, 'wishlist_user_produit', 'produits_id', 'user_id');
    }

    /**
     * Get the formatted price in CFA.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format((float) $this->price, 0, ',', ' ') . ' FCFA';
    }
}
