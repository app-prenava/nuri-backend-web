<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Icons;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Icons::create([
            'name' => 'Ibu hamil 1',
            'type' => 'Profile',
            'data' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/Foto+bumil/Ibu_hamil_1.jpg' 
        ]);

        Icons::create([
            'name' => 'Ibu hamil 2',
            'type' => 'material',
            'data' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/Foto+bumil/Ibu_hamil_2.jpg' 
        ]);

        Icons::create([
            'name' => 'Ibu hamil 3',
            'type' => 'material',
            'data' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/Foto+bumil/Ibu_hamil_3.jpg' 
        ]);

        Icons::create([
            'name' => 'Ibu hamil 4',
            'type' => 'material',
            'data' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/Foto+bumil/Ibu_hamil_4.jpg' 
        ]);

        Icons::create([
            'name' => 'Pasangan',
            'type' => 'url',
            'data' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/Foto+bumil/pasangan_1.jpg' 
        ]);

         Icons::create([
            'name' => 'Suami',
            'type' => 'svg_string',
            'data' => 'https://amazon-datazone-sehatiapp.s3.ap-southeast-1.amazonaws.com/Foto+bumil/suami_1.jpg' 
        ]);
    }
}