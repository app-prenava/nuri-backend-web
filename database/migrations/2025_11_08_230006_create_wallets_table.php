<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wallets', function (Blueprint $table) {
            $table->bigIncrements('wallets_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('wallet_ad', 15, 2)->default(0);
            $table->decimal('wallet_dinkes', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('wallets');
    }
};
