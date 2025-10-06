<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    /**
     * JWT identifier → pakai user_id, bukan id
     */
    public function getJWTIdentifier()
    {
        return $this->attributes[$this->primaryKey];
    }

    /**
     * Custom claims JWT → kosongkan, karena kita isi manual saat login
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Role checker manual (opsional masih dipakai)
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isBidan(): bool
    {
        return $this->hasRole('bidan');
    }

    public function isDinkes(): bool
    {
        return $this->hasRole('dinkes');
    }

    /**
     * Contoh relasi icon kalau memang masih dipakai
     */
    public function selectedIcon(): BelongsTo
    {
        return $this->belongsTo(Icons::class, 'selected_icon_id', 'id');
    }
}
