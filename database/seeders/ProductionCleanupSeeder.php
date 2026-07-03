<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nettoyage "mise en production" : supprime toutes les données
 * TRANSACTIONNELLES (commandes, lignes de commande, paniers, preuves de
 * paiement, transactions de portefeuille, notifications) pour partir sur une
 * base propre.
 *
 * Ne touche PAS au catalogue (produits, catégories, marques, tags…),
 * ni aux utilisateurs, ni aux méthodes de paiement/livraison, ni aux coupons.
 */
class ProductionCleanupSeeder extends Seeder
{
    /** Tables transactionnelles à vider (enfants avant parents). */
    private array $tables = [
        'commande_produit',
        'commande_produits',
        'commandes',
        'panier_produit',
        'paniers',
        'payment_proofs',
        'wallet_transactions',
        'notifications',
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Vidé : {$table}");
            }
        }

        // Remet les soldes de portefeuille à zéro (les recharges sont effacées)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'wallet_balance')) {
            DB::table('users')->update(['wallet_balance' => 0]);
            $this->command->info('Soldes portefeuille remis à zéro.');
        }

        Schema::enableForeignKeyConstraints();

        $this->command->info('Nettoyage transactionnel terminé.');
    }
}
