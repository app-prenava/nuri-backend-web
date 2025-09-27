<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\icons;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
        'role',

        //IBU HAMIL
        'tanggal_lahir',
        'usia',
        'alamat',
        'nomor_telepon',
        'pendidikan_terakhir',
        'pekerjaan',
        'golongan_darah',
        'nama_suami',
        'telepon_suami',
        'usia_suami',
        'pekerjaan_suami',
        'saldo_total',
        'usia_kehamilan',
        'selected_icon_id',
        'selected_icon_name_cache',
        'selected_icon_data_cache',
        
        // BIDAN
        'nomor_str',
        'masa_berlaku_str',
        'nomor_sipb',
        'masa_berlaku_sipb',
        'tempat_praktik',
        'alamat_praktik',
        'telepon_tempat_praktik',
        'spesialisasi',
        'nik',
        // DINKES
        'nama_dinas',
        'alamat_kantor',
        'website',
        'logo',
        'nama_admin',
        'nip',
        'jabatan',
        'foto_ktp',
        'admin_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Implementasi method dari JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

     public function selectedIcon(): BelongsTo 
    {
        
        return $this->belongsTo(Icons::class, 'selected_icon_id', 'id');
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is a bidan
     * 
     * @return bool
     */
    public function isBidan()
    {
        return $this->hasRole('bidan');
    }

    /**
     * Check if user is dinkes staff
     * 
     * @return bool
     */
    public function isDinkes()
    {
        return $this->hasRole('dinkes');
    }
}