<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringRequest extends Model
{
    protected $fillable = [
        'location',
        'target_date',
        'email',
        'status',
    ];

    protected $casts = [
        'target_date' => 'date',
    ];

    public function forecastSnapshots(): HasMany
    {
        return $this->hasMany(ForecastSnapshot::class);
    }
}
