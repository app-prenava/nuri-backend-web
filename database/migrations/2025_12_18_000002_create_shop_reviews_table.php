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
        Schema::create('shop_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('product_id')
                ->on('shop')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['product_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_reviews');
    }
};

