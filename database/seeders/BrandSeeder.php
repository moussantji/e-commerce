<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Liste des marques populaires pour des données réalistes
     */
    protected array $popularBrands = [
        'Nike', 'Adidas', 'Apple', 'Samsung', 'Sony', 'Microsoft', 'Dell', 'HP', 'Lenovo', 'Asus',
        'LG', 'Canon', 'Nikon', 'Bose', 'JBL', 'Sennheiser', 'Logitech', 'Razer', 'Corsair',
        'Intel', 'AMD', 'NVIDIA', 'Western Digital', 'Seagate', 'Toshiba', 'Acer', 'MSI', 'Gigabyte',
        'Xiaomi', 'Huawei', 'OnePlus', 'Google', 'Amazon', 'Netflix', 'Spotify', 'Panasonic',
        'Philips', 'Siemens', 'Bosch', 'Whirlpool', 'Moulinex', 'Tefal', 'Rowenta'
    ];

    /**
     * Exécuter les seeds de la base de données.
     */
    public function run(): void
    {
        // Créer le dossier de stockage des logos s'il n'existe pas
        $storagePath = storage_path('app/public/brands');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Créer des marques populaires en vérifiant les doublons
        $addedBrands = [];
        foreach ($this->popularBrands as $index => $brandName) {
            $slug = Str::slug($brandName);

            // Vérifier si la marque existe déjà
            if (!in_array($slug, $addedBrands)) {
                Brand::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $brandName,
                        'description' => $this->generateBrandDescription($brandName),
                        'logo' => null, // Vous pouvez ajouter des logos plus tard
                        'website' => 'https://www.' . $slug . '.com',
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
                $addedBrands[] = $slug;
            }
        }

        // Créer 10 marques aléatoires supplémentaires
        $existingBrandsCount = Brand::count();
        $brandsToCreate = max(0, 10 - $existingBrandsCount + count($this->popularBrands));

        if ($brandsToCreate > 0) {
            Brand::factory($brandsToCreate)->create();
        }
    }

    /**
     * Génère une description pour une marque
     */
    protected function generateBrandDescription(string $brandName): string
    {
        $descriptions = [
            "Découvrez les dernières innovations et produits de haute qualité de $brandName. Une marque de confiance pour des produits exceptionnels.",
            "$brandName est un leader mondial dans son domaine, offrant des produits innovants et de haute qualité depuis de nombreuses années.",
            "Avec $brandName, profitez d'une expérience utilisateur inégalée et de produits conçus pour durer dans le temps.",
            "$brandName se distingue par son engagement envers l'innovation, la qualité et le service client exceptionnel.",
            "Depuis sa création, $brandName n'a cessé de repousser les limites de la technologie et du design."
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
