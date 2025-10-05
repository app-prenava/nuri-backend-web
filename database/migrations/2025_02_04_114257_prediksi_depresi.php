<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SkorEpds;

return new class extends Migration {
    public function up()
    {
        Schema::create('prediksi_depresi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->integer('umur');
            $table->integer('merasa_sedih'); // 0, 1, 2
            $table->integer('mudah_tersinggung'); // 0, 1, 2
            $table->integer('masalah_tidur'); // 0, 1, 2
            $table->integer('masalah_fokus'); // 0, 1, 2
            $table->integer('pola_makan'); // 0, 1, 2
            $table->integer('merasa_bersalah'); // 0, 1, 2
            $table->integer('suicide_attempt'); // 0, 1, 2
            $table->boolean('hasil_prediksi')->nullable(); // 0: Tidak Depresi, 1: Depresi
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('prediksi_depresi');
    }
};
