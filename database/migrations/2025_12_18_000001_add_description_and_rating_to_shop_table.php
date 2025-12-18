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
            if (!Schema::hasColumn('shop', 'description')) {
                $table->text('description')->nullable()->after('product_name');
            }

            if (!Schema::hasColumn('shop', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->default(0)->after('url');
            }

            if (!Schema::hasColumn('shop', 'rating_count')) {
                $table->unsignedInteger('rating_count')->default(0)->after('average_rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop', function (Blueprint $table) {
            if (Schema::hasColumn('shop', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('shop', 'rating_count')) {
                $table->dropColumn('rating_count');
            }

            if (Schema::hasColumn('shop', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
        });
    }
};

