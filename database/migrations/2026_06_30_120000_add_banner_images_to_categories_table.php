<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deux images de bannière cliquables, choisies manuellement par catégorie
     * (affichées sur l'accueil de l'app mobile).
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('banner_image_1')->nullable()->after('image');
            $table->string('banner_image_2')->nullable()->after('banner_image_1');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['banner_image_1', 'banner_image_2']);
        });
    }
};
