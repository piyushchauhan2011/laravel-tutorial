<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ops:health', function () {
    $pendingJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
    $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
    $lastHeartbeat = Cache::get('scheduler:last-heartbeat', 'never');

    $this->info('Deployment Health');
    $this->line("Pending jobs: {$pendingJobs}");
    $this->line("Failed jobs: {$failedJobs}");
    $this->line("Scheduler heartbeat: {$lastHeartbeat}");
})->purpose('Show queue and scheduler runtime health');

Artisan::command('scheduler:heartbeat', function () {
    $timestamp = now()->toDateTimeString();
    Cache::put('scheduler:last-heartbeat', $timestamp, now()->addHours(2));
    $this->info("Heartbeat recorded at {$timestamp}");
})->purpose('Record scheduler heartbeat timestamp');

Schedule::command('scheduler:heartbeat')->everyMinute();
