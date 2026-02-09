<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ops:scheduler:heartbeat --source=schedule')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('ops:metrics:daily')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('ops:failed-jobs:cleanup --days=14')
    ->dailyAt('01:30')
    ->withoutOverlapping()
    ->onOneServer();
