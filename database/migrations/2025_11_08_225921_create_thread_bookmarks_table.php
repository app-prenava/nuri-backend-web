<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('thread_bookmarks', function (Blueprint $table) {
            $table->bigIncrements('thread_bookmarks_id');
            $table->unsignedBigInteger('thread_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
            $table->unique(['thread_id', 'user_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('thread_bookmarks');
    }
};
