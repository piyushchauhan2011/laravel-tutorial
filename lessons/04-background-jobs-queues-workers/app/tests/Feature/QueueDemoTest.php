<?php

namespace Tests\Feature;

use App\Jobs\GenerateExportReportJob;
use App\Models\ExportReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class QueueDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_endpoint_queues_job(): void
    {
        Queue::fake();
        User::factory()->create();

        $response = $this->postJson('/queue-demo/reports', [
            'topic' => 'weekly-product-metrics',
        ]);

        $response->assertStatus(202);

        Queue::assertPushed(GenerateExportReportJob::class, 1);
        $this->assertDatabaseHas('export_reports', [
            'topic' => 'weekly-product-metrics',
            'status' => 'queued',
        ]);
    }

    public function test_dispatch_endpoint_validates_topic(): void
    {
        User::factory()->create();

        $response = $this->postJson('/queue-demo/reports', []);

        $response->assertStatus(422);
    }

    public function test_job_handle_marks_report_completed(): void
    {
        $report = ExportReport::factory()->create([
            'topic' => 'active-users-summary',
            'status' => 'queued',
            'attempt_count' => 0,
        ]);

        $job = new GenerateExportReportJob($report->id, [
            'topic' => 'active-users-summary',
            'should_fail' => false,
        ]);

        $job->handle();

        $report->refresh();

        $this->assertSame('completed', $report->status);
        $this->assertNotNull($report->summary);
        $this->assertNotNull($report->processed_at);
        $this->assertSame(1, $report->attempt_count);
    }

    public function test_job_failed_marks_report_failed(): void
    {
        $report = ExportReport::factory()->create([
            'status' => 'queued',
            'attempt_count' => 0,
        ]);

        $job = new GenerateExportReportJob($report->id, [
            'topic' => 'queue-performance-report',
            'should_fail' => true,
        ]);

        try {
            $job->handle();
            $this->fail('Expected runtime exception.');
        } catch (RuntimeException $exception) {
            $job->failed($exception);
        }

        $report->refresh();

        $this->assertSame('failed', $report->status);
        $this->assertStringContainsString('Simulated export failure', (string) $report->error_message);
    }

    public function test_retry_endpoint_redispatches_failed_report(): void
    {
        Queue::fake();

        $report = ExportReport::factory()->failed()->create();

        $response = $this->postJson("/queue-demo/reports/{$report->id}/retry");

        $response->assertOk();

        Queue::assertPushed(GenerateExportReportJob::class, 1);

        $this->assertDatabaseHas('export_reports', [
            'id' => $report->id,
            'status' => 'queued',
            'error_message' => null,
        ]);
    }

    public function test_queue_health_command_prints_counts(): void
    {
        $this->artisan('queue:health')
            ->expectsOutputToContain('Pending jobs:')
            ->expectsOutputToContain('Failed jobs:')
            ->assertSuccessful();
    }
}
