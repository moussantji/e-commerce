<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Importe le catalogue de produits RÉELS (Maden Baoubab) depuis
 * database/sql/real_products.sql.
 *
 * Les produits sont insérés SANS id → l'auto-incrément attribue de nouvelles
 * clés (aucune collision avec des id existants).
 *
 * Prérequis :
 *   - les category_id / brand_id référencés doivent exister dans la base ;
 *   - les `sku` doivent être uniques (relancer échouera si déjà importés).
 *
 * Utilisation :
 *   php artisan db:seed --class=RealProductsSeeder
 */
class RealProductsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/real_products.sql');

        if (! is_file($path)) {
            $this->command->error("Fichier introuvable : {$path}");
            return;
        }

        $sql = file_get_contents($path);

        try {
            DB::unprepared($sql);
            $this->command->info('Produits réels importés (nouvelles clés auto-incrémentées).');
        } catch (\Throwable $e) {
            $this->command->error('Import échoué : ' . $e->getMessage());
            $this->command->warn(
                'Vérifiez que les category_id/brand_id existent et que les SKU '
                . 'ne sont pas déjà présents.',
            );
        }
    }
}
