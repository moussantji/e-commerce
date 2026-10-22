<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les comptes administrateur créés via Google avant le correctif ont
     * hérité du statut par défaut `inactive`. Avec le nouveau blocage des
     * comptes inactifs, ces admins ne pourraient plus se connecter.
     *
     * Les vendeurs sont volontairement exclus : un vendeur inactif est en
     * attente de validation par l'administration.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'admin')
            ->where('status', 'inactive')
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        // Irréversible : impossible de distinguer les comptes créés inactifs
        // par erreur des comptes volontairement désactivés.
    }
};
