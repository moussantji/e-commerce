<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les inscriptions client créées avant le correctif héritaient du statut
     * par défaut `inactive` alors que le compte était bien utilisable et
     * connecté. On aligne les comptes client existants sur le statut actif.
     *
     * Les vendeurs (validation par l'admin) et les admins ne sont pas touchés.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'customer')
            ->where('status', 'inactive')
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        // Irréversible : impossible de distinguer les comptes créés inactifs
        // par erreur des comptes volontairement désactivés par un admin.
    }
};
