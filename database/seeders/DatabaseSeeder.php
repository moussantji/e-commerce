<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // D'abord les utilisateurs
            UserSeeder::class,

            // Puis les méthodes de paiement et de livraison
            PaymentMethodSeeder::class,
            ShippingMethodSeeder::class,

            // Ensuite les catégories
            CategorySeeder::class,

            // Puis les caractéristiques et tags
            CharacteristicSeeder::class,
            TagSeeder::class,

            // Puis les marques (avant les produits car les produits ont une clé étrangère vers les marques)
            BrandSeeder::class,

            // Puis les commandes
            CommandesSeeder::class,

            // Ensuite les produits (dépend des catégories, caractéristiques et tags)
            ProductSeeder::class,

            // Puis les paniers (dépend des utilisateurs et des produits)
            CartSeeder::class,

            // Puis les codes promo (nécessaires pour les commandes)
            PromoCodeSeeder::class,

            // Enfin les commandes et avis (dépendent des utilisateurs, produits et codes promo)
            OrderSeeder::class,
            ReviewSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
