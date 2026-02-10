<?php

use App\Models\Application;
use App\Models\ApplicationStatusEvent;
use App\Models\JobPost;
use App\Support\CapstoneFeatures;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Laravel\Pennant\Feature;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('capstone:flags', function () {
    $rows = [];

    foreach (CapstoneFeatures::all() as $feature) {
        $rows[] = [
            'feature' => $feature,
            'active' => Feature::active($feature) ? 'yes' : 'no',
        ];
    }

    $this->table(['feature', 'active'], $rows);
})->purpose('Display current capstone feature-flag states');

Artisan::command('capstone:daily-metrics', function () {
    $rows = [
        ['metric' => 'open_jobs', 'value' => JobPost::query()->where('status', 'open')->count()],
        ['metric' => 'applications_today', 'value' => Application::query()->whereDate('applied_at', today())->count()],
        ['metric' => 'hired_total', 'value' => Application::query()->where('stage', Application::STAGE_HIRED)->count()],
    ];

    $this->table(['metric', 'value'], $rows);
})->purpose('Show daily capstone hiring metrics');

Artisan::command('capstone:stale-applications {days=14}', function (int $days) {
    $cutoff = now()->subDays($days);

    $staleApplications = Application::query()
        ->whereIn('stage', [Application::STAGE_APPLIED, Application::STAGE_SCREENING])
        ->whereNotNull('applied_at')
        ->where('applied_at', '<=', $cutoff)
        ->get();

    DB::transaction(function () use ($staleApplications): void {
        foreach ($staleApplications as $application) {
            $from = $application->stage;

            $application->update([
                'stage' => Application::STAGE_STALE,
                'reviewed_at' => now(),
            ]);

            ApplicationStatusEvent::query()->create([
                'application_id' => $application->id,
                'from_stage' => $from,
                'to_stage' => Application::STAGE_STALE,
                'changed_by' => 'scheduler',
                'notes' => 'Auto-marked stale by scheduler.',
                'changed_at' => now(),
            ]);
        }
    });

    $this->info(sprintf('Marked %d applications as stale.', $staleApplications->count()));
})->purpose('Mark stale applications based on inactivity threshold');

Artisan::command('capstone:scheduler-health', function () {
    $this->table(
        ['name', 'value'],
        [
            ['scheduler_alive', 'yes'],
            ['checked_at', now()->toIso8601String()],
        ]
    );
})->purpose('Scheduler heartbeat and health check');

Schedule::command('capstone:daily-metrics')->dailyAt('01:05');
Schedule::command('capstone:stale-applications 14')->dailyAt('01:10');
Schedule::command('capstone:scheduler-health')->everyFifteenMinutes();
