<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class CapstoneFeatureFlagWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_feature_flags(): void
    {
        $response = $this->get('/capstone');

        $response->assertOk()
            ->assertSee('Capstone Control Center')
            ->assertSee('analytics_v2_dashboards')
            ->assertSee('ai_triage_assistant')
            ->assertSee('canary_release_banner');
    }

    public function test_web_toggle_updates_feature_flag_state(): void
    {
        Feature::deactivate('analytics_v2_dashboards');

        $response = $this->post('/capstone/flags/analytics_v2_dashboards', [
            'enabled' => 1,
        ]);

        $response->assertRedirect('/capstone');
        $this->assertTrue(Feature::active('analytics_v2_dashboards'));
    }
}
