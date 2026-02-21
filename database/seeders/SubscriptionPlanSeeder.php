<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => 'Paket dasar untuk bidan yang baru memulai praktik digital',
                'price' => 99000,
                'duration_days' => 30,
                'features' => [
                    'Profil bidan di aplikasi',
                    'Menerima hingga 20 appointment/bulan',
                    'Notifikasi appointment',
                    'Dashboard sederhana',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'description' => 'Paket lengkap untuk bidan profesional dengan praktik aktif',
                'price' => 199000,
                'duration_days' => 30,
                'features' => [
                    'Semua fitur Basic',
                    'Appointment tidak terbatas',
                    'Prioritas tampil di pencarian',
                    'Dashboard lengkap dengan analitik',
                    'Export data pasien',
                    'Badge "Verified" di profil',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Paket premium untuk klinik atau praktik bersama',
                'price' => 499000,
                'duration_days' => 30,
                'features' => [
                    'Semua fitur Professional',
                    'Multi-lokasi praktik',
                    'Multiple staff accounts',
                    'Custom branding',
                    'API access',
                    'Dedicated support',
                    'Training & onboarding',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Trial',
                'description' => 'Paket percobaan gratis selama 7 hari',
                'price' => 0,
                'duration_days' => 7,
                'features' => [
                    'Profil bidan di aplikasi',
                    'Menerima hingga 5 appointment',
                    'Dashboard sederhana',
                ],
                'is_active' => true,
                'sort_order' => 0,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
