<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaranMakananSeeder extends Seeder
{
    public function run()
    {
        DB::table('rekomendasi_makanan')->insert([
            [
                'nama' => 'Sayur Bayam',
                'deskripsi' => 'Sayur bayam kaya akan zat besi dan sangat baik untuk ibu hamil dan menyusui.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/sayur_bening.jpg',
                'target_makanan' => json_encode(['Hamil', 'Menyusui']),
            ],
            [
                'nama' => 'Ikan Salmon',
                'deskripsi' => 'Ikan salmon mengandung omega-3 yang bermanfaat untuk perkembangan otak janin.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/ikan_salmon.jpg',
                'target_makanan' => json_encode(['Hamil']),
            ],
            [
                'nama' => 'Susu Almond',
                'deskripsi' => 'Susu almond adalah alternatif susu yang menyehatkan bagi ibu menyusui.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/susu_almond.jpg',
                'target_makanan' => json_encode(['Menyusui']),
            ],
            [
                'nama' => 'Telur Rebus',
                'deskripsi' => 'Telur rebus mengandung protein tinggi yang baik untuk pertumbuhan janin.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/telur_rebus.webp',
                'target_makanan' => json_encode(['Hamil']),
            ],
            [
                'nama' => 'Buah Alpukat',
                'deskripsi' => 'Alpukat kaya akan folat dan lemak sehat, penting untuk ibu hamil.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/alpukat.jpg',
                'target_makanan' => json_encode(['Hamil']),
            ],
            [
                'nama' => 'Kacang Merah',
                'deskripsi' => 'Kacang merah mengandung zat besi dan protein yang baik untuk ibu menyusui.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/kacang_merah.jpg',
                'target_makanan' => json_encode(['Menyusui']),
            ],
            [
                'nama' => 'Oatmeal',
                'deskripsi' => 'Oatmeal adalah sumber energi yang baik dan dapat meningkatkan kualitas ASI.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/oatmeal.jpg',
                'target_makanan' => json_encode(['Menyusui']),
            ],
            [
                'nama' => 'Yogurt Plain',
                'deskripsi' => 'Yogurt mengandung kalsium dan probiotik yang baik untuk pencernaan ibu hamil.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/yogurt_plain.jpg',
                'target_makanan' => json_encode(['Hamil']),
            ],
            [
                'nama' => 'Smoothie Buah Segar',
                'deskripsi' => 'Smoothie buah mengandung berbagai vitamin penting untuk ibu hamil dan menyusui.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/smoothies.jpg',
                'target_makanan' => json_encode(['Hamil', 'Menyusui']),
            ],
            [
                'nama' => 'Sup Ayam Kampung',
                'deskripsi' => 'Sup ayam kampung dapat membantu meningkatkan daya tahan tubuh selama masa menyusui.',
                'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/rekomenmakanan/sup_ayam_kampung.jpg',
                'target_makanan' => json_encode(['Menyusui']),
            ],
        ]);

    }
}
