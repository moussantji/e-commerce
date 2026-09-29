<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Espace vendeur : rôle "vendeur" + rattachement optionnel
     * des produits à un vendeur (vendeur_id, null = boutique).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','customer','vendeur') NOT NULL DEFAULT 'customer'");

        Schema::table('produits', function (Blueprint $table) {
            if (!Schema::hasColumn('produits', 'vendeur_id')) {
                $table->unsignedBigInteger('vendeur_id')->nullable()->after('brand_id');
                $table->foreign('vendeur_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            if (Schema::hasColumn('produits', 'vendeur_id')) {
                $table->dropForeign(['vendeur_id']);
                $table->dropColumn('vendeur_id');
            }
        });

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','customer') NOT NULL DEFAULT 'customer'");
    }
};
