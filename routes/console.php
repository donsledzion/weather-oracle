<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch weather forecasts every 6 hours (only for active requests)
Schedule::command('forecasts:fetch')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

// Mark pending requests as expired if not verified within 2 hours (every 10 minutes)
Schedule::command('requests:mark-expired')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onOneServer();

// Mark active requests as completed when target date has passed (daily)
Schedule::command('requests:mark-completed')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();
