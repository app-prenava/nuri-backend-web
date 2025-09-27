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
        'kategori', 
        'nama_makanan', 
        'deskripsi', 
        'cocok', 
        'hemoglobin', 
        'hasil_deteksi'
    ];
    

}
