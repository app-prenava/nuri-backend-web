<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->bigIncrements('wallet_transactions_id');
            $table->unsignedBigInteger('wallets_id')->index();
            $table->enum('sof', ['ad', 'dinkes']);
            $table->decimal('amount', 15, 2);
            $table->enum('trx_action', ['debit', 'kredit']);
            $table->enum('status', ['success', 'failed', 'pending', 'refund', 'reversal']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('wallet_transactions');
    }
};
