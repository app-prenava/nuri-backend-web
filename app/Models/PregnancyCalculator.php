<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnancyCalculator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hpht',
        'hpl',
    ];

    /**
     * Relasi: Setiap data HPL dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
