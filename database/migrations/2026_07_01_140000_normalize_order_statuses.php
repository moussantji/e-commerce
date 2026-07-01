<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normalise le vocabulaire des statuts de commande (commandes.statut) sur le
 * jeu canonique utilisé par la navigation, l'API et l'app mobile :
 *   en_traitement / en_cours -> traitement
 *   expediee / expedition    -> expedie
 *   livree                   -> livre
 *   annulee                  -> annule
 *   paye / paid              -> payee
 */
return new class extends Migration
{
    private array $map = [
        'en_traitement' => 'traitement',
        'en_cours' => 'traitement',
        'expediee' => 'expedie',
        'expedition' => 'expedie',
        'livree' => 'livre',
        'annulee' => 'annule',
        'paye' => 'payee',
        'paid' => 'payee',
        'paiement_accepte' => 'payee',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('commandes') || ! Schema::hasColumn('commandes', 'statut')) {
            return;
        }

        foreach ($this->map as $old => $new) {
            DB::table('commandes')->where('statut', $old)->update(['statut' => $new]);
        }
    }

    public function down(): void
    {
        // Normalisation à sens unique : pas de rollback fiable (plusieurs
        // anciennes valeurs convergent vers un même code canonique).
    }
};
