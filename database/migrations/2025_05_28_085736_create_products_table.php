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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('produk'); // Nama produk
            $table->text('deskripsi'); // Deskripsi produk
            $table->decimal('harga', 15, 2); // Harga produk (misal: 15 digit total, 2 digit desimal)
            $table->string('gambar')->nullable(); // URL gambar, bisa null
            $table->string('link')->nullable(); // URL gambar, bisa null
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};