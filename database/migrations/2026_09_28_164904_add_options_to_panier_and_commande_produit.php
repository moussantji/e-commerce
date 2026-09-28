<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Options choisies sur la fiche produit (ex: Stockage 256 Go).
     * Stockées en JSON texte, nullable pour compatibilité des lignes existantes.
     */
    public function up(): void
    {
        Schema::table('panier_produit', function (Blueprint $table) {
            if (!Schema::hasColumn('panier_produit', 'options')) {
                $table->text('options')->nullable()->after('total_ligne');
            }
        });

        Schema::table('commande_produit', function (Blueprint $table) {
            if (!Schema::hasColumn('commande_produit', 'options')) {
                $table->text('options')->nullable()->after('total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panier_produit', function (Blueprint $table) {
            if (Schema::hasColumn('panier_produit', 'options')) {
                $table->dropColumn('options');
            }
        });

        Schema::table('commande_produit', function (Blueprint $table) {
            if (Schema::hasColumn('commande_produit', 'options')) {
                $table->dropColumn('options');
            }
        });
    }
};
