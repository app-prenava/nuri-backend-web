<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'usia_ibu',
        'tekanan_darah',
        'riwayat_persalinan',
        'posisi_janin',
        'riwayat_kesehatan_ibu',
        'kondisi_kesehatan_janin',
        'metode_persalinan',
        'faktor',      // Satu string, contoh: 'posisi janin'
        'confidence',  // Float, contoh: 70.0
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hpl()
    {
        return $this->hasOne(\App\Models\PregnancyCalculator::class, 'user_id', 'user_id')->latestOfMany();
    }

    protected $casts = [
        'confidence' => 'float', // Konversi otomatis ke float
    ];
}
