<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements', 'instructions')) {
                $table->text('instructions')->nullable()->after('description');
            }
            if (!Schema::hasColumn('paiements', 'account_number')) {
                $table->string('account_number')->nullable()->after('instructions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (Schema::hasColumn('paiements', 'instructions')) {
                $table->dropColumn('instructions');
            }
            if (Schema::hasColumn('paiements', 'account_number')) {
                $table->dropColumn('account_number');
            }
        });
    }
};
