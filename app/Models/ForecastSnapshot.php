<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForecastSnapshot extends Model
{
    protected $fillable = [
        'monitoring_request_id',
        'weather_provider_id',
        'forecast_data',
        'fetched_at',
    ];

    protected $casts = [
        'forecast_data' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function monitoringRequest(): BelongsTo
    {
        return $this->belongsTo(MonitoringRequest::class);
    }

    public function weatherProvider(): BelongsTo
    {
        return $this->belongsTo(WeatherProvider::class);
    }
}
