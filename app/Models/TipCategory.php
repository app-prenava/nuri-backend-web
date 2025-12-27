<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipCategory extends Model
{
    use HasFactory;

    protected $table = 'tip_categories';

    protected $fillable = [
        'name',
        'slug',
        'icon_name',
        'icon_url',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relasi ke PregnancyTip
     */
    public function tips()
    {
        return $this->hasMany(PregnancyTip::class, 'category_id');
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

