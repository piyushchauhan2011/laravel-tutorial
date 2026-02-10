<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class CapstoneFeatureFlagApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_lists_flags_in_standard_envelope(): void
    {
        $response = $this->getJson('/api/v1/feature-flags');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['feature', 'label', 'enabled'],
                ],
                'meta' => ['count'],
            ]);
    }

    public function test_api_can_toggle_a_flag(): void
    {
        Feature::deactivate('ai_triage_assistant');

        $response = $this->patchJson('/api/v1/feature-flags/ai_triage_assistant', [
            'enabled' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.feature', 'ai_triage_assistant')
            ->assertJsonPath('data.enabled', true);

        $this->assertTrue(Feature::active('ai_triage_assistant'));
    }

    public function test_api_rejects_unknown_features(): void
    {
        $response = $this->patchJson('/api/v1/feature-flags/unknown_flag', [
            'enabled' => true,
        ]);

        $response->assertNotFound()
            ->assertJsonPath('error.code', 'feature_not_found');
    }
}
