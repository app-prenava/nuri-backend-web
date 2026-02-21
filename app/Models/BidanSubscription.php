<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BidanSubscription extends Model
{
    protected $table = 'bidan_subscriptions';
    protected $primaryKey = 'bidan_subscription_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'bidan_application_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'status',
        'amount_paid',
        'payment_reference',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the bidan user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the application
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(BidanApplication::class, 'bidan_application_id', 'bidan_application_id');
    }

    /**
     * Get the subscription plan
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id', 'subscription_plan_id');
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Check if subscription is currently active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE 
            && Carbon::parse($this->end_date)->gte(Carbon::today());
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED 
            || Carbon::parse($this->end_date)->lt(Carbon::today());
    }

    /**
     * Get remaining days
     */
    public function getRemainingDaysAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return Carbon::today()->diffInDays(Carbon::parse($this->end_date), false);
    }

    /**
     * Mark subscription as expired
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }
}
