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
         Schema::create('user_dinkes', function (Blueprint $table) {
            $table->id('user_dinkes_id');
            $table->string('photo', 255);
            $table->unsignedBigInteger('user_id');
            $table->string('jabatan')->nullable();
            $table->string('nip')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dinkes');
    }
};
