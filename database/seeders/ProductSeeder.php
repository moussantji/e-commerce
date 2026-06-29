<?php

namespace Database\Seeders;

use App\Models\Produits;
use App\Models\Categories;
use App\Models\Caracteristiques;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Récupérer les catégories enfants
        $categories = Categories::whereNotNull('parent_id')->get();

        // Récupérer les caractéristiques
        $caracteristiques = Caracteristiques::all();

        // Récupérer les tags
        $tags = Tag::all();

        $this->createElectronicProducts($faker, $categories, $caracteristiques, $tags);
        $this->createFashionProducts($faker, $categories, $caracteristiques, $tags);
        $this->createHomeAndGardenProducts($faker, $categories, $caracteristiques, $tags);
        $this->createBeautyProducts($faker, $categories, $caracteristiques, $tags);
        $this->createSportOutdoorProducts($faker, $categories, $caracteristiques, $tags);
        $this->createKidsProducts($faker, $categories, $caracteristiques, $tags);
        $this->createFoodProducts($faker, $categories, $caracteristiques, $tags);
        $this->createAutoProducts($faker, $categories, $caracteristiques, $tags);

        $this->command->info('Produits créés avec succès !');
    }

    /**
     * Crée des produits électroniques.
     */
    protected function createElectronicProducts($faker, $categories, $caracteristiques, $tags)
    {
        $phoneCategory = $categories->where('name', 'Smartphones')->first();
        $laptopCategory = $categories->where('name', 'Ordinateurs portables')->first();
        $audioCategory = $categories->where('name', 'Audio & Hi-Fi')->first();
        $tvCategory = $categories->where('name', 'TV & Home cinéma')->first();

        if ($phoneCategory) {
            $phones = [
                [
                    'name' => 'iPhone 14 Pro Max',
                    'description' => 'Smartphone haut de gamme avec écran Super Retina XDR et puce A16 Bionic.',
                    'price' => 1329.00,
                    'stock' => 50,
                    'sku' => 'IPH14PM-256-SPACE',
                    'caracteristiques' => [
                        'Marque' => 'Apple',
                        'Couleur' => 'Noir Sidéral',
                        'Capacité de stockage' => '256 Go',
                        'Mémoire RAM' => '6 Go',
                        'Taille d\'écran' => '6.7"',
                    ],
                    'tags' => ['Nouveauté', 'Haut de gamme', 'Meilleure vente'],
                ],
                [
                    'name' => 'Samsung Galaxy S23 Ultra',
                    'description' => 'Smartphone Android premium avec appareil photo 200 MP et grand écran.',
                    'price' => 1249.00,
                    'stock' => 40,
                    'sku' => 'SGS23U-256-BLACK',
                    'caracteristiques' => [
                        'Marque' => 'Samsung',
                        'Couleur' => 'Noir',
                        'Capacité de stockage' => '256 Go',
                        'Mémoire RAM' => '12 Go',
                        'Taille d\'écran' => '6.8"',
                    ],
                    'tags' => ['Best seller', 'Android', 'Photo'],
                ],
            ];

            $this->createProducts($faker, $phoneCategory, $phones, $caracteristiques, $tags);
        }

        if ($laptopCategory) {
            $laptops = [
                [
                    'name' => 'MacBook Pro 16" M2 Max',
                    'description' => 'Ordinateur portable professionnel avec écran Liquid Retina XDR et puce M2 Max.',
                    'price' => 3899.00,
                    'stock' => 30,
                    'sku' => 'MBP16-M2MAX-1TB',
                    'caracteristiques' => [
                        'Marque' => 'Apple',
                        'Couleur' => 'Gris Sidéral',
                        'Capacité de stockage' => '1 To',
                        'Mémoire RAM' => '32 Go',
                        'Taille d\'écran' => '16.2"',
                    ],
                    'tags' => ['Professionnel', 'Haut de gamme'],
                ],
                [
                    'name' => 'Dell XPS 13',
                    'description' => 'Ultrabook compact et performant idéal pour le bureau et les voyages.',
                    'price' => 1399.00,
                    'stock' => 25,
                    'sku' => 'XPS13-512GB-SILVER',
                    'caracteristiques' => [
                        'Marque' => 'Dell',
                        'Couleur' => 'Argent',
                        'Capacité de stockage' => '512 Go',
                        'Mémoire RAM' => '16 Go',
                        'Taille d\'écran' => '13.4"',
                    ],
                    'tags' => ['Portable', 'Design', 'Performance'],
                ],
            ];

            $this->createProducts($faker, $laptopCategory, $laptops, $caracteristiques, $tags);
        }

        if ($audioCategory) {
            $audio = [
                [
                    'name' => 'Casque sans fil Bluetooth',
                    'description' => 'Casque audio confortable avec réduction de bruit active.',
                    'price' => 199.99,
                    'stock' => 80,
                    'sku' => 'HEAD-BT-NC',
                    'caracteristiques' => [
                        'Marque' => 'Sony',
                        'Couleur' => 'Noir',
                        'Autonomie' => '30 heures',
                    ],
                    'tags' => ['Musique', 'Portable', 'Bluetooth'],
                ],
            ];

            $this->createProducts($faker, $audioCategory, $audio, $caracteristiques, $tags);
        }

        if ($tvCategory) {
            $tv = [
                [
                    'name' => 'TV 55" 4K UHD',
                    'description' => 'Téléviseur 4K avec Smart TV et compatibilité HDR.',
                    'price' => 899.99,
                    'stock' => 20,
                    'sku' => 'TV-55-4K',
                    'caracteristiques' => [
                        'Marque' => 'LG',
                        'Taille d\'écran' => '55"',
                        'Résolution' => '3840x2160',
                    ],
                    'tags' => ['Cinéma', 'Smart TV', '4K'],
                ],
            ];

            $this->createProducts($faker, $tvCategory, $tv, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits de mode.
     */
    protected function createFashionProducts($faker, $categories, $caracteristiques, $tags)
    {
        $menCategory = $categories->where('name', 'Vêtements Homme')->first();
        $womenCategory = $categories->where('name', 'Vêtements Femme')->first();
        $shoesCategory = $categories->where('name', 'Chaussures')->first();
        $accessoriesCategory = $categories->where('name', 'Accessoires')->first();

        if ($menCategory) {
            $menClothes = [
                [
                    'name' => 'Jean Slim Noir',
                    'description' => 'Jean slim noir élégant et confortable pour un look décontracté ou habillé.',
                    'price' => 79.99,
                    'stock' => 100,
                    'sku' => 'JEAN-SLIM-NOIR-32',
                    'caracteristiques' => [
                        'Marque' => 'Zara',
                        'Couleur' => 'Noir',
                        'Taille' => ['S', 'M', 'L', 'XL'],
                        'Matière' => 'Coton',
                        'Sexe' => 'Homme',
                    ],
                    'tags' => ['Tendance', 'Décontracté'],
                ],
            ];

            $this->createProducts($faker, $menCategory, $menClothes, $caracteristiques, $tags);
        }

        if ($womenCategory) {
            $womenClothes = [
                [
                    'name' => 'Robe d\'été fleurie',
                    'description' => 'Robe légère et fluide à motifs fleuris pour un look estival élégant.',
                    'price' => 59.99,
                    'stock' => 75,
                    'sku' => 'ROBE-FLEURIE-SUMMER',
                    'caracteristiques' => [
                        'Marque' => 'Zara',
                        'Couleur' => 'Multicolore',
                        'Taille' => ['XS', 'S', 'M', 'L'],
                        'Matière' => 'Viscose',
                        'Sexe' => 'Femme',
                    ],
                    'tags' => ['Été', 'Coup de cœur'],
                ],
            ];

            $this->createProducts($faker, $womenCategory, $womenClothes, $caracteristiques, $tags);
        }

        if ($shoesCategory) {
            $shoes = [
                [
                    'name' => 'Sneakers blanches',
                    'description' => 'Sneakers blanches basses, idéales pour un style casual.',
                    'price' => 89.99,
                    'stock' => 60,
                    'sku' => 'SNEAK-WHITE-42',
                    'caracteristiques' => [
                        'Marque' => 'Nike',
                        'Couleur' => 'Blanc',
                        'Taille' => ['40', '41', '42', '43', '44'],
                    ],
                    'tags' => ['Sport', 'Streetwear'],
                ],
            ];

            $this->createProducts($faker, $shoesCategory, $shoes, $caracteristiques, $tags);
        }

        if ($accessoriesCategory) {
            $accessories = [
                [
                    'name' => 'Sac à main en cuir',
                    'description' => 'Sac à main en cuir véritable avec bandoulière ajustable.',
                    'price' => 129.99,
                    'stock' => 40,
                    'sku' => 'SAC-CUIR-BLACK',
                    'caracteristiques' => [
                        'Marque' => 'Mango',
                        'Couleur' => 'Noir',
                        'Matériau' => 'Cuir',
                    ],
                    'tags' => ['Fashion', 'Cuir'],
                ],
            ];

            $this->createProducts($faker, $accessoriesCategory, $accessories, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits pour la maison et le jardin.
     */
    protected function createHomeAndGardenProducts($faker, $categories, $caracteristiques, $tags)
    {
        $furnitureCategory = $categories->where('name', 'Meubles')->first();
        $decorationCategory = $categories->where('name', 'Décoration')->first();
        $diyCategory = $categories->where('name', 'Bricolage')->first();
        $gardenCategory = $categories->where('name', 'Jardinage')->first();

        if ($furnitureCategory) {
            $furniture = [
                [
                    'name' => 'Canapé 3 places en tissu gris',
                    'description' => 'Canapé moderne et confortable en tissu gris anthracite.',
                    'price' => 799.99,
                    'stock' => 15,
                    'sku' => 'CANAPE-GRIS-3P',
                    'caracteristiques' => [
                        'Marque' => 'IKEA',
                        'Couleur' => 'Gris',
                        'Matériau' => 'Tissu',
                    ],
                    'tags' => ['Design', 'Confort'],
                ],
            ];

            $this->createProducts($faker, $furnitureCategory, $furniture, $caracteristiques, $tags);
        }

        if ($decorationCategory) {
            $decoration = [
                [
                    'name' => 'Lampe sur pied design',
                    'description' => 'Lampe sur pied moderne avec abat-jour en tissu.',
                    'price' => 119.99,
                    'stock' => 35,
                    'sku' => 'LAMPE-PIED-DESIGN',
                    'caracteristiques' => [
                        'Marque' => 'IKEA',
                        'Couleur' => 'Blanc',
                        'Matériau' => 'Métal',
                    ],
                    'tags' => ['Ambiance', 'Décoration'],
                ],
            ];

            $this->createProducts($faker, $decorationCategory, $decoration, $caracteristiques, $tags);
        }

        if ($diyCategory) {
            $diy = [
                [
                    'name' => 'Perceuse sans fil 18V',
                    'description' => 'Perceuse-visseuse sans fil avec deux batteries et mallette.',
                    'price' => 149.99,
                    'stock' => 50,
                    'sku' => 'DRILL-18V-SET',
                    'caracteristiques' => [
                        'Marque' => 'Bosch',
                        'Tension' => '18V',
                        'Puissance' => '60Nm',
                    ],
                    'tags' => ['Bricolage', 'Équipement'],
                ],
            ];

            $this->createProducts($faker, $diyCategory, $diy, $caracteristiques, $tags);
        }

        if ($gardenCategory) {
            $gardenItems = [
                [
                    'name' => 'Tondeuse électrique 1400W',
                    'description' => 'Tondeuse légère avec bac de ramassage de grande capacité.',
                    'price' => 229.99,
                    'stock' => 20,
                    'sku' => 'TONDEUSE-1400W',
                    'caracteristiques' => [
                        'Marque' => 'Black+Decker',
                        'Puissance' => '1400W',
                    ],
                    'tags' => ['Jardin', 'Entretien'],
                ],
            ];

            $this->createProducts($faker, $gardenCategory, $gardenItems, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits pour beauté et santé.
     */
    protected function createBeautyProducts($faker, $categories, $caracteristiques, $tags)
    {
        $faceCategory = $categories->where('name', 'Soins du visage')->first();
        $makeupCategory = $categories->where('name', 'Maquillage')->first();
        $perfumeCategory = $categories->where('name', 'Parfums')->first();

        if ($faceCategory) {
            $faceProducts = [
                [
                    'name' => 'Crème hydratante anti-âge',
                    'description' => 'Soin visage hydratant et protecteur pour peau normale à sèche.',
                    'price' => 29.99,
                    'stock' => 120,
                    'sku' => 'CREME-ANTIAGE-50ML',
                    'caracteristiques' => [
                        'Marque' => 'Nivea',
                        'Volume' => '50 ml',
                    ],
                    'tags' => ['Soins', 'Hydratation'],
                ],
            ];

            $this->createProducts($faker, $faceCategory, $faceProducts, $caracteristiques, $tags);
        }

        if ($makeupCategory) {
            $makeupProducts = [
                [
                    'name' => 'Palette de maquillage 12 couleurs',
                    'description' => 'Palette complète de fards à paupières et blush.',
                    'price' => 24.99,
                    'stock' => 90,
                    'sku' => 'PALETTE-MAQ-12',
                    'caracteristiques' => [
                        'Marque' => 'NYX',
                        'Couleur' => 'Multicolore',
                    ],
                    'tags' => ['Maquillage', 'Cadeau'],
                ],
            ];

            $this->createProducts($faker, $makeupCategory, $makeupProducts, $caracteristiques, $tags);
        }

        if ($perfumeCategory) {
            $perfumes = [
                [
                    'name' => 'Eau de parfum floral',
                    'description' => 'Parfum féminin aux notes florales et fruitées.',
                    'price' => 59.99,
                    'stock' => 70,
                    'sku' => 'PARFUM-FLEUR-50',
                    'caracteristiques' => [
                        'Marque' => 'Lancôme',
                        'Volume' => '50 ml',
                    ],
                    'tags' => ['Parfum', 'Femme'],
                ],
            ];

            $this->createProducts($faker, $perfumeCategory, $perfumes, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits sport et outdoor.
     */
    protected function createSportOutdoorProducts($faker, $categories, $caracteristiques, $tags)
    {
        $fitnessCategory = $categories->where('name', 'Fitness & Musculation')->first();
        $campingCategory = $categories->where('name', 'Camping & Outdoor')->first();
        $cyclingCategory = $categories->where('name', 'Cyclisme')->first();

        if ($fitnessCategory) {
            $fitnessProducts = [
                [
                    'name' => 'Tapis de yoga antidérapant',
                    'description' => 'Tapis de yoga épais et confortable pour toutes les pratiques.',
                    'price' => 39.99,
                    'stock' => 100,
                    'sku' => 'YOGA-TAPIS-6MM',
                    'caracteristiques' => [
                        'Marque' => 'Decathlon',
                        'Épaisseur' => '6 mm',
                    ],
                    'tags' => ['Fitness', 'Yoga'],
                ],
            ];

            $this->createProducts($faker, $fitnessCategory, $fitnessProducts, $caracteristiques, $tags);
        }

        if ($campingCategory) {
            $campingProducts = [
                [
                    'name' => 'Tente 4 places imperméable',
                    'description' => 'Tente familiale facile à monter et résistante à la pluie.',
                    'price' => 129.99,
                    'stock' => 25,
                    'sku' => 'TENTE-4P',
                    'caracteristiques' => [
                        'Marque' => 'Quechua',
                        'Capacité' => '4 personnes',
                    ],
                    'tags' => ['Camping', 'Outdoor'],
                ],
            ];

            $this->createProducts($faker, $campingCategory, $campingProducts, $caracteristiques, $tags);
        }

        if ($cyclingCategory) {
            $cyclingProducts = [
                [
                    'name' => 'Casque de vélo urbain',
                    'description' => 'Casque léger avec visière et ajustement facile.',
                    'price' => 59.99,
                    'stock' => 45,
                    'sku' => 'CASQUE-VELO-URB',
                    'caracteristiques' => [
                        'Marque' => 'Specialized',
                        'Taille' => ['S', 'M', 'L'],
                    ],
                    'tags' => ['Cyclisme', 'Sécurité'],
                ],
            ];

            $this->createProducts($faker, $cyclingCategory, $cyclingProducts, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits bébé et enfants.
     */
    protected function createKidsProducts($faker, $categories, $caracteristiques, $tags)
    {
        $strollerCategory = $categories->where('name', 'Poussettes & Sièges auto')->first();
        $toysCategory = $categories->where('name', 'Jouets')->first();
        $babyClothesCategory = $categories->where('name', 'Vêtements bébé')->first();

        if ($strollerCategory) {
            $strollers = [
                [
                    'name' => 'Poussette compacte 3-en-1',
                    'description' => 'Poussette avec siège auto et nacelle incluse.',
                    'price' => 399.99,
                    'stock' => 18,
                    'sku' => 'POUSSETTE-3EN1',
                    'caracteristiques' => [
                        'Marque' => 'Chicco',
                        'Type' => '3-en-1',
                    ],
                    'tags' => ['Bébé', 'Pratique'],
                ],
            ];

            $this->createProducts($faker, $strollerCategory, $strollers, $caracteristiques, $tags);
        }

        if ($toysCategory) {
            $toys = [
                [
                    'name' => 'Jeu de construction éducatif',
                    'description' => 'Jeu de construction pour stimuler la créativité des enfants.',
                    'price' => 29.99,
                    'stock' => 120,
                    'sku' => 'JEU-CONSTR-100',
                    'caracteristiques' => [
                        'Marque' => 'Lego',
                        'Âge' => '6+',
                    ],
                    'tags' => ['Jouets', 'Éducatif'],
                ],
            ];

            $this->createProducts($faker, $toysCategory, $toys, $caracteristiques, $tags);
        }

        if ($babyClothesCategory) {
            $babyClothes = [
                [
                    'name' => 'Body bébé en coton',
                    'description' => 'Body doux et confortable pour bébé.',
                    'price' => 14.99,
                    'stock' => 90,
                    'sku' => 'BODY-BEBE-3P',
                    'caracteristiques' => [
                        'Marque' => 'Petit Bateau',
                        'Taille' => ['0-3 mois', '3-6 mois', '6-12 mois'],
                    ],
                    'tags' => ['Bébé', 'Cotton'],
                ],
            ];

            $this->createProducts($faker, $babyClothesCategory, $babyClothes, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits alimentation et boissons.
     */
    protected function createFoodProducts($faker, $categories, $caracteristiques, $tags)
    {
        $groceryCategory = $categories->where('name', 'Épicerie salée')->first();
        $drinksCategory = $categories->where('name', 'Boissons')->first();
        $freshCategory = $categories->where('name', 'Produits frais')->first();

        if ($groceryCategory) {
            $groceries = [
                [
                    'name' => 'Pack pâtes artisanales',
                    'description' => 'Lot de pâtes artisanales de qualité supérieure.',
                    'price' => 12.99,
                    'stock' => 200,
                    'sku' => 'PAST-ART-500',
                    'caracteristiques' => [
                        'Marque' => 'Barilla',
                        'Poids' => '500 g',
                    ],
                    'tags' => ['Épicerie', 'Italien'],
                ],
            ];

            $this->createProducts($faker, $groceryCategory, $groceries, $caracteristiques, $tags);
        }

        if ($drinksCategory) {
            $drinks = [
                [
                    'name' => 'Café en grains 1kg',
                    'description' => 'Café en grains torréfié pour espresso et filtre.',
                    'price' => 18.99,
                    'stock' => 80,
                    'sku' => 'CAFE-1KG',
                    'caracteristiques' => [
                        'Marque' => 'Nespresso',
                        'Poids' => '1 kg',
                    ],
                    'tags' => ['Boissons', 'Café'],
                ],
            ];

            $this->createProducts($faker, $drinksCategory, $drinks, $caracteristiques, $tags);
        }

        if ($freshCategory) {
            $freshProducts = [
                [
                    'name' => 'Corbeille de fruits frais',
                    'description' => 'Sélection de fruits frais pour une collation saine.',
                    'price' => 24.99,
                    'stock' => 60,
                    'sku' => 'FRUITS-24',
                    'caracteristiques' => [
                        'Marque' => 'Bio',
                        'Poids' => '2 kg',
                    ],
                    'tags' => ['Frais', 'Bio'],
                ],
            ];

            $this->createProducts($faker, $freshCategory, $freshProducts, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits auto et moto.
     */
    protected function createAutoProducts($faker, $categories, $caracteristiques, $tags)
    {
        $partsCategory = $categories->where('name', 'Pièces détachées')->first();
        $maintenanceCategory = $categories->where('name', 'Entretien auto')->first();
        $motorbikeCategory = $categories->where('name', 'Équipements moto')->first();

        if ($partsCategory) {
            $parts = [
                [
                    'name' => 'Ampoule LED H4',
                    'description' => 'Ampoule LED longue durée pour feux de voiture.',
                    'price' => 24.99,
                    'stock' => 120,
                    'sku' => 'AMP-LED-H4',
                    'caracteristiques' => [
                        'Marque' => 'Philips',
                        'Type' => 'LED',
                    ],
                    'tags' => ['Auto', 'Sécurité'],
                ],
            ];

            $this->createProducts($faker, $partsCategory, $parts, $caracteristiques, $tags);
        }

        if ($maintenanceCategory) {
            $maintenance = [
                [
                    'name' => 'Huille moteur 5W-30 5L',
                    'description' => 'Huile moteur synthétique pour entretien automobile.',
                    'price' => 39.99,
                    'stock' => 90,
                    'sku' => 'HUILE-5W30-5L',
                    'caracteristiques' => [
                        'Marque' => 'Total',
                        'Volume' => '5 L',
                    ],
                    'tags' => ['Entretien', 'Auto'],
                ],
            ];

            $this->createProducts($faker, $maintenanceCategory, $maintenance, $caracteristiques, $tags);
        }

        if ($motorbikeCategory) {
            $motorbike = [
                [
                    'name' => 'Casque moto intégral',
                    'description' => 'Casque intégral confortable pour motard urbain.',
                    'price' => 129.99,
                    'stock' => 35,
                    'sku' => 'CASQUE-MOTO-INT',
                    'caracteristiques' => [
                        'Marque' => 'Shoei',
                        'Taille' => ['S', 'M', 'L'],
                    ],
                    'tags' => ['Moto', 'Sécurité'],
                ],
            ];

            $this->createProducts($faker, $motorbikeCategory, $motorbike, $caracteristiques, $tags);
        }
    }

    /**
     * Crée des produits à partir d'un tableau de données.
     */
    protected function createProducts($faker, $category, $products, $caracteristiques, $allTags)
    {
        foreach ($products as $productData) {
            $existingProductQuery = Produits::where('category_id', $category->id)
                ->where(function ($query) use ($productData) {
                    $query->where('name', $productData['name']);

                    if (!empty($productData['sku'])) {
                        $query->orWhere('sku', $productData['sku']);
                    }
                });

            if ($existingProductQuery->exists()) {
                continue;
            }

            $price = isset($productData['price']) ? $productData['price'] : round($faker->numberBetween(200000, 500000) / 100, 2);
            $salePrice = isset($productData['sale_price'])
                ? $productData['sale_price']
                : round($price * 0.9, 2);

            $product = Produits::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => $productData['stock'] ?? $faker->numberBetween(0, 500),
                'category_id' => $category->id,
                'images' => null,
                'is_active' => true,
            ]);

            if (isset($productData['caracteristiques'])) {
                $characteristicData = [];
                foreach ($productData['caracteristiques'] as $charName => $value) {
                    $characteristic = $caracteristiques->firstWhere('name', $charName);
                    if (!$characteristic) {
                        continue;
                    }

                    if (is_array($value)) {
                        foreach ($value as $val) {
                            $characteristicData[$characteristic->id] = ['value' => $val];
                        }
                    } else {
                        $characteristicData[$characteristic->id] = ['value' => $value];
                    }
                }
                $product->caracteristiques()->sync($characteristicData);
            }

            if (isset($productData['tags'])) {
                $tagIds = $allTags->whereIn('name', $productData['tags'])->pluck('id')->toArray();
                $product->tags()->sync($tagIds);
            }
        }
    }
}
