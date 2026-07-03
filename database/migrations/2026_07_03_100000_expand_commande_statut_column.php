<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrige l'erreur « Data truncated for column 'statut' » lors de la mise à
 * jour de l'état d'une commande depuis l'app mobile.
 *
 * La colonne `commandes.statut` était un ENUM limité à
 *   ['en_attente', 'traitement', 'expedie', 'livre', 'annule'].
 * Or le workflow (API + app mobile + web) utilise désormais deux états
 * supplémentaires : `paiement_declare` et `payee`. Toute tentative d'écrire
 * ces valeurs provoquait la troncature MySQL.
 *
 * On remplace l'ENUM par un VARCHAR souple (le vocabulaire canonique est déjà
 * garanti côté application via App\Support\OrderStatus et la validation des
 * contrôleurs), ce qui évite tout futur blocage lié à l'ajout d'un statut.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('commandes') || ! Schema::hasColumn('commandes', 'statut')) {
            return;
        }

        // MySQL / MariaDB : passage de ENUM à VARCHAR sans perdre les données.
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE `commandes` MODIFY `statut` VARCHAR(40) NOT NULL DEFAULT 'en_attente'");
        } else {
            // Autres pilotes (sqlite/pgsql) : la colonne se comporte déjà comme
            // une chaîne, rien à faire.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('commandes') || ! Schema::hasColumn('commandes', 'statut')) {
            return;
        }

        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // Retour à l'ENUM d'origine (les valeurs hors liste seront ramenées
            // à 'en_attente' par MySQL si nécessaire).
            DB::statement("ALTER TABLE `commandes` MODIFY `statut` ENUM('en_attente','traitement','expedie','livre','annule') NOT NULL DEFAULT 'en_attente'");
        }
    }
};
