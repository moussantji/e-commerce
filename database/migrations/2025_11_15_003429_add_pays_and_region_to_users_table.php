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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pays')) {
                $table->string('pays')->nullable()->after('adresse');
            }
            if (!Schema::hasColumn('users', 'region')) {
                $table->string('region')->nullable()->after('pays');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pays')) {
                $table->dropColumn('pays');
            }
            if (Schema::hasColumn('users', 'region')) {
                $table->dropColumn('region');
            }
        });
    }
};
