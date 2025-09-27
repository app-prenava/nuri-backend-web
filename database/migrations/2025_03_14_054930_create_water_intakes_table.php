<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('water_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('jumlah_ml')->default(250);
            $table->date('tanggal');
            $table->timestamps();

            // ✅ Cegah duplikasi entri harian per user
            $table->unique(['user_id', 'tanggal'], 'unique_user_per_day');
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_intakes');
    }
};
