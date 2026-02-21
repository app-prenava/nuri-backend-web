<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BidanLocation extends Model
{
    protected $table = 'bidan_locations';
    protected $primaryKey = 'bidan_location_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'bidan_id',
        'lat',
        'lng',
        'address_label',
        'phone_override',
        'notes',
        'operating_hours',
        'is_active',
        'is_primary',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'operating_hours' => 'array',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the bidan user
     */
    public function bidan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bidan_id', 'user_id');
    }

    /**
     * Get appointments at this location
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'bidan_location_id', 'bidan_location_id');
    }

    /**
     * Scope to get only active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get primary locations
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Calculate distance from given coordinates (in KM)
     * Using Haversine formula
     */
    public function distanceFrom(float $lat, float $lng): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->lat);
        $lngFrom = deg2rad($this->lng);
        $latTo = deg2rad($lat);
        $lngTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Scope to filter by radius (km) from given coordinates
     */
    public function scopeWithinRadius($query, float $lat, float $lng, float $radiusKm)
    {
        // Approximate bounding box for initial filtering
        $latDelta = $radiusKm / 111; // 1 degree lat ≈ 111 km
        $lngDelta = $radiusKm / (111 * cos(deg2rad($lat)));

        return $query->whereBetween('lat', [$lat - $latDelta, $lat + $latDelta])
                     ->whereBetween('lng', [$lng - $lngDelta, $lng + $lngDelta]);
    }

    /**
     * Get effective phone (override or bidan's phone)
     */
    public function getEffectivePhoneAttribute(): ?string
    {
        if ($this->phone_override) {
            return $this->phone_override;
        }
        
        // Get from bidan profile if available
        $bidanProfile = $this->bidan?->bidanProfile;
        return $bidanProfile?->telepon_tempat_praktik ?? null;
    }
}
