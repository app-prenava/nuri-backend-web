<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentConsent extends Model
{
    protected $table = 'appointment_consents';
    protected $primaryKey = 'consent_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'appointment_id',
        'consent_version',
        'consent_text_snapshot',
        'shared_fields',
        'accepted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'shared_fields' => 'array',
        'accepted_at' => 'datetime',
    ];

    // Current consent version
    const CURRENT_VERSION = '1.0';

    // Default consent text
    const DEFAULT_CONSENT_TEXT = 'Dengan menyetujui pernyataan ini, Anda mengizinkan data pribadi Anda untuk dibagikan kepada bidan yang Anda pilih untuk keperluan konsultasi dan kunjungan kesehatan. Data yang dibagikan meliputi informasi yang Anda pilih dalam formulir ini. Bidan akan menggunakan data tersebut semata-mata untuk memberikan layanan kesehatan yang Anda minta. Anda dapat mencabut persetujuan ini kapan saja dengan membatalkan janji temu.';

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the appointment
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    /**
     * Check if specific field is shared
     */
    public function isFieldShared(string $field): bool
    {
        return isset($this->shared_fields[$field]) && $this->shared_fields[$field] === true;
    }

    /**
     * Get list of shared fields
     */
    public function getSharedFieldsList(): array
    {
        if (!is_array($this->shared_fields)) {
            return [];
        }

        return array_keys(array_filter($this->shared_fields, fn($v) => $v === true));
    }

    /**
     * Create consent record
     */
    public static function createForAppointment(
        int $userId,
        int $appointmentId,
        array $sharedFields,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'appointment_id' => $appointmentId,
            'consent_version' => self::CURRENT_VERSION,
            'consent_text_snapshot' => self::DEFAULT_CONSENT_TEXT,
            'shared_fields' => $sharedFields,
            'accepted_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
