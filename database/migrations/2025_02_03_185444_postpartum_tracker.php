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
        Schema::create('pospartum_tracker', function (Blueprint $table) {
            $table->id('pospartum_tracker_id'); 
            $table->string('nama_bayi');
            $table->date('tanggal_kelahiran');
            $table->integer('hari');
            $table->integer('minggu');
            $table->integer('bulan');
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
