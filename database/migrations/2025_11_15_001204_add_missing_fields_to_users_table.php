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
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->json('adresse')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'pays')) {
                $table->string('pays')->after('adresse')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'last_activity')) {
                $table->timestamp('last_activity')->nullable()->after('last_login');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('users', 'adresse')) {
                $columnsToDrop[] = 'adresse';
            }
            if (Schema::hasColumn('users', 'pays')) {
                $columnsToDrop[] = 'pays';
            }
            if (Schema::hasColumn('users', 'last_login')) {
                $columnsToDrop[] = 'last_login';
            }
            if (Schema::hasColumn('users', 'last_activity')) {
                $columnsToDrop[] = 'last_activity';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
