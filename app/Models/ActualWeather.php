<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActualWeather extends Model
{
    protected $table = 'actual_weather';

    protected $fillable = [
        'monitoring_request_id',
        'actual_data',
        'fetched_at',
    ];

    protected $casts = [
        'actual_data' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function monitoringRequest(): BelongsTo
    {
        return $this->belongsTo(MonitoringRequest::class);
    }
}
