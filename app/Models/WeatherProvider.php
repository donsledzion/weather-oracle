<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherProvider extends Model
{
    protected $fillable = [
        'name',
        'configuration',
        'is_active',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];
}
