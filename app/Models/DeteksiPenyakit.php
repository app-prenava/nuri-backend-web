<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeteksiPenyakit extends Model
{
    use HasFactory;

    protected $table = 'deteksipenyakit';
    protected $primaryKey = 'deteksi_id';

    protected $fillable = [
        'nama',
        'pregnancies', 
        'bs', 
        'blood_pressure', 
        'skin_thickness', 
        'bmi', 
        'age', 
        'sex', 
        'current_smoker', 
        'cigs_per_day', 
        'bp_meds', 
        'systolic_bp', 
        'diastolic_bp', 
        'heart_rate', 
        'body_temp', 
        'user_id',
    
        // Kolom hasil prediksi
        'diabetes_prediction', 
        'hypertension_prediction', 
        'maternal_health_prediction'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
