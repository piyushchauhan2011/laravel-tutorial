<?php

namespace Tests\Feature;

use App\Models\OperationalMetric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SchedulerAndCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_metrics_command_persists_idempotent_snapshot(): void
    {
        $this->artisan('ops:metrics:daily', ['--date' => '2026-02-10'])
            ->assertSuccessful();

        $this->artisan('ops:metrics:daily', ['--date' => '2026-02-10'])
            ->assertSuccessful();

        $count = OperationalMetric::query()
            ->where('metric_name', 'daily_platform_snapshot')
            ->whereDate('metric_date', '2026-02-10')
            ->count();

        $this->assertSame(1, $count);
    }

    public function test_daily_metrics_dry_run_does_not_persist(): void
    {
        $this->artisan('ops:metrics:daily', ['--dry-run' => true])
            ->expectsOutputToContain('Dry run: metrics computed')
            ->assertSuccessful();

        $this->assertDatabaseCount('operational_metrics', 0);
    }

    public function test_failed_jobs_cleanup_dry_run_and_cleanup(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid' => (string) str()->uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'demo',
            'failed_at' => now()->subDays(30),
        ]);

        $this->artisan('ops:failed-jobs:cleanup', ['--days' => 14, '--dry-run' => true])
            ->expectsOutputToContain('Dry run: 1 failed jobs')
            ->assertSuccessful();

        $this->assertDatabaseCount('failed_jobs', 1);

        $this->artisan('ops:failed-jobs:cleanup', ['--days' => 14])
            ->expectsOutputToContain('Deleted 1 failed jobs')
            ->assertSuccessful();

        $this->assertDatabaseCount('failed_jobs', 0);
    }

    public function test_scheduler_heartbeat_command_records_run(): void
    {
        $this->artisan('ops:scheduler:heartbeat', ['--source' => 'manual'])
            ->assertSuccessful();

        $this->assertDatabaseHas('scheduled_task_runs', [
            'task_name' => 'scheduler_heartbeat',
            'status' => 'ok',
        ]);
    }

    public function test_schedule_list_contains_registered_tasks(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('ops:scheduler:heartbeat')
            ->expectsOutputToContain('ops:metrics:daily')
            ->expectsOutputToContain('ops:failed-jobs:cleanup')
            ->assertSuccessful();
    }

    public function test_scheduler_registration_includes_expected_cron_expressions(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('*  * * * *')
            ->expectsOutputToContain('0  1 * * *')
            ->expectsOutputToContain('30 1 * * *')
            ->assertSuccessful();
    }

    public function test_scheduler_status_endpoint_returns_operational_data(): void
    {
        $this->artisan('ops:scheduler:heartbeat')->assertSuccessful();
        $this->artisan('ops:metrics:daily')->assertSuccessful();

        $response = $this->getJson('/scheduler/status');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'latest_metric',
                'recent_scheduler_runs',
                'queue' => ['pending_jobs', 'failed_jobs'],
            ],
        ]);
    }
}
