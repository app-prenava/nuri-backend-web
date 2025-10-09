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
        Schema::create('pregnancies', function (Blueprint $t) {
            $t->id('pregnancy_id');
            $t->unsignedBigInteger('user_id');
            $t->date('lmp_date')->nullable();
            $t->date('edd')->nullable();
            $t->enum('status', ['planned','ongoing','postpartum'])->default('ongoing');
            $t->boolean('multiple_gestation')->default(false);
            $t->timestamps();

            $t->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $t->index(['user_id','status','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnancies');
    }
};
