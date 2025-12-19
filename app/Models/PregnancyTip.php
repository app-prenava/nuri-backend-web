<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnancyTip extends Model
{
    use HasFactory;

    protected $table = 'pregnancy_tips';

    protected $fillable = [
        'category_id',
        'created_by',
        'judul',
        'konten',
        'is_published',
        'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relasi ke TipCategory
     */
    public function category()
    {
        return $this->belongsTo(TipCategory::class, 'category_id');
    }

    /**
     * Relasi ke User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Scope untuk tips yang published
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
