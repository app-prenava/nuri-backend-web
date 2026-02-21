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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id('subscription_plan_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('duration_days')->default(30); // durasi berlangganan dalam hari
            $table->json('features')->nullable(); // fitur-fitur plan dalam JSON
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // untuk urutan tampilan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
