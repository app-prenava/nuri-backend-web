<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrediksiDepresi extends Model
{
    use HasFactory;

    protected $table = 'prediksi_depresi';

    protected $fillable = [
        'user_id',
        'umur',
        'merasa_sedih',
        'mudah_tersinggung',
        'masalah_tidur',
        'masalah_fokus',
        'pola_makan',
        'merasa_bersalah',
        'suicide_attempt',
        'hasil_prediksi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function epds()
    {
        return $this->hasOne(SkorEpds::class, 'prediksi_depresi_id');
    }

    // Mapping Accessors
    public function getUmurTextAttribute()
    {
        $mapping = [
            0 => '0-30',
            1 => '31-35',
            2 => '36-40',
            3 => '41-45',
            4 => '46-50',
        ];
        return $mapping[$this->umur] ?? 'Tidak diketahui';
    }

    public function getMerasaSedihTextAttribute()
    {
        return [0 => 'Tidak', 1 => 'Ya', 2 => 'Kadang-kadang'][$this->merasa_sedih] ?? 'Tidak diketahui';
    }

    public function getMudahTersinggungTextAttribute()
    {
        return [0 => 'Tidak', 1 => 'Ya', 2 => 'Kadang-kadang'][$this->mudah_tersinggung] ?? 'Tidak diketahui';
    }

    public function getMasalahTidurTextAttribute()
    {
        return [0 => 'Tidak', 1 => 'Ya', 2 => 'Dua hari dalam seminggu/lebih'][$this->masalah_tidur] ?? 'Tidak diketahui';
    }

    public function getMasalahFokusTextAttribute()
    {
        return [0 => 'Tidak', 1 => 'Ya', 2 => 'Sering'][$this->masalah_fokus] ?? 'Tidak diketahui';
    }

    public function getPolaMakanTextAttribute()
    {
        return [2 => 'Tidak sama sekali', 0 => 'Kadang-kadang', 1 => 'Ya'][$this->pola_makan] ?? 'Tidak diketahui';
    }

    public function getMerasaBersalahTextAttribute()
    {
        return [0 => 'Tidak', 1 => 'Ya', 2 => 'Mungkin'][$this->merasa_bersalah] ?? 'Tidak diketahui';
    }

    public function getSuicideAttemptTextAttribute()
    {
        return [0 => 'Tidak', 1 => 'Ya', 2 => 'Tidak ingin menjawab'][$this->suicide_attempt] ?? 'Tidak diketahui';
    }

    public function getHasilPrediksiTextAttribute()
    {
        return $this->hasil_prediksi ? 'Bergejala Depresi' : 'Tidak Bergejala Depresi';
    }

}
