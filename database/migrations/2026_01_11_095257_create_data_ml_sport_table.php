<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_ml_sport', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('activity', 100)->unique();

            $table->string('video_link')->nullable();
            $table->text('long_text')->nullable();

            $table->string('picture_1')->nullable();
            $table->string('picture_2')->nullable();
            $table->string('picture_3')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_ml_sport');
    }
};
