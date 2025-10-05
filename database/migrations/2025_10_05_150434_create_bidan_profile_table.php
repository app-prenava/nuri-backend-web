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
        Schema::create('bidan_profile', function (Blueprint $table) {
            $table->id('bidan_profile_id');
            $table->unsignedBigInteger('user_id');
            $table->string('tempat_praktik')->nullable();
            $table->string('alamat_praktik')->nullable();
            $table->string('kota_tempat_praktik')->nullable();
            $table->string('kecamatan_tempat_praktik')->nullable();
            $table->string('telepon_tempat_praktik')->nullable();
            $table->string('spesialisasi')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidan_profile');
    }
};
