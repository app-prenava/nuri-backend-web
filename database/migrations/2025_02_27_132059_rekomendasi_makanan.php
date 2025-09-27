<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rekomendasi_makanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->json('target_makanan'); // Ex: ["Hamil", "Menyusui"]
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nutrition_recommendations');
    }
};
