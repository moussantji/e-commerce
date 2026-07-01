<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normalise le vocabulaire des statuts de paiement pour qu'il soit identique
 * partout (site web + app mobile) :
 *   confirme -> confirmed
 *   rejete   -> rejected
 *
 * S'applique aux preuves de paiement (payment_proofs) et aux paiements
 * (paiements). Les rechargements (wallet_transactions) utilisent déjà
 * confirmed/rejected.
 */
return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'confirme' => 'confirmed',
            'rejete' => 'rejected',
        ];

        foreach (['payment_proofs', 'paiements'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'status')) {
                continue;
            }
            foreach ($map as $old => $new) {
                DB::table($table)->where('status', $old)->update(['status' => $new]);
            }
        }
    }

    public function down(): void
    {
        $map = [
            'confirmed' => 'confirme',
            'rejected' => 'rejete',
        ];

        // On ne touche qu'aux preuves de paiement : les rechargements
        // (wallet_transactions) doivent conserver confirmed/rejected.
        if (Schema::hasTable('payment_proofs') && Schema::hasColumn('payment_proofs', 'status')) {
            foreach ($map as $new => $old) {
                DB::table('payment_proofs')->where('status', $new)->update(['status' => $old]);
            }
        }
    }
};
