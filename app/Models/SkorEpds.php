<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkorEpds extends Model
{
    use HasFactory;

    protected $table = 'skor_epds';
    
    protected $fillable = [
        'prediksi_depresi_id',
        'answers',
        'score',
        'hasil_epds'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'answers' => 'array',
    ];

    /**
     * Get the prediksi depresi record associated with the EPDS score.
     */
    public function prediksiDepresi()
    {
        return $this->belongsTo(PrediksiDepresi::class, 'prediksi_depresi_id');
    }
    
    /**
     * Calculate and set hasil_epds based on score
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // If hasil_epds is not set, determine it based on score
            if (empty($model->hasil_epds)) {
                $model->hasil_epds = $model->score >= 10 ? 'Possible Depression' : 'No Depression';
            }
        });
    }
}