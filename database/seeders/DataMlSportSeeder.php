<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataMlSportSeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            'padel',
            'strength_light_resistance',
            'elliptical_light',
            'treadmill_walk',
            'light_dance',
            'modified_plank',

            'low_impact_aerobic',
            'high_impact_aerobic',
            'strength_heavy',
            'pilates_prenatal',
            'jump_rope',
            'jogging',
            'running',
            'jumping',

            'prenatal_yoga',
            'stretching_gentle',
            'pelvic_floor',
            'cat_cow_pose',
            'breathing_exercise',
            'walking',

            'swimming',
            'aqua_cycling',
        ];

        $now = now();

        $rows = array_map(function ($act) use ($now) {
            return [
                'activity'   => $act,
                'video_link' => null,
                'long_text'  => null,
                'picture_1'  => null,
                'picture_2'  => null,
                'picture_3'  => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $activities);

        DB::table('data_ml_sport')->upsert(
            $rows,
            ['activity'],
            ['updated_at']
        );
    }
}
