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
        Schema::create('appointment_consents', function (Blueprint $table) {
            $table->id('consent_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('appointment_id');
            
            // Versi dan snapshot consent
            $table->string('consent_version', 20)->default('1.0');
            $table->text('consent_text_snapshot'); // copy T&C saat user setuju
            
            // Data yang diizinkan untuk dibagikan
            $table->json('shared_fields'); // {"name": true, "phone": true, "address": false, ...}
            
            // Timestamp dan tracking
            $table->timestamp('accepted_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('appointment_id')
                  ->references('appointment_id')
                  ->on('appointments')
                  ->onDelete('cascade');
            
            // Unique constraint - satu consent per appointment
            $table->unique(['appointment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_consents');
    }
};
