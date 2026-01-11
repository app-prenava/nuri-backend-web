<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PregnancyTipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate tables first (disable foreign key checks temporarily)
        Schema::disableForeignKeyConstraints();
        DB::table('pregnancy_tips')->truncate();
        DB::table('tip_categories')->truncate();
        Schema::enableForeignKeyConstraints();

        // Get admin user ID (assuming admin exists)
        $adminId = DB::table('users')->where('role', 'admin')->value('user_id');

        if (!$adminId) {
            $this->command->warn('No admin user found. Please create an admin user first.');
            return;
        }

        // Insert Categories
        $categories = [
            [
                'name' => 'Nutrisi',
                'slug' => 'nutrisi',
                'icon_name' => 'nutrition',
                'icon_url' => null,
                'description' => 'Tips nutrisi dan makanan sehat untuk ibu hamil',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'icon_name' => 'exercise',
                'icon_url' => null,
                'description' => 'Panduan olahraga aman selama kehamilan',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Perawatan Kehamilan',
                'slug' => 'perawatan-kehamilan',
                'icon_name' => 'care',
                'icon_url' => null,
                'description' => 'Tips perawatan diri selama masa kehamilan',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Persiapan Persalinan',
                'slug' => 'persiapan-persalinan',
                'icon_name' => 'birth',
                'icon_url' => null,
                'description' => 'Persiapan menghadapi proses persalinan',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Kesehatan Mental',
                'slug' => 'kesehatan-mental',
                'icon_name' => 'mental',
                'icon_url' => null,
                'description' => 'Menjaga kesehatan mental selama kehamilan',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('tip_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Tip categories seeded successfully.');

        // Get category IDs
        $categoryIdMap = [
            'nutrisi' => DB::table('tip_categories')->where('slug', 'nutrisi')->value('id'),
            'olahraga' => DB::table('tip_categories')->where('slug', 'olahraga')->value('id'),
            'perawatan-kehamilan' => DB::table('tip_categories')->where('slug', 'perawatan-kehamilan')->value('id'),
            'persiapan-persalinan' => DB::table('tip_categories')->where('slug', 'persiapan-persalinan')->value('id'),
            'kesehatan-mental' => DB::table('tip_categories')->where('slug', 'kesehatan-mental')->value('id'),
        ];

        // Insert Tips for each category

        // Nutrisi Tips
        $nutrisiTips = [
            [
                'judul' => 'Pentingnya Asam Folat untuk Ibu Hamil',
                'konten' => 'Asam folat sangat penting untuk perkembangan neural tube janin. Konsumsi 400-800 mcg asam folat setiap hari, terutama pada trimester pertama. Sumber makanan kaya asam folat meliputi: sayuran hijau tua (bayam, brokoli), kacang-kacangan, dan sereal yang difortifikasi.',
                'order' => 1,
            ],
            [
                'judul' => 'Kebutuhan Protein Selama Kehamilan',
                'konten' => 'Protein penting untuk pertumbuhan sel jaringan janin dan plasenta. Ibu hamil membutuhkan sekitar 70-100 gram protein per hari. Konsumsi protein hewani (daging ayam, ikan, telur) dan nabati (tahu, tempe, kacang merah) secara seimbang.',
                'order' => 2,
            ],
            [
                'judul' => 'Pentingnya Zat Besi untuk Cegah Anemia',
                'konten' => 'Kebutuhan zat besi meningkat dua kali lipat selama kehamilan untuk mendukung peningkatan volume darah. Konsumsi makanan kaya zat besi seperti daging merah, hati ayam, bayam, dan lentil. Vitamin C dari jeruk atau tomat membantu penyerapan zat besi yang lebih baik.',
                'order' => 3,
            ],
            [
                'judul' => 'Konsumsi Ikan yang Aman untuk Ibu Hamil',
                'konten' => 'Ikan merupakan sumber omega-3 yang baik untuk perkembangan otak janin. Pilih ikan yang rendah merkuri seperti salmon, sarden, dan tuna. Hindari ikan predator tinggi seperti hiu, swordfish, dan king mackerel. Batasi konsumsi ikan hingga 2-3 porsi per minggu.',
                'order' => 4,
            ],
            [
                'judul' => 'Hindari Makanan Rawan Kontaminasi',
                'konten' => 'Selama hamil, hindari makanan mentah atau setengah matang seperti sushi, sashimi, telur mentah, dan susu tidak pasteurisasi. Makanan ini dapat mengandung bakteri seperti Listeria dan Salmonella yang berbahaya untuk janin.',
                'order' => 5,
            ],
        ];

        // Olahraga Tips
        $olahragaTips = [
            [
                'judul' => 'Jalan Kaki: Olahraga Teraman untuk Ibu Hamil',
                'konten' => 'Jalan kaki 30 menit setiap hari sangat bermanfaat untuk meningkatkan sirkulasi darah, mengurangi bengkak, dan menjaga berat badan tetap sehat. Gunakan sepatu yang nyaman dan hindari jalanan yang tidak rata. Pastikan untuk tetap terhidrasi selama berjalan.',
                'order' => 1,
            ],
            [
                'judul' => 'Prenatal Yoga untuk Relaksasi',
                'konten' => 'Prenatal yoga membantu mengurangi stres, meningkatkan fleksibilitas, dan mempersiapkan tubuh untuk persalinan. Fokus pada gerakan yang lembut dan teknik pernapasan dalam. Hindari pose yang menekan perut atau memerlukan keseimbangan berlebih.',
                'order' => 2,
            ],
            [
                'judul' => 'Berenang untuk Olahraga Low Impact',
                'konten' => 'Berenang adalah olahraga ideal karena mendukung berat badan dan mengurangi tekanan pada sendi. Air membantu meringankan rasa tidak nyaman di punggung dan kaki. Berenang juga meningkatkan kapasitas kardiovaskular yang penting untuk persalinan.',
                'order' => 3,
            ],
            [
                'judul' => 'Senam Hamil untuk Persiapan Persalinan',
                'konten' => 'Senam hamil difokuskan untuk memperkuat otot panggul dan punggung bawah. Latihan ini membantu mengurangi nyeri punggung, memperbaiki postur, dan mempersiapkan otot-otot yang dibutuhkan saat proses persalinan. Ikuti kelas dengan instruktur tersertifikasi.',
                'order' => 4,
            ],
            [
                'judul' => 'Tanda Berhenti Berolahraga',
                'konten' => 'Segera hentikan olahraga jika mengalami: pusing, sesak napas, nyeri dada, kontraksi teratur, atau perdarahan vagina. Konsultasikan dengan dokter kandungan sebelum memulai atau melanjutkan program olahraga selama kehamilan.',
                'order' => 5,
            ],
        ];

        // Perawatan Kehamilan Tips
        $perawatanTips = [
            [
                'judul' => 'Merawat Kulit yang Berubah Selama Kehamilan',
                'konten' => 'Hormon kehamilan dapat menyebabkan kulit menjadi lebih sensitif dan munculnya stretch mark. Gunakan pelembab yang mengandung vitamin E, minyak almond, atau cocoa butter. Lindungi kulit dari sinar matahari dengan SPF minimal 30 dan hindari produk skincare dengan retinol atau salicylic acid.',
                'order' => 1,
            ],
            [
                'judul' => 'Mengatasi Morning Sickness',
                'konten' => 'Mual dan m Morning sickness bisa diatasi dengan: makan biskuit kering sebelum bangun tidur, makan porsi kecil tapi sering (6-8 kali sehari), hindari makanan berminyak dan berbau kuat, dan minum air putih secara perlahan sepanjang hari. Vitamin B6 juga dapat membantu mengurangi mual.',
                'order' => 2,
            ],
            [
                'judul' => 'Tidur yang Nyaman dengan Posisi Menghadap Kiri',
                'konten' => 'Tidur miring ke kiri meningkatkan aliran darah ke plasenta dan janin. Gunakan bantal hamil untuk menopang perut dan di antara kaki. Hindari tidur telentang setelah trimester kedua karena dapat menekan vena cava dan menyebabkan penurunan tekanan darah.',
                'order' => 3,
            ],
            [
                'judul' => 'Perawatan Gigi dan Gusi Selama Hamil',
                'konten' => 'Hormon kehamilan dapat membuat gusi lebih sensitif dan mudah berdarah. Gosok gigi dengan lembut dua kali sehari dan gunakan dental floss secara teratur. Tetap lakukan pemeriksaan gigi rutin dan beri tahu dokter gigi bahwa Anda sedang hamil. X-ray sebaiknya ditunda setelah melahirkan.',
                'order' => 4,
            ],
            [
                'judul' => 'Mengatasi Bengkak pada Kaki dan Tangan',
                'konten' => 'Edema atau pembengkakan adalah normal selama kehamilan. Kurangi dengan: mengangkat kaki saat beristirahat, hindari berdiri terlalu lama, minum cukup air, kurangi konsumsi garam, dan gunakan stocking kompresi jika dianjurkan dokter. Hubungi dokter jika bengkak tiba-tiba dan hebat.',
                'order' => 5,
            ],
        ];

        // Persiapan Persalinan Tips
        $persalinanTips = [
            [
                'judul' => 'Membuat Rencana Persalinan (Birth Plan)',
                'konten' => 'Birth plan adalah dokumen yang berisi preferensi persalinan Anda. Tuliskan: siapa yang boleh mendampingi, jenis persalinan yang diinginkan, metode manajemen nyeri, dan preferensi untuk episiotomi atau induksi. Diskusikan dengan dokter kandungan dan suami sejak minggu ke-28.',
                'order' => 1,
            ],
            [
                'judul' => 'Menyiapkan Tas Persalinan',
                'konten' => 'Siapkan tas persalinan sejak minggu ke-36. Isi dengan: dokumen penting (KTP, kartu prenatal), baju ganti ibu dan bayi, pembalut pasca operasi, bra menyusui, perlengkapan bayi (popok, pakaian, selimut), kamera/phone charger, dan snack. Jangan lupa bawa botol air dan bantal nyaman.',
                'order' => 2,
            ],
            [
                'judul' => 'Teknik Pernapasan untuk Persalinan',
                'konten' => 'Latihan pernapasan membantu mengelola nyeri kontraksi. Teknik dasar: tarik napas dalam perlahan melalui hidung selama 4 detik, tahan selama 2 detik, lalu hembuskan melalui mulut selama 6 detik. Praktikkan teknik ini setiap hari sejak trimester ketiga bersama pasangan.',
                'order' => 3,
            ],
            [
                'judul' => 'Tanda-Tanda Akan Melahirkan',
                'konten' => 'Tanda persalinan sudah dekat: kontraksi teratur dan semakin kuat (setiap 5-10 menit), ketuban pecah (cairan jernih atau kehijauan), lendir bercampur darah (bloody show), dan tekanan di panggul. Segera ke rumah sakit jika mengalami tanda-tanda ini atau pergerakan bayi berkurang.',
                'order' => 4,
            ],
            [
                'judul' => 'Peran Suami saat Persalinan',
                'konten' => 'Suami memiliki peran penting sebagai pendamping. Dukung dengan cara: memijat punggung saat kontraksi, membantu dengan teknik pernapasan, menyiapkan handuk dan air minum, berikan semangat dan kata-kata positif, dan komunikasikan kebutuhan istri ke tenaga medis. Ikut kelas persiapan persalinan bersama.',
                'order' => 5,
            ],
        ];

        // Kesehatan Mental Tips
        $mentalTips = [
            [
                'judul' => 'Mengelola Kecemasan Selama Kehamilan',
                'konten' => 'Kecemasan tentang kesehatan bayi adalah normal. Kelola dengan: tidur cukup 7-9 jam, olahraga ringan, meditasi atau doa, dan jangan ragu konsultasi ke profesional. Ingat bahwa stres berlebih dapat mempengaruhi kehamilan, jadi prioritaskan kesehatan mental Anda.',
                'order' => 1,
            ],
            [
                'judul' => 'Bangun Sistem Dukungan yang Kuat',
                'konten' => 'Ibu hamil membutuhkan dukungan dari keluarga dan teman. Jangan ragu meminta bantuan untuk pekerjaan rumah, temani ke dokter, atau sekadar didengarkan. Bergabung dengan komunitas ibu hamil juga bisa memberikan dukungan emosional dan informasi berharga.',
                'order' => 2,
            ],
            [
                'judul' => 'Bonding dengan Janin dalam Kandungan',
                'konten' => 'Mulai bonding sejak dini dengan: sering berbicara atau membacakan buku pada bayi, putar musik lembut, sentuh perut dengan lembut saat bayi menendang, dan luangkan waktu untuk merasakan gerakan bayi. Kontak ini membantu perkembangan emosional bayi setelah lahir.',
                'order' => 3,
            ],
            [
                'judul' => 'Menghadapi Perubahan Mood',
                'konten' => 'Perubahan hormon dapat menyebabkan mood swing yang ekstrem. Ini adalah normal. Hadapi dengan: jangan terlalu keras pada diri sendiri, luangkan waktu untuk hobi, jaga komunikasi terbuka dengan pasangan, dan konsultasikan ke dokter jika mood swing mengganggu aktivitas sehari-hari.',
                'order' => 4,
            ],
            [
                'judul' => 'Persiapan Mental Menjadi Ibu Baru',
                'konten' => 'Transisi menjadi ibu adalah perubahan besar. Persiapkan mental dengan: membaca buku parenting, diskusikan pembagian tugas dengan pasangan, realistis dengan ekspektasi, dan terima bahwa tidak ada ibu yang sempurna. Fokus pada memberikan yang terbaik, bukan sempurna.',
                'order' => 5,
            ],
        ];

        // Combine all tips with category IDs
        $allTips = [
            ...array_map(fn($tip) => array_merge($tip, [
                'category_id' => $categoryIdMap['nutrisi'],
                'created_by' => $adminId,
                'is_published' => true,
            ]), $nutrisiTips),
            ...array_map(fn($tip) => array_merge($tip, [
                'category_id' => $categoryIdMap['olahraga'],
                'created_by' => $adminId,
                'is_published' => true,
            ]), $olahragaTips),
            ...array_map(fn($tip) => array_merge($tip, [
                'category_id' => $categoryIdMap['perawatan-kehamilan'],
                'created_by' => $adminId,
                'is_published' => true,
            ]), $perawatanTips),
            ...array_map(fn($tip) => array_merge($tip, [
                'category_id' => $categoryIdMap['persiapan-persalinan'],
                'created_by' => $adminId,
                'is_published' => true,
            ]), $persalinanTips),
            ...array_map(fn($tip) => array_merge($tip, [
                'category_id' => $categoryIdMap['kesehatan-mental'],
                'created_by' => $adminId,
                'is_published' => true,
            ]), $mentalTips),
        ];

        // Insert all tips
        foreach ($allTips as $tip) {
            DB::table('pregnancy_tips')->insert(array_merge($tip, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Pregnancy tips seeded successfully. Total: ' . count($allTips) . ' tips.');
    }
}
