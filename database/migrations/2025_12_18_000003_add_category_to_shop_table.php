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
        Schema::table('shop', function (Blueprint $table) {
            if (!Schema::hasColumn('shop', 'category')) {
                $table->string('category', 50)->nullable()->after('product_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop', function (Blueprint $table) {
            if (Schema::hasColumn('shop', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};

