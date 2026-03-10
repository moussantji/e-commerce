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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // ex: produits/12/img_1.jpg

            // Liens possibles (un seul utilisé par ligne)
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Produits::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Paiements::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Livraison::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Brand::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Categories::class)->nullable()->constrained();
            // ✅ Par ceci :
            $table->foreignId('banner_id')->nullable()->constrained('banners')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
