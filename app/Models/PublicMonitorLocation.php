<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicMonitorLocation extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'is_active',
        'max_concurrent_monitors',
        'days_ahead',
        'stagger_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Get active monitors for this location
     */
    public function activeMonitors()
    {
        return $this->hasMany(MonitoringRequest::class, 'location', 'name')
            ->where('is_public', true)
            ->whereIn('status', ['active', 'pending_verification']);
    }

    /**
     * Get completed monitors for this location
     */
    public function completedMonitors()
    {
        return $this->hasMany(MonitoringRequest::class, 'location', 'name')
            ->where('is_public', true)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');
    }
}
