<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Caracteristiques;
use App\Models\Categories;
use App\Models\Produits;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

/**
 * Génère un catalogue prêt pour la production : ~1000 produits réalistes
 * répartis dans les catégories existantes, avec marques, tags, caractéristiques,
 * prix en FCFA et PHOTOS.
 *
 * IMPORTANT — Images :
 *   Les photos utilisées sont des visuels LIBRES DE DROITS servis par mot-clé
 *   (loremflickr.com). Elles conviennent pour une mise en ligne / démo, mais
 *   pour un vrai lancement vous devez les remplacer par VOS propres photos
 *   (ou des images sous licence). Aucune image de marque tierce (Shein, etc.)
 *   n'est utilisée : ce serait une violation de droits d'auteur.
 *
 *   Pour remplacer les images : mettez à jour la colonne `filename` des
 *   enregistrements `photos` (elle accepte désormais une URL absolue) ou
 *   uploadez vos fichiers via l'admin.
 */
class ProductionCatalogSeeder extends Seeder
{
    /** Nombre total de produits à générer. */
    private int $target = 1000;

    /**
     * Config par catégorie : mot-clé image + fourchette de prix (FCFA).
     * clé = nom exact de la catégorie enfant.
     */
    private array $config = [
        'Smartphones'               => ['smartphone,phone', 45000, 900000],
        'Ordinateurs portables'     => ['laptop,computer', 150000, 1500000],
        'Audio & Hi-Fi'             => ['headphones,speaker', 8000, 250000],
        'TV & Home cinéma'          => ['television,tv', 90000, 1200000],
        'Vêtements Homme'           => ['menswear,shirt', 3000, 45000],
        'Vêtements Femme'           => ['dress,fashion', 3000, 55000],
        'Chaussures'                => ['shoes,sneakers', 5000, 80000],
        'Accessoires'               => ['accessories,watch', 2000, 40000],
        'Meubles'                   => ['furniture,sofa', 25000, 600000],
        'Décoration'                => ['home-decor,interior', 3000, 90000],
        'Bricolage'                 => ['tools,drill', 3000, 150000],
        'Jardinage'                 => ['gardening,plant', 3000, 120000],
        'Soins du visage'           => ['skincare,cream', 3000, 45000],
        'Maquillage'                => ['makeup,cosmetics', 2000, 40000],
        'Parfums'                   => ['perfume,fragrance', 8000, 90000],
        'Fitness & Musculation'     => ['fitness,gym', 5000, 250000],
        'Camping & Outdoor'         => ['camping,tent', 8000, 300000],
        'Cyclisme'                  => ['bicycle,bike', 25000, 900000],
        'Poussettes & Sièges auto'  => ['stroller,baby', 30000, 350000],
        'Jouets'                    => ['toys,kids', 2000, 60000],
        'Vêtements bébé'            => ['baby-clothes,baby', 2000, 25000],
        'Épicerie salée'            => ['grocery,food', 500, 15000],
        'Boissons'                  => ['drinks,beverage', 500, 20000],
        'Produits frais'            => ['food,vegetables', 500, 20000],
        'Pièces détachées'          => ['car-parts,engine', 3000, 300000],
        'Entretien auto'            => ['car-care,car', 2000, 60000],
        'Équipements moto'          => ['motorcycle,helmet', 8000, 250000],
    ];

    private array $adjectifs = [
        'Premium', 'Classique', 'Édition Limitée', 'Confort', 'Pro',
        'Éco', 'Élégant', 'Compact', 'Ultra', 'Deluxe', 'Essentiel',
        'Nouvelle Génération', 'Signature', 'Sport', 'Urbain',
    ];

    private array $couleurs = [
        'Noir', 'Blanc', 'Bleu', 'Rouge', 'Vert', 'Gris', 'Beige',
        'Rose', 'Doré', 'Argent', 'Marine', 'Kaki',
    ];

    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        $categories = Categories::whereNotNull('parent_id')->get();
        if ($categories->isEmpty()) {
            $this->command->warn('Aucune catégorie enfant : lancez CategorySeeder d\'abord.');
            return;
        }

        $brands = Brand::all();
        $caracteristiques = Caracteristiques::all();
        $tags = Tag::all();

        // Idempotence : on complète jusqu'à la cible sans jamais la dépasser
        $existing = Produits::count();
        if ($existing >= $this->target) {
            $this->command->info("Catalogue déjà rempli ({$existing} produits) — rien à générer.");
            return;
        }
        $toCreate = $this->target - $existing;

        $created = 0;
        $index = 0;

        // Répartition circulaire sur les catégories jusqu'à atteindre la cible
        while ($created < $toCreate) {
            $category = $categories[$index % $categories->count()];
            $index++;

            [$keyword, $minPrice, $maxPrice] = $this->config[$category->name]
                ?? ['product,shop', 2000, 100000];

            $adjectif = $faker->randomElement($this->adjectifs);
            $couleur = $faker->randomElement($this->couleurs);
            $modelToken = strtoupper(Str::random(3)) . $faker->numberBetween(100, 999);

            $baseName = Str::singular($category->name);
            $name = trim("{$baseName} {$adjectif} {$couleur}");
            $sku = strtoupper(Str::slug(Str::limit($category->name, 8, ''))) . '-' . $modelToken . '-' . $created;

            if (Produits::where('sku', $sku)->exists()) {
                continue;
            }

            $price = $faker->numberBetween($minPrice, $maxPrice);
            // ~35% des produits en promo
            $salePrice = $faker->boolean(35)
                ? (int) round($price * $faker->randomFloat(2, 0.6, 0.9))
                : null;

            $product = Produits::create([
                'name' => $name,
                'description' => $this->description($faker, $baseName, $adjectif, $couleur),
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => $faker->numberBetween(0, 400),
                'sku' => $sku,
                'category_id' => $category->id,
                'image' => null,
                'images' => null,
                'is_active' => true,
            ]);

            // brand_id et is_featured ne sont pas "fillable" → écriture directe
            if ($brands->isNotEmpty()) {
                $product->brand_id = $brands->random()->id;
            }
            $product->is_featured = $faker->boolean(15);
            $product->saveQuietly();

            // Photos libres de droits, par mot-clé, stables (lock)
            for ($p = 1; $p <= 3; $p++) {
                $lock = ($product->id * 10) + $p;
                $product->photos()->create([
                    'filename' => "https://loremflickr.com/600/600/{$keyword}?lock={$lock}",
                ]);
            }

            // Caractéristiques (3 à 5 au hasard, avec valeurs plausibles)
            if ($caracteristiques->isNotEmpty()) {
                $picked = $caracteristiques->random(min($caracteristiques->count(), $faker->numberBetween(3, 5)));
                $sync = [];
                foreach ($picked as $c) {
                    $sync[$c->id] = ['value' => $this->caracValue($faker, $c->name, $couleur)];
                }
                $product->caracteristiques()->sync($sync);
            }

            // Tags (2 à 4 au hasard)
            if ($tags->isNotEmpty()) {
                $tagIds = $tags->random(min($tags->count(), $faker->numberBetween(2, 4)))->pluck('id')->toArray();
                $product->tags()->sync($tagIds);
            }

            $created++;
            if ($created % 100 === 0) {
                $this->command->info("… {$created} produits générés");
            }
        }

        $this->command->info("Catalogue de production : {$created} produits créés (avec photos).");
    }

    private function description($faker, string $noun, string $adjectif, string $couleur): string
    {
        return sprintf(
            '%s %s de coloris %s. %s Livraison rapide, qualité garantie et retours simplifiés.',
            $noun,
            strtolower($adjectif),
            strtolower($couleur),
            $faker->sentence(12),
        );
    }

    private function caracValue($faker, string $name, string $couleur): string
    {
        $n = Str::lower($name);

        return match (true) {
            str_contains($n, 'couleur') => $couleur,
            str_contains($n, 'taille') || str_contains($n, 'écran') => $faker->randomElement(['S', 'M', 'L', 'XL', '6.1"', '13"', '15"', '55"']),
            str_contains($n, 'stockage') || str_contains($n, 'capacité') => $faker->randomElement(['64 Go', '128 Go', '256 Go', '512 Go', '1 To']),
            str_contains($n, 'ram') || str_contains($n, 'mémoire') => $faker->randomElement(['4 Go', '8 Go', '16 Go', '32 Go']),
            str_contains($n, 'autonomie') => $faker->numberBetween(8, 40) . ' heures',
            str_contains($n, 'poids') => $faker->randomFloat(2, 0.1, 5) . ' kg',
            str_contains($n, 'marque') => $faker->company(),
            str_contains($n, 'matière') || str_contains($n, 'matériau') => $faker->randomElement(['Coton', 'Cuir', 'Polyester', 'Aluminium', 'Bois', 'Verre']),
            default => Str::ucfirst($faker->word()),
        };
    }
}
