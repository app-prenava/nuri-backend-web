<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostpartumArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('postpartum_articles', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten');
            $table->string('kategori')->nullable();
            $table->string('gambar');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('postpartum_articles');
    }
}
