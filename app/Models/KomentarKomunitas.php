<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarKomunitas extends Model
{
    use HasFactory;

    protected $table = 'komentarkomunitas';

    protected $fillable = [
        'post_id',
        'user_id',
        'komentar',
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'post_id');
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }
}
