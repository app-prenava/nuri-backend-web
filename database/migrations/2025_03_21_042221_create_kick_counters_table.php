<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKickCountersTable extends Migration
{
    public function up()
    {
        Schema::create('kick_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('kick_count');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
            $table->integer('duration')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kick_counters');
    }
};
