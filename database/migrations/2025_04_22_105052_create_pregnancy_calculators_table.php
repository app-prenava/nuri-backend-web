<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pregnancy_calculators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('user_id') // pakai PK yang benar di tabel users
                ->on('users')
                ->onDelete('cascade');

            // 🔹 Data kehamilan
            $table->date('hpht')->nullable(); // Hari Pertama Haid Terakhir (nullable)
            $table->date('hpl')->nullable();  // Hari Perkiraan Lahir (nullable)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_calculators');
    }
};
