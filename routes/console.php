<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch weather forecasts every 12 hours
Schedule::command('forecasts:fetch')
    ->everyTwoHours()
    ->withoutOverlapping()
    ->onOneServer();
