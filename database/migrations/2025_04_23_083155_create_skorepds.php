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
        Schema::create('skor_epds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediksi_depresi_id')
                  ->constrained('prediksi_depresi')
                  ->onDelete('cascade');
            $table->json('answers'); // Store all 10 answers as JSON array
            $table->integer('score'); // total skor EPDS
            $table->string('hasil_epds')->nullable(); // status depresi (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skor_epds');
    }
};