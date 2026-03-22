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
        Schema::table('livraisons', function (Blueprint $table) {
            // Suppression des anciennes colonnes
            $table->dropColumn(['name', 'duree', 'prix']);

            // Ajout des nouvelles colonnes
            $table->string('method_name');
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->integer('delivery_time_min')->nullable();
            $table->integer('delivery_time_max')->nullable();
            $table->string('delivery_time_unit')->default('days');
            $table->boolean('is_active')->default(true);
            $table->json('zones')->nullable();
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_order_amount', 10, 2)->nullable();
            $table->decimal('weight_limit', 10, 2)->nullable();
            $table->string('logo')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('config')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livraisons', function (Blueprint $table) {
            // Suppression des nouvelles colonnes
            $table->dropColumn([
                'method_name',
                'description',
                'price',
                'free_shipping_threshold',
                'delivery_time_min',
                'delivery_time_max',
                'delivery_time_unit',
                'is_active',
                'zones',
                'min_order_amount',
                'max_order_amount',
                'weight_limit',
                'logo',
                'sort_order',
                'config'
            ]);

            // Recréation des anciennes colonnes
            $table->string('name');
            $table->timestamp('duree')->nullable();
            $table->string('prix');
        });
    }
};
