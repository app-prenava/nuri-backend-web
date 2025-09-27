<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomendasiMakanan extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_makanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'target_makanan',
    ];

    protected $casts = [
        'target_makanan' => 'array',
    ];
}
