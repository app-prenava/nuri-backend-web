<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jumlah_ml',
        'tanggal', // ✅ gunakan tanggal sesuai dengan kolom di tabel
    ];

    /**
     * Relasi: setiap catatan air minum dimiliki oleh satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
