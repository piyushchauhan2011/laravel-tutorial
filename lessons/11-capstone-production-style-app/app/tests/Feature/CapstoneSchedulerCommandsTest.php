<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapstoneSchedulerCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_health_command_runs(): void
    {
        $this->artisan('capstone:scheduler-health')
            ->assertSuccessful()
            ->expectsOutputToContain('scheduler_alive');
    }

    public function test_stale_applications_command_marks_old_records_as_stale(): void
    {
        $application = Application::factory()->create([
            'stage' => Application::STAGE_APPLIED,
            'applied_at' => now()->subDays(30),
        ]);

        $this->artisan('capstone:stale-applications 14')
            ->assertSuccessful();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'stage' => Application::STAGE_STALE,
        ]);
    }
}
