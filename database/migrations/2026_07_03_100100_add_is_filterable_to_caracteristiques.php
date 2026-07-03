<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le modèle Caracteristiques déclare déjà `is_filterable` (fillable + cast),
 * mais la colonne n'existait pas en base. On l'ajoute pour permettre à
 * l'administrateur de marquer une caractéristique comme filtrable depuis la
 * gestion mobile sans provoquer d'erreur SQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('caracteristiques') && ! Schema::hasColumn('caracteristiques', 'is_filterable')) {
            Schema::table('caracteristiques', function (Blueprint $table) {
                $table->boolean('is_filterable')->default(false)->after('unite');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('caracteristiques') && Schema::hasColumn('caracteristiques', 'is_filterable')) {
            Schema::table('caracteristiques', function (Blueprint $table) {
                $table->dropColumn('is_filterable');
            });
        }
    }
};
