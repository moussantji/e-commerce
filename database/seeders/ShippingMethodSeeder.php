<?php

namespace Database\Seeders;

use App\Models\Livraison;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Dossier de stockage des logos
        $logoPath = 'public/shipping-methods';
        if (!Storage::exists($logoPath)) {
            Storage::makeDirectory($logoPath);
        }
        
        // Copier les logos de démonstration
        $logos = [
            'colissimo.png' => 'https://raw.githubusercontent.com/google/material-design-icons/master/png/maps/local_shipping/materialicons/48dp/1x/baseline_local_shipping_black_48dp.png',
            'chronopost.png' => 'https://raw.githubusercontent.com/google/material-design-icons/master/png/maps/local_shipping/materialicons/48dp/1x/baseline_local_shipping_black_48dp.png',
            'relais-colis.png' => 'https://raw.githubusercontent.com/google/material-design-icons/master/png/maps/local_convenience_store/materialicons/48dp/1x/baseline_local_convenience_store_black_48dp.png',
            'mondial-relay.png' => 'https://raw.githubusercontent.com/google/material-design-icons/master/pkg/@mdi/svg/svg/truck-delivery.svg',
        ];
        
        foreach ($logos as $filename => $url) {
            $destination = $logoPath . '/' . $filename;
            if (!Storage::exists($destination)) {
                try {
                    $contents = @file_get_contents($url);
                    if ($contents === false) {
                        $this->command->warn("Impossible de télécharger l'image: $url");
                        continue;
                    }
                    Storage::put($destination, $contents);
                    $this->command->info("Image téléchargée: $filename");
                } catch (\Exception $e) {
                    $this->command->error("Erreur lors du téléchargement de $url: " . $e->getMessage());
                }
            }
        }
        
        // Méthodes de livraison par défaut en FCFA
        $methods = [
            [
                'method_name' => 'Livraison standard',
                'description' => 'Livraison en 3-5 jours ouvrés dans la ville',
                'price' => 2000, // ~3€
                'delivery_time_min' => 3,
                'delivery_time_max' => 5,
                'delivery_time_unit' => 'days',
                'free_shipping_threshold' => 50000, // ~76€
                'min_order_amount' => 0,
                'weight_limit' => 20, // kg
                'logo' => 'shipping-methods/colissimo.png',
                'is_active' => true,
                'sort_order' => 1,
                'zones' => [
                    'CI' => ['price' => 2000, 'free_threshold' => 50000],
                ],
                'config' => [
                    'tracking_url' => '#',
                    'insurance' => true,
                    'signature_required' => false,
                ],
            ],
            [
                'method_name' => 'Livraison express',
                'description' => 'Livraison en 24-48h en ville',
                'price' => 5000, // ~7.6€
                'delivery_time_min' => 1,
                'delivery_time_max' => 2,
                'delivery_time_unit' => 'days',
                'free_shipping_threshold' => 100000, // ~152€
                'min_order_amount' => 0,
                'weight_limit' => 30,
                'logo' => 'shipping-methods/chronopost.png',
                'is_active' => true,
                'sort_order' => 2,
                'zones' => [
                    'CI' => ['price' => 5000, 'free_threshold' => 100000],
                ],
                'config' => [
                    'tracking_url' => '#',
                    'insurance' => true,
                    'signature_required' => true,
                ],
            ],
            [
                'method_name' => 'Livraison intérieure',
                'description' => 'Livraison en 5-10 jours dans les autres villes',
                'price' => 5000, // ~7.6€
                'delivery_time_min' => 5,
                'delivery_time_max' => 10,
                'delivery_time_unit' => 'days',
                'free_shipping_threshold' => 100000, // ~152€
                'min_order_amount' => 0,
                'weight_limit' => 30,
                'logo' => 'shipping-methods/relais-colis.png',
                'is_active' => true,
                'sort_order' => 3,
                'zones' => [
                    'CI' => ['price' => 5000, 'free_threshold' => 100000],
                ],
                'config' => [
                    'tracking_url' => '#',
                    'insurance' => true,
                    'signature_required' => true,
                ],
            ],
            [
                'method_name' => 'Retrait en magasin',
                'description' => 'Retrait gratuit en magasin',
                'price' => 0,
                'delivery_time_min' => 0,
                'delivery_time_max' => 0,
                'delivery_time_unit' => 'hours',
                'free_shipping_threshold' => 0,
                'min_order_amount' => 0,
                'weight_limit' => null,
                'logo' => 'shipping-methods/mondial-relay.png',
                'is_active' => true,
                'sort_order' => 4,
                'zones' => [
                    'CI' => ['price' => 0, 'free_threshold' => 0],
                ],
                'config' => [
                    'address' => 'Rue du Commerce, Cocody, Abidjan',
                    'opening_hours' => 'Lun-Sam: 8h-20h',
                ],
            ],
        ];
        
        foreach ($methods as $method) {
            // Vérifier si la méthode existe déjà
            $exists = Livraison::where('method_name', $method['method_name'])->exists();
            
            if (!$exists) {
                // Convertir les tableaux en JSON
                $zones = json_encode($method['zones'] ?? []);
                $config = json_encode($method['config'] ?? []);
                
                unset($method['zones'], $method['config']);
                
                $method['zones'] = $zones;
                $method['config'] = $config;
                
                Livraison::create($method);
            }
        }
        
        $this->command->info('Méthodes de livraison créées avec succès !');
    }
}
