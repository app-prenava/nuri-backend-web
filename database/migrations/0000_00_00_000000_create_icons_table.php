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
        Schema::create('icons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama deskriptif ikon, mis. "Senyum", "Hati"
            $table->string('type')->nullable(); // Tipe ikon, mis. "material", "fontawesome", "svg_string", "url"
            $table->text('data')->nullable(); // Bisa untuk menyimpan path URL jika tipe 'url', atau string SVG jika tipe 'svg_string'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icons');
    }
};