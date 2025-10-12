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
        Schema::create('pregnancy_assessments', function (Blueprint $t) {
            $t->id('assessment_id');
            $t->unsignedBigInteger('pregnancy_id');

            $t->decimal('bmi', 4, 1)->nullable();
            $t->boolean('hypertension')->default(false);
            $t->boolean('is_diabetes')->default(false);
            $t->boolean('gestational_diabetes')->default(false);
            $t->boolean('is_fever')->default(false);
            $t->boolean('is_high_heart_rate')->default(false);
            $t->boolean('previous_complications')->default(false);
            $t->boolean('mental_health_issue')->default(false);

            $t->boolean('back_pain')->default(false);
            $t->boolean('low_impact_pref')->default(true);
            $t->boolean('water_access')->default(false);
            $t->boolean('placenta_previa')->default(false);

            $t->timestamps();

            $t->foreign('pregnancy_id')->references('pregnancy_id')->on('pregnancies')->onDelete('cascade');
            $t->index(['pregnancy_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnancy_assessments');
    }
};
