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
        Schema::create('shop_logs', function (Blueprint $table) {
            $table->bigIncrements('shop_logs_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('product_name', 150)->nullable();
            $table->string('price', 30)->nullable();
            $table->text('url')->nullable();
            $table->enum('action', ['create', 'update', 'delete', 'admin_delete']);
            $table->json('data_snapshot')->nullable();
            $table->timestamp('admin_deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_logs');
    }
};
