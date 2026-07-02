<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder de MISE EN PRODUCTION.
 *
 * Remplit tout le catalogue avec des données réalistes (utilisateurs admin,
 * méthodes de paiement/livraison, catégories, caractéristiques, tags, marques,
 * ~1000 produits AVEC photos, coupons, bannières) et NE crée AUCUNE donnée
 * transactionnelle (pas de commandes, pas de paniers, pas de ventes de démo).
 *
 * Un nettoyage final garantit qu'aucune commande / panier / paiement / recharge
 * ne subsiste.
 *
 * Utilisation :
 *   php artisan migrate:fresh          # base vierge (recommandé)
 *   php artisan db:seed --class=ProductionSeeder
 *
 * NB : les photos produits sont des placeholders libres de droits (voir
 * ProductionCatalogSeeder). Remplacez-les par vos propres photos avant le
 * lancement réel.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Comptes (dont l'admin)
            UserSeeder::class,

            // Paramètres boutique
            PaymentMethodSeeder::class,
            ShippingMethodSeeder::class,

            // Taxonomie du catalogue
            CategorySeeder::class,
            CharacteristicSeeder::class,
            TagSeeder::class,
            BrandSeeder::class,

            // Catalogue : ~1000 produits réalistes avec photos
            ProductionCatalogSeeder::class,

            // Marketing (aucune commande n'est créée ici)
            PromoCodeSeeder::class,
            BannerSeeder::class,

            // Sécurité : on s'assure qu'aucune donnée transactionnelle ne reste
            ProductionCleanupSeeder::class,
        ]);

        $this->command->info('✅ Base prête pour la production (catalogue rempli, aucune commande/panier).');
    }
}
