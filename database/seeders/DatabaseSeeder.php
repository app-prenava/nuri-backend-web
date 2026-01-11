<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            DB::table('users')->insert([
                'name'       => 'Admin Prenava',
                'email'      => 'admin@prenava.com',
                'password'   => Hash::make('password123'),
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $adminDinkesId = DB::table('users')->insertGetId([
                'name'       => 'Admin Dinkes',
                'email'      => 'dinkes@prenava.com',
                'password'   => Hash::make('password123'),
                'role'       => 'dinkes',
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('user_dinkes')->insert([
                'user_id'    => $adminDinkesId,
                'photo'      => 'profiles/dinkes/photo.jpg',
                'jabatan'    => 'Keuangan',
                'nip'        => '1234567890',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $ibuHamilId = DB::table('users')->insertGetId([
                'name'       => 'User Hamil',
                'email'      => 'hamil@prenava.com',
                'password'   => Hash::make('password123'),
                'role'       => 'ibu_hamil',
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('user_profile')->insert([
                'user_id'              => $ibuHamilId,
                'photo'                => 'profiles/ibu/photo.jpg',
                'tanggal_lahir'        => '1995-03-15',
                'alamat'               => 'Jl. Melati No. 10',
                'usia'                 => 29,
                'no_telepon'           => null,
                'pendidikan_terakhir'  => null,
                'pekerjaan'            => null,
                'golongan_darah'       => null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);

            $bidanId = DB::table('users')->insertGetId([
                'name'       => 'Bidan Rita',
                'email'      => 'bidan.rita@prenava.com',
                'password'   => Hash::make('password123'),
                'role'       => 'bidan',
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('bidan_profile')->insert([
                'user_id'                   => $bidanId,
                'photo'                     => 'profiles/bidan/photo.jpg',
                'tempat_praktik'            => 'Klinik Sehati',
                'alamat_praktik'            => 'Jl. Mawar No. 5',
                'kota_tempat_praktik'       => 'Bandung',
                'kecamatan_tempat_praktik'  => 'Coblong',
                'telepon_tempat_praktik'    => '081234567890',
                'spesialisasi'              => 'Kebidanan Umum',
                'created_at'                => $now,
                'updated_at'                => $now,
            ]);

            $this->call([
                IconSeeder::class,
                KomunitasSeeder::class,
                PostpartumArticlesSeeder::class,
                SaranMakananSeeder::class,
                ProductSeeder::class,
                IbuHamilSeeder::class,
                KomunitasPostSeeder::class,
                CatatanKunjunganSeeder::class,
                ShopSeeder::class,
                ShopReviewSeeder::class,
                KomunitasLikeSeeder::class,
                PregnancyTipsSeeder::class,
            ]);
        });
    }
}
