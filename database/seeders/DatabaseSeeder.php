<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Dinas Kesehatan
        User::create([
            'name' => 'Admin Dinkes',
            'email' => 'admin@dinkes.go.id',
            'password' => Hash::make('password123'),
            'role' => 'dinkes',
            'alamat' => 'Jl. Kesehatan No. 1',
            'saldo_total' => 0,
            'admin_id' => 1,
        ]);

        // Contoh data ibu hamil
        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => Hash::make('password123'),
            'role' => 'ibu_hamil',
            'tanggal_lahir' => '1995-03-15',
            'alamat' => 'Jl. Melati No. 10',
        ]);

        // Jalankan semua seeder lainnya
        $this->call([
            IconSeeder::class,
            KomunitasSeeder::class,
            PostpartumArticlesSeeder::class,
            SaranMakananSeeder::class,
            // WaterIntakeSeeder::class,
            ProductSeeder::class
        ]);
    }
}
