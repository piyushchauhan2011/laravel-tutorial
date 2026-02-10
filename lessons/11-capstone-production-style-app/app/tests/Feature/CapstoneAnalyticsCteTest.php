<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\JobPost;
use App\Support\CapstoneFeatures;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class CapstoneAnalyticsCteTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_analytics_requires_feature_flag(): void
    {
        Feature::deactivate(CapstoneFeatures::ANALYTICS_V2_DASHBOARDS);

        $response = $this->getJson('/api/v1/analytics/pipeline');

        $response->assertForbidden()
            ->assertJsonPath('error.code', 'feature_disabled');
    }

    public function test_pipeline_analytics_returns_cte_aggregates_when_enabled(): void
    {
        Feature::activate(CapstoneFeatures::ANALYTICS_V2_DASHBOARDS);

        $job = JobPost::factory()->create();

        Application::factory()->create([
            'job_post_id' => $job->id,
            'stage' => Application::STAGE_APPLIED,
            'source' => 'career_site',
            'applied_at' => now()->subDay(),
        ]);

        Application::factory()->create([
            'job_post_id' => $job->id,
            'stage' => Application::STAGE_HIRED,
            'source' => 'referral',
            'applied_at' => now()->subHours(10),
        ]);

        $response = $this->getJson('/api/v1/analytics/pipeline?group_by=day');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['pipeline', 'conversion_by_source', 'referral_tree'],
                'meta' => ['from', 'to', 'group_by'],
            ]);
    }
}
