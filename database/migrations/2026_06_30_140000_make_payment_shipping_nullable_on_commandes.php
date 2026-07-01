<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Une commande "en attente de paiement" créée depuis le mobile n'a pas
     * encore de méthode de paiement/livraison choisie : on les rend nullable.
     */
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('paiement_id')->nullable()->change();
            $table->unsignedBigInteger('livraison_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Pas de retour en arrière (éviter d'échouer si des NULL existent)
    }
};
