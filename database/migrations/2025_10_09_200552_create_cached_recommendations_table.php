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
        Schema::create('cached_recommendations', function (Blueprint $t) {
            $t->id();
            $t->string('features_hash', 64);
            $t->unsignedTinyInteger('gestational_age_weeks');
            $t->string('model_version', 20)->default('v1');
            $t->string('rules_version', 20)->default('r1');
            $t->string('prompt_version', 20)->default('p1');
            $t->json('recommendations');
            $t->timestamps();

            $t->unique(['features_hash','gestational_age_weeks','model_version','rules_version','prompt_version'], 'cache_unique_key');
            $t->index(['gestational_age_weeks','updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cached_recommendations');
    }
};
