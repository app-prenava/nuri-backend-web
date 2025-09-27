<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komunitas extends Model
{
    use HasFactory;

    protected $table = 'komunitas';
    protected $primaryKey = 'post_id';

    protected $fillable = [
        'user_id', 
        'judul', 
        'deskripsi', 
        'gambar', 
        'apresiasi',
        'komen'
    ];

    public function komentar()
    {
        return $this->hasMany(KomentarKomunitas::class, 'id');
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

}
