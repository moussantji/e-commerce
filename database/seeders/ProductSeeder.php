<?php

namespace Database\Seeders;

use App\Models\Produits;
use App\Models\Categories;
use App\Models\Caracteristiques;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Dossier de stockage des images
        $imagePath = 'public/products';
        if (!Storage::exists($imagePath)) {
            Storage::makeDirectory($imagePath);
        }

        // Récupérer les catégories
        $categories = Categories::whereNotNull('parent_id')->get();

        // Récupérer les caractéristiques
        $caracteristiques = Caracteristiques::all();

        // Récupérer les tags
        $tags = Tag::all();

        // Produits électroniques
        $this->createElectronicProducts($faker, $categories, $caracteristiques, $tags);

        // Produits mode
        $this->createFashionProducts($faker, $categories, $caracteristiques, $tags);

        // Produits maison & jardin
        $this->createHomeAndGardenProducts($faker, $categories, $caracteristiques, $tags);

        $this->command->info('Produits créés avec succès !');
    }

    /**
     * Crée des produits électroniques.
     */
    protected function createElectronicProducts($faker, $categories, $caracteristiques, $tags)
    {
        $electronicCategory = $categories->where('name', 'Téléphones portables')->first();
        $laptopCategory = $categories->where('name', 'Ordinateurs portables')->first();

        if (!$electronicCategory || !$laptopCategory) return;

        // Téléphones
        $phones = [
            [
                'name' => 'iPhone 14 Pro Max',
                'description' => 'Le dernier iPhone avec écran Super Retina XDR et puce A16 Bionic.',
                'price' => 1329.00,
                'stock' => 50,
                'sku' => 'IPH14PM-256-SPACE',
                'images' => [
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-1inch-spaceblack?wid=5120&hei=2880&fmt=p-jpg&qlt=80&.v=1663703841896',
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-1inch-spaceblack_AV1?wid=2560&hei=1440&fmt=p-jpg&qlt=80&.v=1662159649801'
                ],
                'caracteristiques' => [
                    'Marque' => 'Apple',
                    'Couleur' => 'Noir Sidéral',
                    'Capacité de stockage' => '256 Go',
                    'Mémoire RAM' => '6 Go',
                    'Taille d\'écran' => '6.7"',
                    'Résolution d\'écran' => '2796x1290 pixels',
                ],
                'tags' => ['Nouveauté', 'Haut de gamme', 'Meilleure vente', 'Coup de cœur', 'Livraison gratuite'],
            ],
            [
                'name' => 'iPhone 14 Pro Max',
                'description' => 'Le dernier iPhone avec écran Super Retina XDR et puce A16 Bionic.',
                'price' => 1329.00,
                'stock' => 50,
                'sku' => 'IPH14PM-256-SPACE',
                'images' => [
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-1inch-spaceblack?wid=5120&hei=2880&fmt=p-jpg&qlt=80&.v=1663703841896',
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-1inch-spaceblack_AV1?wid=2560&hei=1440&fmt=p-jpg&qlt=80&.v=1662159649801'
                ],
                'caracteristiques' => [
                    'Marque' => 'Apple',
                    'Couleur' => 'Noir Sidéral',
                    'Capacité de stockage' => '256 Go',
                    'Mémoire RAM' => '6 Go',
                    'Taille d\'écran' => '6.7"',
                    'Résolution d\'écran' => '2796x1290 pixels',
                ],
                'tags' => ['Nouveauté', 'Haut de gamme', 'Meilleure vente', 'Coup de cœur', 'Livraison gratuite'],
            ],
            [
                'name' => 'iPhone 14 Pro Max',
                'description' => 'Le dernier iPhone avec écran Super Retina XDR et puce A16 Bionic.',
                'price' => 1329.00,
                'stock' => 50,
                'sku' => 'IPH14PM-256-SPACE',
                'images' => [
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-1inch-spaceblack?wid=5120&hei=2880&fmt=p-jpg&qlt=80&.v=1663703841896',
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-1inch-spaceblack_AV1?wid=2560&hei=1440&fmt=p-jpg&qlt=80&.v=1662159649801'
                ],
                'caracteristiques' => [
                    'Marque' => 'Apple',
                    'Couleur' => 'Noir Sidéral',
                    'Capacité de stockage' => '256 Go',
                    'Mémoire RAM' => '6 Go',
                    'Taille d\'écran' => '6.7"',
                    'Résolution d\'écran' => '2796x1290 pixels',
                ],
                'tags' => ['Nouveauté', 'Haut de gamme', 'Meilleure vente', 'Coup de cœur', 'Livraison gratuite'],
            ],
            // Ajoutez d'autres téléphones ici...
        ];

        // Ordinateurs portables
        $laptops = [
            [
                'name' => 'MacBook Pro 16" M2 Max',
                'description' => 'Ordinateur portable professionnel avec écran Liquid Retina XDR et puce M2 Max.',
                'price' => 3899.00,
                'stock' => 30,
                'sku' => 'MBP16-M2MAX-1TB-SPACE',
                'images' => [
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mbp16-spacegray-select-202301?wid=452&hei=420&fmt=jpeg&qlt=95&.v=1671304673202',
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mbp16-spacegray-select-202301_GEO_FR?wid=452&hei=420&fmt=jpeg&qlt=95&.v=1670716930635'
                ],
                'caracteristiques' => [
                    'Marque' => 'Apple',
                    'Couleur' => 'Gris Sidéral',
                    'Capacité de stockage' => '1 To',
                    'Mémoire RAM' => '32 Go',
                    'Taille d\'écran' => '16.2"',
                    'Résolution d\'écran' => '3456x2234 pixels',
                ],
                'tags' => ['Nouveauté', 'Haut de gamme', 'Professionnel', 'Livraison gratuite'],
            ],
            [
                'name' => 'MacBook Pro 16" M2 Max',
                'description' => 'Ordinateur portable professionnel avec écran Liquid Retina XDR et puce M2 Max.',
                'price' => 3899.00,
                'stock' => 30,
                'sku' => 'MBP16-M2MAX-1TB-SPACE',
                'images' => [
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mbp16-spacegray-select-202301?wid=452&hei=420&fmt=jpeg&qlt=95&.v=1671304673202',
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mbp16-spacegray-select-202301_GEO_FR?wid=452&hei=420&fmt=jpeg&qlt=95&.v=1670716930635'
                ],
                'caracteristiques' => [
                    'Marque' => 'Apple',
                    'Couleur' => 'Gris Sidéral',
                    'Capacité de stockage' => '1 To',
                    'Mémoire RAM' => '32 Go',
                    'Taille d\'écran' => '16.2"',
                    'Résolution d\'écran' => '3456x2234 pixels',
                ],
                'tags' => ['Nouveauté', 'Haut de gamme', 'Professionnel', 'Livraison gratuite'],
            ],
            [
                'name' => 'MacBook Pro 16" M2 Max',
                'description' => 'Ordinateur portable professionnel avec écran Liquid Retina XDR et puce M2 Max.',
                'price' => 3899.00,
                'stock' => 30,
                'sku' => 'MBP16-M2MAX-1TB-SPACE',
                'images' => [
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mbp16-spacegray-select-202301?wid=452&hei=420&fmt=jpeg&qlt=95&.v=1671304673202',
                    'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mbp16-spacegray-select-202301_GEO_FR?wid=452&hei=420&fmt=jpeg&qlt=95&.v=1670716930635'
                ],
                'caracteristiques' => [
                    'Marque' => 'Apple',
                    'Couleur' => 'Gris Sidéral',
                    'Capacité de stockage' => '1 To',
                    'Mémoire RAM' => '32 Go',
                    'Taille d\'écran' => '16.2"',
                    'Résolution d\'écran' => '3456x2234 pixels',
                ],
                'tags' => ['Nouveauté', 'Haut de gamme', 'Professionnel', 'Livraison gratuite'],
            ],
            // Ajoutez d'autres ordinateurs portables ici...
        ];

        // Créer les produits électroniques
        $this->createProducts($faker, $electronicCategory, $phones, $caracteristiques, $tags);
        $this->createProducts($faker, $laptopCategory, $laptops, $caracteristiques, $tags);
    }

    /**
     * Crée des produits de mode.
     */
    protected function createFashionProducts($faker, $categories, $caracteristiques, $tags)
    {
        $menCategory = $categories->where('name', 'Vêtements Homme')->first();
        $womenCategory = $categories->where('name', 'Vêtements Femme')->first();

        if (!$menCategory || !$womenCategory) return;

        // Vêtements Homme
        $menClothes = [
            [
                'name' => 'Jean Slim Noir',
                'description' => 'Jean slim noir élégant et confortable pour un look décontracté ou habillé.',
                'price' => 79.99,
                'stock' => 100,
                'sku' => 'JEAN-SLIM-NOIR-32',
                'images' => [
                    'https://static.zara.net/photos///2023/I/0/1/p/2517/410/800/2/w/1920/2517410800_1_1_1.jpg?ts=1685000000000',
                    'https://static.zara.net/photos///2023/I/0/1/p/2517/410/800/2/w/1920/2517410800_2_1_1.jpg?ts=1685000000001'
                ],
                'caracteristiques' => [
                    'Marque' => 'Zara',
                    'Couleur' => 'Noir',
                    'Taille' => ['S', 'M', 'L', 'XL'],
                    'Matière' => 'Coton',
                    'Sexe' => 'Homme',
                ],
                'tags' => ['Tendance', 'Décontracté', 'Soldes', 'Livraison gratuite'],
            ],
            // Ajoutez d'autres vêtements homme ici...
        ];

        // Vêtements Femme
        $womenClothes = [
            [
                'name' => 'Robe d\'été fleurie',
                'description' => 'Robe légère et fluide à motifs fleuris pour un look estival élégant.',
                'price' => 59.99,
                'stock' => 75,
                'sku' => 'ROBE-FLEURIE-SUMMER',
                'images' => [
                    'https://static.zara.net/photos///2023/V/0/1/p/1234/567/800/2/w/1920/1234567800_1_1_1.jpg?ts=1685000000000',
                    'https://static.zara.net/photos///2023/V/0/1/p/1234/567/800/2/w/1920/1234567800_2_1_1.jpg?ts=1685000000001'
                ],
                'caracteristiques' => [
                    'Marque' => 'Zara',
                    'Couleur' => 'Multicolore',
                    'Taille' => ['XS', 'S', 'M', 'L'],
                    'Matière' => 'Viscose',
                    'Sexe' => 'Femme',
                ],
                'tags' => ['Tendance', 'Été', 'Coup de cœur', 'Livraison gratuite'],
            ],
            // Ajoutez d'autres vêtements femme ici...
        ];

        // Créer les produits de mode
        $this->createProducts($faker, $menCategory, $menClothes, $caracteristiques, $tags);
        $this->createProducts($faker, $womenCategory, $womenClothes, $caracteristiques, $tags);
    }

    /**
     * Crée des produits pour la maison et le jardin.
     */
    protected function createHomeAndGardenProducts($faker, $categories, $caracteristiques, $tags)
    {
        $livingRoomCategory = $categories->where('name', 'Meubles de salon')->first();
        $gardenCategory = $categories->where('name', 'Jardinage')->first();

        if (!$livingRoomCategory || !$gardenCategory) return;

        // Meubles de salon
        $furniture = [
            [
                'name' => 'Canapé 3 places en tissu gris',
                'description' => 'Canapé 3 places moderne et confortable en tissu gris anthracite.',
                'price' => 799.99,
                'stock' => 15,
                'sku' => 'CANAPE-GRIS-3P',
                'images' => [
                    'https://www.ikea.com/fr/fr/images/products/kivik-canape-3-places-knisa-gris-clair__1051387_pe845701_s5.jpg?f=s',
                    'https://www.ikea.com/fr/fr/images/products/kivik-canape-3-places-knisa-gris-clair__1051388_pe845700_s5.jpg?f=s'
                ],
                'caracteristiques' => [
                    'Marque' => 'IKEA',
                    'Couleur' => 'Gris',
                    'Matériau' => 'Tissu',
                    'Couleur du tissu' => 'Gris',
                    'Poids' => '45',
                ],
                'tags' => ['Design', 'Moderne', 'Confortable', 'Livraison gratuite'],
            ],
            // Ajoutez d'autres meubles ici...
        ];

        // Articles de jardinage
        $gardenItems = [
            [
                'name' => 'Ensemble de jardin en teck 6 places',
                'description' => 'Table de jardin rectangulaire et 6 chaises en teck massif traité autoclave.',
                'price' => 1499.99,
                'stock' => 8,
                'sku' => 'JARDIN-TECK-6P',
                'images' => [
                    'https://www.leroymerlin.fr/multimedia/1fbe0100e5a738f1f5b2d9f0f0e3a5b8/Ensemble-de-jardin-en-teck-6-places-avec-table-et-bancs.jpg',
                    'https://www.leroymerlin.fr/multimedia/9c1e0100e5a738f1f5b2d9f0f0e3a5b8/Ensemble-de-jardin-en-teck-6-places-avec-table-et-bancs.jpg'
                ],
                'caracteristiques' => [
                    'Marque' => 'Leroy Merlin',
                    'Matériau' => 'Bois massif',
                    'Couleur' => 'Naturel',
                    'Poids' => '120',
                ],
                'tags' => ['Extérieur', 'Bois massif', 'Résistant aux intempéries', 'Livraison gratuite'],
            ],
            // Ajoutez d'autres articles de jardinage ici...
        ];

        // Créer les produits maison & jardin
        $this->createProducts($faker, $livingRoomCategory, $furniture, $caracteristiques, $tags);
        $this->createProducts($faker, $gardenCategory, $gardenItems, $caracteristiques, $tags);
    }

    /**
     * Crée des produits à partir d'un tableau de données.
     */
    protected function createProducts($faker, $category, $products, $caracteristiques, $allTags)
    {
        foreach ($products as $productData) {
            // Télécharger les images
            $imagePaths = [];
            foreach ($productData['images'] as $imageUrl) {
                $imageName = Str::slug($productData['name']) . '-' . uniqid() . '.jpg';
                $imagePath = 'products/' . $imageName;

                if (!Storage::exists('public/' . $imagePath)) {
                    $contents = @file_get_contents($imageUrl);
                    if ($contents !== false) {
                        Storage::put('public/' . $imagePath, $contents);
                        $imagePaths[] = $imagePath;
                    }
                } else {
                    $imagePaths[] = $imagePath;
                }
            }

            // Définir le prix en FCFA (entre 2000 et 500000 FCFA)
            $prix = $faker->numberBetween(2000, 500000);
            $prix_promotionnel = $faker->optional(0.3)->numberBetween(1000, $prix - 1000);

            // Créer le produit
            $product = Produits::create([
                'name' => $productData['name'],
                'description' => $faker->paragraphs(3, true),
                'price' => $prix / 100, // Convertir en décimal pour le stockage
                'sale_price' => ($prix / 100) - 50,
                'stock' => $faker->numberBetween(0, 500),
                'category_id' => $category->id,
                'images' => $imagePaths,
                'is_active' => true
            ]);

            // Ajouter les caractéristiques
            $characteristicData = [];
            if (isset($productData['caracteristiques'])) {
                foreach ($productData['caracteristiques'] as $charName => $value) {
                    $characteristic = $caracteristiques->firstWhere('name', $charName);

                    if ($characteristic) {
                        if (is_array($value)) {
                            // Pour les caractéristiques à valeurs multiples (comme les tailles)
                            foreach ($value as $val) {
                                $characteristicData[$characteristic->id] = ['value' => $val];
                            }
                        } else {
                            $characteristicData[$characteristic->id] = ['value' => $value];
                        }
                    }
                }

                $product->caracteristiques()->sync($characteristicData);
            }

            // Ajouter les tags
            if (isset($productData['tags'])) {
                $tagIds = $allTags->whereIn('name', $productData['tags'])->pluck('id')->toArray();
                $product->tags()->sync($tagIds);
            }
        }
    }
}
