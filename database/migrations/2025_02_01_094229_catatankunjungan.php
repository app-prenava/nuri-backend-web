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
        Schema::create('catatankunjungan', function (Blueprint $table) {
            $table->id('kunjungan_id'); 
            $table->string('tanggal_kunjungan');
            $table->string('keluhan'); 
            $table->string('pertanyaan');
            $table->string('status_kunjungan');
            $table->string('hasil_kunjungan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
