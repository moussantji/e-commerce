<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Supprime l'état "traitement" (redondant entre "payee" et "expedie").
     * Les commandes concernées deviennent "payee" (paiement confirmé,
     * prête à expédier) avec date_traitement renseignée.
     */
    public function up(): void
    {
        // Anciennes variantes -> vocabulaire canonique restant.
        $legacy = [
            'en_cours' => 'payee',
            'en_traitement' => 'payee',
            'en_preparation' => 'payee',
            'en preparation' => 'payee',
            'expediee' => 'expedie',
            'expedition' => 'expedie',
            'livree' => 'livre',
            'delivered' => 'livre',
            'annulee' => 'annule',
            'cancelled' => 'annule',
            'canceled' => 'annule',
            'paye' => 'payee',
            'paid' => 'payee',
            'paiement_accepte' => 'payee',
        ];
        foreach ($legacy as $from => $to) {
            DB::table('commandes')->where('statut', $from)->update(['statut' => $to]);
        }

        // "traitement" fusionné dans "payee".
        DB::table('commandes')
            ->where('statut', 'traitement')
            ->update(['statut' => 'payee', 'date_traitement' => DB::raw('COALESCE(date_traitement, NOW())')]);
    }

    /**
     * Reverse the migrations (fusion irréversible : on ne peut pas
     * distinguer les ex-"traitement" des "payee" d'origine).
     */
    public function down(): void
    {
        // Volontairement vide.
    }
};
