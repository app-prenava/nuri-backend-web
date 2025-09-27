<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnancyStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'minggu_ke',
        'bentuk_janin',
        'panjang_badan',
        'berat_badan',
        'perkembangan',
        'rekomendasi'
    ];
}
