<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('paiement_id')->constrained('paiements')->cascadeOnDelete();
            $table->foreignId('livraison_id')->constrained('livraisons')->cascadeOnDelete();
            $table->string('numero_commande')->unique();
            $table->enum('statut', ['en_attente', 'traitement', 'expedie', 'livre', 'annule'])->default('en_attente');
            $table->decimal('sous_total', 10, 2);
            $table->decimal('frais_livraison', 10, 2);
            $table->decimal('remise', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->json('adresse_facturation');
            $table->json('adresse_livraison')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('date_en_attente')->nullable();
            $table->timestamp('date_traitement')->nullable();
            $table->timestamp('date_expedition')->nullable();
            $table->timestamp('date_livraison')->nullable();
            $table->timestamp('date_annulation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
