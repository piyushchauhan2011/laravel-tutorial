<?php

namespace App\Console\Commands;

use App\Models\OperationalMetric;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateDailyMetricsCommand extends Command
{
    protected $signature = 'ops:metrics:daily
                            {--date= : Metric date in Y-m-d format}
                            {--dry-run : Calculate metrics without persisting}';

    protected $description = 'Generate or update idempotent daily operational metrics';

    public function handle(): int
    {
        $dateValue = $this->option('date') ?: now()->toDateString();
        $metricDate = CarbonImmutable::parse($dateValue)->startOfDay();
        $metricDateKey = $metricDate->format('Y-m-d H:i:s');
        $dryRun = (bool) $this->option('dry-run');

        $payload = [
            'users_total' => User::query()->count(),
            'jobs_pending' => DB::table('jobs')->count(),
            'jobs_failed' => DB::table('failed_jobs')->count(),
            'captured_at' => now()->toIso8601String(),
        ];

        if ($dryRun) {
            $this->info("Dry run: metrics computed for {$metricDate->toDateString()}");
            $this->line(json_encode($payload, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        }

        $metric = OperationalMetric::query()->updateOrCreate(
            [
                'metric_date' => $metricDateKey,
                'metric_name' => 'daily_platform_snapshot',
            ],
            [
                'payload' => $payload,
            ]
        );

        Log::info('Daily operational metrics generated', [
            'metric_id' => $metric->id,
            'metric_date' => $metricDate->toDateString(),
        ]);

        $this->info("Metrics persisted for {$metricDate->toDateString()} (id: {$metric->id}).");

        return self::SUCCESS;
    }
}
