<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Importe le catalogue de produits RÉELS (Maden Baoubab) depuis
 * database/sql/real_products.sql.
 *
 * - Les produits sont insérés SANS id → nouvelles clés auto-incrémentées.
 * - Import robuste : les contraintes de clé étrangère sont désactivées le
 *   temps de l'insertion, puis les `category_id` / `brand_id` qui ne
 *   correspondent à aucune catégorie / marque existante sont remis à NULL
 *   (le produit reste importé, simplement « non classé »).
 *
 * Utilisation :
 *   php artisan db:seed --class=RealProductsSeeder
 *
 * NB : à lancer UNE fois (les `sku` sont uniques ; relancer échouera car les
 * produits seront déjà présents).
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
        $startId = (int) (DB::table('produits')->max('id') ?? 0);

        Schema::disableForeignKeyConstraints();
        try {
            DB::unprepared($sql);
        } catch (\Throwable $e) {
            Schema::enableForeignKeyConstraints();
            $this->command->error('Import échoué : ' . $e->getMessage());
            $this->command->warn('Astuce : vérifiez que les SKU ne sont pas déjà présents.');
            return;
        }
        Schema::enableForeignKeyConstraints();

        // Nettoyage des références orphelines sur les lignes fraîchement importées
        $newRows = DB::table('produits')->where('id', '>', $startId);

        $catIds = DB::table('categories')->pluck('id')->all() ?: [0];
        (clone $newRows)
            ->whereNotNull('category_id')
            ->whereNotIn('category_id', $catIds)
            ->update(['category_id' => null]);

        if (Schema::hasColumn('produits', 'brand_id') && Schema::hasTable('brands')) {
            $brandIds = DB::table('brands')->pluck('id')->all() ?: [0];
            (clone $newRows)
                ->whereNotNull('brand_id')
                ->whereNotIn('brand_id', $brandIds)
                ->update(['brand_id' => null]);
        }

        $count = DB::table('produits')->where('id', '>', $startId)->count();
        $this->command->info("Import réussi : {$count} produits réels ajoutés (nouvelles clés).");
        $this->command->info('Les références catégorie/marque manquantes ont été mises à NULL.');
    }
}
