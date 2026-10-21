<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupon_user', function (Blueprint $table) {
            // Permet de renvoyer le coupon fidélité une fois par mois, sans
            // dupliquer la ligne d'attribution (clé unique coupon + client).
            $table->string('last_sent_month', 7)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('coupon_user', function (Blueprint $table) {
            $table->dropColumn('last_sent_month');
        });
    }
};
