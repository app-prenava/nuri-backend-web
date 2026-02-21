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
        Schema::create('bidan_subscriptions', function (Blueprint $table) {
            $table->id('bidan_subscription_id');
            $table->unsignedBigInteger('user_id'); // bidan user
            $table->unsignedBigInteger('bidan_application_id')->nullable();
            $table->unsignedBigInteger('subscription_plan_id');
            
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            
            // Info pembayaran (optional untuk tracking)
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('bidan_application_id')
                  ->references('bidan_application_id')
                  ->on('bidan_applications')
                  ->onDelete('set null');
            
            $table->foreign('subscription_plan_id')
                  ->references('subscription_plan_id')
                  ->on('subscription_plans')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidan_subscriptions');
    }
};
