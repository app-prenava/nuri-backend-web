<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostpartumArticlesSeeder extends Seeder
{
    public function run()
    {
        DB::table('postpartum_articles')->insert([
            [
                'judul' => 'Perawatan Diri Setelah Melahirkan',
                'konten' => <<<EOT
                    <p><strong>Perawatan Diri Setelah Melahirkan</strong></p>

                    <p>Masa setelah melahirkan atau <em>postpartum</em> adalah fase penting bagi ibu untuk memulihkan tubuh sekaligus beradaptasi dengan peran baru sebagai orang tua. Perawatan diri yang tepat tidak hanya membantu proses pemulihan fisik, tetapi juga menjaga kesehatan mental.</p>

                    <p>Secara fisik, ibu perlu memperhatikan kebersihan area kewanitaan, cukup istirahat, serta konsumsi makanan bergizi untuk mempercepat penyembuhan dan mendukung produksi ASI. Jangan ragu untuk meminta bantuan orang terdekat agar ibu bisa beristirahat dan fokus pada pemulihan.</p>

                    <p>Selain itu, penting juga menjaga kesehatan mental. Perubahan hormon dan tekanan merawat bayi bisa memicu stres atau bahkan depresi postpartum. Bila merasa sedih berkepanjangan, cemas, atau tidak bersemangat, jangan ragu untuk berkonsultasi dengan tenaga kesehatan.</p>

                    <p>Meluangkan waktu untuk diri sendiri, seperti mandi dengan tenang, berjalan santai, atau sekadar tidur cukup, adalah bentuk perawatan diri yang tidak boleh diabaikan. Ingatlah bahwa ibu yang sehat secara fisik dan emosional akan lebih siap merawat bayinya dengan baik.</p>
                    EOT,
                'kategori' => 'Perawatan',
                'gambar'=> 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/artikel/perawatan.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'judul' => 'Tanda-Tanda Baby Blues dan Cara Mengatasinya',
                'konten' => <<<EOT
                    <p><strong>Tanda-Tanda Baby Blues dan Cara Mengatasinya</strong></p>

                    <p>Setelah melahirkan, sebagian ibu mengalami kondisi emosional yang dikenal sebagai <em>baby blues</em>. Ini adalah reaksi umum akibat perubahan hormon, kelelahan, dan tekanan merawat bayi baru lahir. Meski tergolong ringan, <em>baby blues</em> tetap perlu dikenali dan ditangani agar tidak berkembang menjadi depresi postpartum.</p>

                    <p><strong>Tanda-tanda baby blues</strong> antara lain:</p>
                    <ul>
                        <li>Mood yang mudah berubah</li>
                        <li>Mudah menangis tanpa alasan jelas</li>
                        <li>Perasaan cemas, cemas berlebihan, atau mudah tersinggung</li>
                        <li>Sulit tidur meski bayi sedang tidur</li>
                        <li>Merasa kewalahan atau tidak percaya diri sebagai ibu</li>
                    </ul>

                    <p>Kondisi ini biasanya muncul dalam beberapa hari setelah persalinan dan dapat berlangsung hingga dua minggu.</p>

                    <p><strong>Cara mengatasinya:</strong></p>
                    <ol>
                        <li><strong>Istirahat cukup</strong>: Tidurlah saat bayi tidur, meskipun hanya sebentar.</li>
                        <li><strong>Bercerita dengan orang terdekat</strong>: Jangan memendam perasaan sendiri. Berbagi cerita bisa meringankan beban emosional.</li>
                        <li><strong>Dukungan pasangan dan keluarga</strong>: Mintalah bantuan dalam mengurus bayi atau pekerjaan rumah.</li>
                        <li><strong>Jaga asupan gizi dan hidrasi</strong>: Tubuh yang sehat membantu kestabilan emosi.</li>
                        <li><strong>Luangkan waktu untuk diri sendiri</strong>: Mandi dengan tenang, dengarkan musik, atau lakukan hal sederhana yang menyenangkan.</li>
                    </ol>

                    <p>Jika perasaan sedih atau cemas tidak membaik setelah dua minggu, sebaiknya konsultasikan dengan tenaga kesehatan untuk mendapatkan penanganan lebih lanjut.</p>
                    EOT,
                'kategori' => 'Kesehatan Mental',
                'gambar'=> 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/artikel/babyblues.jpeg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'judul' => 'Nutrisi Penting untuk Ibu Nifas',
                'konten' => <<<EOT
                    <p><strong>Nutrisi Penting untuk Ibu Nifas</strong></p>

                    <p>Masa nifas adalah periode penting bagi ibu untuk memulihkan kondisi fisik pasca persalinan sekaligus memenuhi kebutuhan gizi untuk menyusui. Asupan nutrisi yang tepat akan membantu proses penyembuhan, memperkuat sistem imun, dan menjaga energi ibu.</p>

                    <p><strong>Berikut beberapa nutrisi penting yang perlu diperhatikan oleh ibu nifas:</strong></p>
                    <ul>
                        <li><strong>Protein</strong>: Membantu pemulihan jaringan tubuh dan mendukung produksi ASI. Sumbernya seperti telur, ikan, ayam, tahu, dan tempe.</li>
                        <li><strong>Zat besi</strong>: Mencegah anemia setelah kehilangan darah saat melahirkan. Dapat diperoleh dari hati ayam, bayam, daging merah, dan kacang-kacangan.</li>
                        <li><strong>Kalsium</strong>: Penting untuk kesehatan tulang dan mendukung produksi ASI. Sumbernya antara lain susu, keju, yogurt, dan sayuran hijau.</li>
                        <li><strong>Vitamin C</strong>: Membantu penyembuhan luka dan meningkatkan daya tahan tubuh. Terdapat dalam jeruk, tomat, jambu, dan brokoli.</li>
                        <li><strong>Serat dan cairan</strong>: Membantu melancarkan pencernaan dan mencegah sembelit. Konsumsi buah, sayur, dan air putih yang cukup setiap hari.</li>
                    </ul>

                    <p>Ibu nifas juga sebaiknya menghindari makanan tinggi gula, makanan instan, dan kafein berlebih, karena dapat memengaruhi kualitas ASI dan kondisi tubuh. Jika memungkinkan, konsultasikan kebutuhan gizi dengan tenaga kesehatan untuk hasil terbaik.</p>
                    EOT,
                'kategori' => 'Nutrisi',
                'gambar'=> 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/artikel/makannifas.jpeg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
