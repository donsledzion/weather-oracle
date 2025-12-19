<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NotificationPreference extends Model
{
    protected $fillable = [
        'email',
        'user_id',
        'token',
        'first_snapshot_enabled',
        'daily_summary_enabled',
        'final_summary_enabled',
    ];

    protected $casts = [
        'first_snapshot_enabled' => 'boolean',
        'daily_summary_enabled' => 'boolean',
        'final_summary_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create notification preferences for email
     */
    public static function getForEmail(string $email): self
    {
        return self::firstOrCreate(
            ['email' => $email],
            ['token' => Str::random(64)]
        );
    }

    /**
     * Get or create notification preferences for user
     */
    public static function getForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['token' => Str::random(64)]
        );
    }

    /**
     * Get notification preferences by token
     */
    public static function getByToken(string $token): ?self
    {
        return self::where('token', $token)->first();
    }

    /**
     * Check if any notification type is enabled
     */
    public function hasAnyEnabled(): bool
    {
        return $this->first_snapshot_enabled
            || $this->daily_summary_enabled
            || $this->final_summary_enabled;
    }
}
