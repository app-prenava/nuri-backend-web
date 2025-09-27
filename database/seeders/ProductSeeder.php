<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product; // Import model Product
use Illuminate\Support\Facades\DB; // Jika ingin menggunakan DB Facade

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada (opsional, hati-hati jika sudah ada data penting)
        // Product::truncate(); // atau DB::table('products')->delete();

        Product::create([
            'produk' => 'Baby Walker alas Babywalker fimily',
            'deskripsi' => 'Premium Fold Up Baby Walker 5 in 1, Baby Walker yang dapat di lipat dengan system Soft Closing.',
            'harga' => 253000.000,
            'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/produk/stroller.png',
            'link' => 'https://id.shp.ee/H68N6Ps'
        ]);

        Product::create([
            'produk' => 'V-BABYCARE Stroller Baby',
            'deskripsi' => 'kereta aluminium aloi, aman, kuat dan seimbang.',
            'harga' => 199000.200,
            'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/produk/stroller_1.png',
            'link' => 'https://id.shp.ee/MVvQXos'
        ]);

        Product::create([
            'produk' => 'Alamii Puffs Snack 25gr ',
            'deskripsi' => 'Alamii Puffs - Cemilan / Snack Anak.',
            'harga' => 18500.500,
            'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/produk/alami.png',
            'link' => 'https://id.shp.ee/zsQPbQS'
        ]);

        Product::create([
            'produk' => 'Babygrow - Kaldu Tulang Ayam Organik MPASI',
            'deskripsi' => 'FORMULA KHUSUS BAYI, MPASI, ANAK, IBU HAMIL, DAN MENYUSUI.',
            'harga' => 89000.991,
            'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/produk/grow.png',
            'link' => 'https://id.shp.ee/E7wx7HQ'
        ]);

        Product::create([
            'produk' => 'Think Baby Gendongan Bayi Hipseat Karakter',
            'deskripsi' => 'Think Baby Gendongan Hipseat Karakter.',
            'harga' => 94000.990,
            'gambar' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/produk/hipseat.png',
            'link' => 'https://id.shp.ee/dCtaRUq'
        ]);

    }
}