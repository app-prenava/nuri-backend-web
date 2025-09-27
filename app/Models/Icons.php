<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icons extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'data', // Tambahkan jika Anda menggunakannya
    ];

    /**
     * Get the users that have selected this icon.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'selected_icon_id');
    }
}