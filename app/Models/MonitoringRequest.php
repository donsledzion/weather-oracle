<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
