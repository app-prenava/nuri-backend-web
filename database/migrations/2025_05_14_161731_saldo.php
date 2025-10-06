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
        
        Schema::create('saldo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('user_id') // pakai PK yang benar di tabel users
                ->on('users')
                ->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->string('keterangan')->nullable();
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->foreign('cancelled_by')
                ->references('user_id')   // PK yang benar di tabel users
                ->on('users')
                ->onDelete('set null');  
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

        });

 
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
