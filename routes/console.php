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

// Send daily summary emails (daily at 8:00 AM)
Schedule::command('notifications:send-daily-summaries')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

// Send final summary emails for newly completed requests (daily after marking completed)
Schedule::command('notifications:send-final-summaries')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();

// Maintain public monitors for demo purposes (daily)
Schedule::command('monitors:maintain-public')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();
