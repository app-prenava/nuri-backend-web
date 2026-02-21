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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->unsignedBigInteger('user_id'); // user yang booking
            $table->unsignedBigInteger('bidan_id'); // bidan yang dipilih
            $table->unsignedBigInteger('bidan_location_id')->nullable(); // lokasi bidan (optional)
            
            // Status appointment
            $table->enum('status', [
                'requested',  // user kirim request
                'accepted',   // bidan terima
                'rejected',   // bidan tolak
                'completed',  // selesai
                'cancelled'   // dibatalkan user
            ])->default('requested');
            
            // Jadwal
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->time('confirmed_time')->nullable();
            
            // Keterangan
            $table->text('notes')->nullable(); // catatan dari user
            $table->text('bidan_notes')->nullable(); // catatan dari bidan
            $table->text('rejection_reason')->nullable();
            
            // Tipe konsultasi
            $table->enum('consultation_type', ['visit', 'consultation', 'checkup'])->default('consultation');
            
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('bidan_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('bidan_location_id')
                  ->references('bidan_location_id')
                  ->on('bidan_locations')
                  ->onDelete('set null');
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['bidan_id', 'status']);
            $table->index(['status', 'preferred_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
