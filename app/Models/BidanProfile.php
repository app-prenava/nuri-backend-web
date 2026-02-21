<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BidanProfile extends Model
{
    protected $table = 'bidan_profile';
    protected $primaryKey = 'bidan_profile_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'photo',
        'tempat_praktik',
        'alamat_praktik',
        'kota_tempat_praktik',
        'kecamatan_tempat_praktik',
        'telepon_tempat_praktik',
        'spesialisasi',
    ];

    /**
     * Get the user that owns this profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
