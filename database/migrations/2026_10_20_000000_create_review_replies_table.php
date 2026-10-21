<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Réponses imbriquées aux avis produits.
     *
     * Un avis (avis_clients) peut recevoir plusieurs réponses. Une réponse peut
     * elle-même recevoir des réponses via parent_id, sans limite de profondeur.
     */
    public function up(): void
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avis_client_id')->constrained('avis_clients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('review_replies')->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index(['avis_client_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};
