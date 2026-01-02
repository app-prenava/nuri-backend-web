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
        Schema::create('catatanibu', function (Blueprint $table) {
            $table->id('catatan_id');
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal_kunjungan');
            $table->enum('status_kunjungan', ['sedang_berlangsung', 'selesai'])->default('sedang_berlangsung');

            // 9 pertanyaan dengan jawaban boolean
            $table->boolean('q1_demam')->nullable();
            $table->boolean('q2_pusing')->nullable();
            $table->boolean('q3_sulit_tidur')->nullable();
            $table->boolean('q4_risiko_tb')->nullable();
            $table->boolean('q5_gerakan_bayi')->nullable();
            $table->boolean('q6_nyeri_perut')->nullable();
            $table->boolean('q7_cairan_jalan_lahir')->nullable();
            $table->boolean('q8_sakit_kencing')->nullable();
            $table->boolean('q9_diare')->nullable();

            $table->text('hasil_kunjungan')->nullable();
            $table->timestamps();

            // Foreign key ke users
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatanibu');
    }
};
