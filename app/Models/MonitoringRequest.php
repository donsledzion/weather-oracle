<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringRequest extends Model
{
    // Status constants
    const STATUS_PENDING_VERIFICATION = 'pending_verification';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'location',
        'target_date',
        'email',
        'status',
        'verification_token',
        'dashboard_token',
        'expires_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forecastSnapshots(): HasMany
    {
        return $this->hasMany(ForecastSnapshot::class);
    }

    /**
     * Check if request is active (being monitored)
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if request is pending verification
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING_VERIFICATION;
    }

    /**
     * Check if request is completed (target date passed)
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Count active and pending requests for given email
     */
    public static function activeAndPendingCountForEmail(string $email): int
    {
        return self::where('email', $email)
            ->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_PENDING_VERIFICATION])
            ->count();
    }

    /**
     * Count active requests for given user
     */
    public static function activeCountForUser(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('status', self::STATUS_ACTIVE)
            ->count();
    }
}
