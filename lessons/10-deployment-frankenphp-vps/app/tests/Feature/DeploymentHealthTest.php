<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_reports_ok(): void
    {
        $response = $this->get('/health');

        $response->assertOk();
        $response->assertJsonPath('status', 'ok');
        $response->assertJsonPath('database', 'ok');
    }

    public function test_ops_health_command_runs(): void
    {
        Artisan::call('scheduler:heartbeat');

        $this->artisan('ops:health')
            ->expectsOutputToContain('Pending jobs:')
            ->expectsOutputToContain('Failed jobs:')
            ->expectsOutputToContain('Scheduler heartbeat:')
            ->assertExitCode(0);
    }
}
