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
        Schema::create('komunitas', function (Blueprint $table) {
            $table->id('post_id' );
            $table->string('user_id');
            $table->string('judul');
            $table->string('deskripsi');
            $table->string('gambar')->nullable();
            $table->integer('komen')->nullable();
            $table->integer('apresiasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komunitas');
    }
};
