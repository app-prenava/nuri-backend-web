<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatatanKunjunganSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Ambil semua user dengan role ibu_hamil
        $ibuHamilUsers = DB::table('users')->where('role', 'ibu_hamil')->pluck('user_id')->toArray();

        if (empty($ibuHamilUsers)) {
            $this->command->warn('No ibu_hamil users found. Please run IbuHamilSeeder first.');
            return;
        }

        // Data kunjungan dengan pertanyaan bidan
        $kunjunganTemplates = [
            [
                'keluhan' => 'Mual dan muntah di trimester pertama, terutama pagi hari',
                'pertanyaan' => 'Berapa kali ibu merasa mual dalam sehari? Apa yang memicu mual tersebut?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan vitamin B6 dan saran untuk makan dalam porsi kecil tapi sering. Kondisi ibu dan janin baik.'
            ],
            [
                'keluhan' => 'Sering merasa lelah dan cepat capek beraktivitas sehari-hari',
                'pertanyaan' => 'Berapa jam istirahat ibu per hari? Apakah ibu masih bekerja?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dianjurkan istirahat cukup dan konsumsi makanan bergizi. Diberikan suplemen zat besi.'
            ],
            [
                'keluhan' => 'Nyeri punggung bawah dan pegal di area pinggang',
                'pertanyaan' => 'Apakah ibu sering mengangkat beban berat? Posisi tidur bagaimana?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan teknik pijat ringan dan disarankan menggunakan bantal hamil saat tidur.'
            ],
            [
                'keluhan' => 'Sering buang air kecil terutama malam hari',
                'pertanyaan' => 'Apakah ada nyeri saat buang air kecil? Warna urine seperti apa?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Pemeriksaan urine normal. Dijelaskan bahwa ini akibat tekanan rahim pada kandung kemih.'
            ],
            [
                'keluhan' => 'Kaki bengkak terutama di malam hari',
                'pertanyaan' => 'Apakah bengkak disertai dengan sakit kepala atau penglihatan kabur?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Tekanan darah normal. Disarankan kurangi konsumsi garam dan tinggikan kaki saat istirahat.'
            ],
            [
                'keluhan' => 'Sesak napas terutama saat berbaring',
                'pertanyaan' => 'Sejak kapan ibu merasakan sesak? Apakah ada riwayat asma?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Kondisi janin baik. Disarankan tidur dengan posisi miring ke kiri.'
            ],
            [
                'keluhan' => 'Nafsu makan menurun drastis',
                'pertanyaan' => 'Berapa berat badan turun dalam sebulan terakhir? Ada mual muntah tidak?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan resep obat anti-mual dan edukasi pentingnya nutrisi untuk janin.'
            ],
            [
                'keluhan' => 'Konstipasi atau susah buang air besar',
                'pertanyaan' => 'Berapa hari ibu tidak BAB? Pola makan seperti apa?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dianjurkan perbanyak serat (sayur, buah), minum air putih cukup, dan olahraga ringan.'
            ],
            [
                'keluhan' => 'Wasir yang menyakitkan',
                'pertanyaan' => 'Apakah ada darah saat BAB? Sejak kapan keluhan ini muncul?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan obat oles dan saran perawatan. Disarankan hindaran mengejan terlalu kuat.'
            ],
            [
                'keluhan' => 'Heartburn atau rasa panas di dada',
                'pertanyaan' => 'Kapan biasanya heartburn muncul? Setelah makan apa?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan tentang GERD akibat tekanan rahim. Dianjurkan makan sedikit demi sedikit dan hindari makanan pedas/asam.'
            ],
            [
                'keluhan' => 'Insomnia atau susah tidur malam',
                'pertanyaan' => 'Apa yang membuat ibu sulit tidur? Apakah sering buang air kecil?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan teknik relaksasi dan saran posisi tidur yang nyaman dengan bantal.'
            ],
            [
                'keluhan' => 'Kram kaki di malam hari',
                'pertanyaan' => 'Apakah ibu minum suplemen kalsium? Kapan kram biasanya terjadi?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan suplemen kalsium tambahan dan teknik peregangan kaki sebelum tidur.'
            ],
            [
                'keluhan' => 'Gatal-gatal di area perut dan paha',
                'pertanyaan' => 'Apakah ada ruam merah? Ibu menggunakan krim apa untukStretch mark?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan tentang stretch mark dan perubahan hormonal. Diberikan losion yang aman untuk ibu hamil.'
            ],
            [
                'keluhan' => 'Sering sakit kepala',
                'pertanyaan' => 'Apakah sakit kepala disertai pandangan kabur? Tekanan darah berapa?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Tekanan darah normal. Dianjurkan istirahat cukup dan kompres dingin. Dilarang minum obat sakit kepala sembarangan.'
            ],
            [
                'keluhan' => 'Perut terasa kencang dan keras',
                'pertanyaan' => 'Berapa lama ketegangan berlangsung? Apakah ada kontraksi teratur?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan tentang Braxton Hicks kontraksi. Dianjurkan istirahat dan monitoring pergerakan janin.'
            ],
            [
                'keluhan' => 'Gerakan janin berkurang',
                'pertanyaan' => 'Berapa kali ibu merasakan gerakan janin dalam sehari? Terakhir kapan?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dilakukan pemeriksaan USG. Kondisi janin baik dan sehat. Dijelaskan cara menghitung gerakan janin.'
            ],
            [
                'keluhan' => 'Flu atau batuk yang tidak kunjung sembuh',
                'pertanyaan' => 'Sudah berapa hari sakit? Ada demam tidak? Obat apa yang sudah diminum?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan obat yang aman untuk ibu hamil. Dianjurkan banyak minum dan istirahat.'
            ],
            [
                'keluhan' => 'Sariawan di mulut yang menyakitkan',
                'pertanyaan' => 'Apa ibu minum suplemen vitamin? Berapa lama sariawan ini ada?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan vitamin B kompleks dan obat kumur. Dianjurkan perbanyak vitamin C.'
            ],
            [
                'keluhan' => 'Nyeri saat buang air kecil',
                'pertanyaan' => 'Apakah urine berwarna keruh atau anyir? Ada demam tidak?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diduga infeksi saluran kemih. Diberikan antibiotik yang aman untuk ibu hamil dan dianjurkan minum air putih banyak.'
            ],
            [
                'keluhan' => 'Pendarahan flek atau bercak darah',
                'pertanyaan' => 'Berapa banyak pendarahan? Apakah disertai kram perut?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Segera dilakukan pemeriksaan USG. Janin dalam kondisi baik. Dianjurkan bed rest dan monitoring ketat.'
            ],
            [
                'keluhan' => 'Cemas dan mudah marah',
                'pertanyaan' => 'Apa yang membuat ibu cemas? Apakah ibu punya dukungan keluarga?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan tentang perubahan hormonal. Diberikan informasi tentang kelas ibu hamil dan dukungan psikologis.'
            ],
            [
                'keluhan' => 'Rasa sakit di area pinggul dan panggul',
                'pertanyaan' => 'Sakit dirasakan kapan? Apakah saat berjalan atau saat duduk?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan tentang relaksasi sendi panggul persiapan persalinan. Diberikan teknik olahraga ringan.'
            ],
            [
                'keluhan' => ' Kulit wajah berjerawat',
                'pertanyaan' => 'Apakah ibu menggunakan produk skincare apa? Sejak kapan jerawat muncul?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan tentang perubahan hormon. Dianjurkan gunakan skincare yang aman untuk ibu hamil dan hindari produk yang mengandung retinol.'
            ],
            [
                'keluhan' => 'Gula darah tinggi (gestational diabetes)',
                'pertanyaan' => 'Berapa gula darah puasa dan 2 jam setelah makan? Ada riwayat diabetes keluarga?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan edukasi diet rendah gula danjadwal makan teratur. Dianjurkan monitoring gula darah rutin.'
            ],
            [
                'keluhan' => 'Anemia (Hb rendah)',
                'pertanyaan' => 'Berapa kadar Hb terakhir? Ibu sudah minum suplemen zat besi?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan suplemen zat besi dan vitamin C. Dianjurkan konsumsi makanan kaya zat besi seperti daging merah, bayam, dan kacang-kacangan.'
            ],
            [
                'keluhan' => 'Posisi sungsang atau kepala belum masuk panggul',
                'pertanyaan' => 'Usia kehamilan berapa minggu sekarang? Sudah pernah USG tidak?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Dijelaskan bahwa masih ada waktu untuk janin berputar. Diberikan teknik posisi tidur dan latihan untuk membantu janin berputar.'
            ],
            [
                'keluhan' => 'Preeklamsia (tekanan darah tinggi)',
                'pertanyaan' => 'Berapa tekanan darah terakhir? Ada bengkak di wajah dan tangan tidak?',
                'status_kunjungan' => 'Selesai',
                'hasil_kunjungan' => 'Diberikan obat anti-hipertensi yang aman untuk ibu hamil. Dianjurkan bed rest dan monitoring ketat tekanan darah.'
            ]
        ];

        $totalCatatan = 0;

        // Generate 2-4 catatan kunjungan untuk setiap ibu hamil
        foreach ($ibuHamilUsers as $userId) {
            // Pilih 2-4 template kunjungan secara acak
            $numKunjungan = rand(2, 4);
            $selectedKunjungan = array_rand($kunjunganTemplates, $numKunjungan);

            foreach ((array)$selectedKunjungan as $index) {
                $template = $kunjunganTemplates[$index];

                // Generate tanggal kunjungan yang bervariasi dalam 6 bulan terakhir
                $daysAgo = rand(1, 180);
                $tanggalKunjungan = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

                DB::table('catatankunjungan')->insert([
                    'tanggal_kunjungan' => $tanggalKunjungan,
                    'keluhan' => $template['keluhan'],
                    'pertanyaan' => $template['pertanyaan'],
                    'status_kunjungan' => $template['status_kunjungan'],
                    'hasil_kunjungan' => $template['hasil_kunjungan'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $totalCatatan++;
            }
        }

        $this->command->info("Successfully created {$totalCatatan} catatan kunjungan for " . count($ibuHamilUsers) . " ibu hamil users.");
    }
}
