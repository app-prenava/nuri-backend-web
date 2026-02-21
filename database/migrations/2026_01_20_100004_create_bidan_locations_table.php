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
        Schema::create('bidan_locations', function (Blueprint $table) {
            $table->id('bidan_location_id');
            $table->unsignedBigInteger('bidan_id'); // FK ke users (role=bidan)
            
            // Koordinat lokasi
            $table->decimal('lat', 10, 8); // latitude
            $table->decimal('lng', 11, 8); // longitude
            
            // Info lokasi
            $table->string('address_label', 255); // alamat display
            $table->string('phone_override', 20)->nullable(); // nomor telp khusus lokasi
            $table->text('notes')->nullable(); // catatan tambahan
            
            // Jadwal operasional (optional)
            $table->json('operating_hours')->nullable(); // {"mon": "08:00-17:00", ...}
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false); // lokasi utama
            
            $table->timestamps();

            $table->foreign('bidan_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Index untuk query geospatial
            $table->index(['lat', 'lng']);
            $table->index(['bidan_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidan_locations');
    }
};
