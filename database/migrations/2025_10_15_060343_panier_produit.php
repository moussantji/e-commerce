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
        Schema::create('panier_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Paniers::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Produits::class)->constrained()->cascadeOnDelete();
            $table->string('quantite')->nullable();
            $table->string('prix_unitaire')->nullable();
            $table->string('total_ligne')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panier_produit');
    }
};
