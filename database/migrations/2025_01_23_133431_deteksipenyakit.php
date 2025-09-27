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
        Schema::create('deteksipenyakit', function (Blueprint $table) {
            $table->id('deteksi_id'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable(); 
            $table->string('bidan_id')->nullable(); 
            $table->string('nama')->nullable(); 
            $table->integer('pregnancies')->nullable(); 
            $table->integer('bs'); 
            $table->integer('blood_pressure'); 
            $table->integer('skin_thickness')->nullable(); 
            $table->integer('bmi'); 
            $table->integer('age'); 
            $table->integer('sex')->nullable(); 
            $table->boolean('current_smoker')->nullable(); 
            $table->integer('cigs_per_day')->nullable(); 
            $table->boolean('bp_meds')->nullable(); 
            $table->integer('systolic_bp'); 
            $table->integer('diastolic_bp'); 
            $table->integer('heart_rate'); 
            $table->decimal('body_temp', 5, 2)->nullable(); 

        
            // Kolom hasil prediksi
            $table->boolean('diabetes_prediction')->nullable(); 
            $table->boolean('hypertension_prediction')->nullable(); 
            $table->string('maternal_health_prediction')->nullable(); 
        
            $table->timestamps();
        });
    }        

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deteksipenyakit'); // Perbaiki nama tabel di sini
    }
};
