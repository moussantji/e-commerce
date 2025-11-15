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
        Schema::table('paiements', function (Blueprint $table) {
            // Suppression des anciennes colonnes
            $table->dropColumn(['name', 'duree', 'prix']);
            
            // Ajout des nouvelles colonnes
            $table->string('method_name');
            $table->string('description')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('fee_percentage', 5, 2)->default(0);
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('provider_name')->nullable();
            $table->json('config')->nullable();
            $table->integer('sort_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Suppression des nouvelles colonnes
            $table->dropColumn([
                'method_name',
                'description',
                'fee',
                'fee_percentage',
                'logo',
                'is_active',
                'provider_name',
                'config',
                'sort_order'
            ]);
            
            // Recréation des anciennes colonnes
            $table->string('name');
            $table->timestamp('duree')->nullable();
            $table->string('prix')->nullable();
        });
    }
};
