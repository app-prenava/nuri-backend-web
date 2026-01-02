<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatatanIbuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil 20 user ibu hamil
        $ibuHamilUsers = DB::table('users')
            ->where('role', 'ibu_hamil')
            ->limit(20)
            ->pluck('user_id');

        if ($ibuHamilUsers->isEmpty()) {
            $this->command->warn('Tidak ada user dengan role ibu_hamil ditemukan.');
            return;
        }

        // Generate tanggal kunjungan (6 bulan terakhir)
        $statuses = ['sedang_berlangsung', 'selesai'];

        foreach ($ibuHamilUsers as $userId) {
            // Buat 1-3 catatan kunjungan per user
            $jumlahCatatan = rand(1, 3);

            for ($i = 0; $i < $jumlahCatatan; $i++) {
                $tanggalKunjungan = now()
                    ->subDays(rand(0, 180))
                    ->subDays($i * 30); // Sebar tanggal setiap 30 hari

                // Random jawaban untuk 9 pertanyaan
                $q1_demam = (bool) rand(0, 1);
                $q2_pusing = (bool) rand(0, 1);
                $q3_sulit_tidur = (bool) rand(0, 1);
                $q4_risiko_tb = (bool) rand(0, 10) === 0; // Jarang yang yes (10%)
                $q5_gerakan_bayi = (bool) rand(0, 1);
                $q6_nyeri_perut = (bool) rand(0, 1);
                $q7_cairan_jalan_lahir = (bool) rand(0, 10) === 0; // Jarang (10%)
                $q8_sakit_kencing = (bool) rand(0, 1);
                $q9_diare = (bool) rand(0, 1);

                // Status dan hasil kunjungan
                $statusKunjungan = $statuses[array_rand($statuses)];

                // Jika status selesai, beri hasil kunjungan dari bidan
                $hasilKunjungan = null;
                if ($statusKunjungan === 'selesai') {
                    $hasilKunjunganOptions = [
                        'Kondisi ibu dan bayu baik. Tetap jaga kesehatan dan rutin kontrol.',
                        'Perlu perhatian lebih pada pola makan dan istirahat.',
                        'Tekanan darah normal, berat badan baik. Lakukan senam hamil rutin.',
                        'Hati-hati dengan keluhan yang dialami, segera hubungi bidan jika ada perubahan.',
                        'Kondisi stabil, jaga pola makan bergizi dan istirahat cukup.',
                        'Perlu monitoring lebih intensif minggu depan.',
                        'Semua dalam batas normal, tetap jalankan saran hidup sehat.',
                    ];
                    $hasilKunjungan = $hasilKunjunganOptions[array_rand($hasilKunjunganOptions)];
                }

                DB::table('catatanibu')->insert([
                    'user_id' => $userId,
                    'tanggal_kunjungan' => $tanggalKunjungan->format('Y-m-d'),
                    'status_kunjungan' => $statusKunjungan,
                    'q1_demam' => $q1_demam,
                    'q2_pusing' => $q2_pusing,
                    'q3_sulit_tidur' => $q3_sulit_tidur,
                    'q4_risiko_tb' => $q4_risiko_tb,
                    'q5_gerakan_bayi' => $q5_gerakan_bayi,
                    'q6_nyeri_perut' => $q6_nyeri_perut,
                    'q7_cairan_jalan_lahir' => $q7_cairan_jalan_lahir,
                    'q8_sakit_kencing' => $q8_sakit_kencing,
                    'q9_diare' => $q9_diare,
                    'hasil_kunjungan' => $hasilKunjungan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Berhasil membuat ' . $ibuHamilUsers->count() . ' catatan ibu hamil dengan total ' . DB::table('catatanibu')->count() . ' records.');
    }
}
