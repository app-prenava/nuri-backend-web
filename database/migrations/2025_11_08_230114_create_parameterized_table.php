<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parameterized', function (Blueprint $table) {
            $table->bigIncrements('parameterized_id');
            $table->string('key', 150)->unique();
            $table->string('value', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('parameterized');
    }
};
