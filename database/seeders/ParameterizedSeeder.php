<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterizedSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('parameterized')->insert([
            [
                'key' => 'schedule_update_wallet_ad',
                'value' => '0 0 * * *',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'schedule_clear_thread_views',
                'value' => '0 3 * * 0',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'thread_retention_days',
                'value' => '365',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'default_thread_category',
                'value' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'max_thread_category_length',
                'value' => '100',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
