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
        Schema::create('bidan_applications', function (Blueprint $table) {
            $table->id('bidan_application_id');
            $table->unsignedBigInteger('subscription_plan_id');
            
            // Data pendaftaran bidan
            $table->string('full_name', 150);
            $table->string('email', 150);
            $table->string('phone', 20);
            $table->string('bidan_name', 150); // nama praktik/brand
            $table->text('full_address');
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            
            // Dokumen pendukung (optional)
            $table->string('str_number', 100)->nullable(); // Surat Tanda Registrasi
            $table->string('sip_number', 100)->nullable(); // Surat Izin Praktik
            $table->string('document_url', 500)->nullable(); // URL dokumen pendukung
            
            // Status aplikasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('approved_by_admin_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();

            $table->foreign('subscription_plan_id')
                  ->references('subscription_plan_id')
                  ->on('subscription_plans')
                  ->onDelete('restrict');
            
            $table->foreign('approved_by_admin_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidan_applications');
    }
};
