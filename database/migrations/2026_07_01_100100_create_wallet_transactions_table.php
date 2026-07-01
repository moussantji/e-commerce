<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // topup | transfer_out | transfer_in | purchase | refund
            $table->string('type');
            $table->decimal('amount', 12, 2);
            // orange_money | moov_money | wave | wallet ...
            $table->string('method')->nullable();
            $table->string('phone')->nullable();
            // Destinataire pour un transfert
            $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
            // pending | confirmed | rejected
            $table->string('status')->default('pending');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
