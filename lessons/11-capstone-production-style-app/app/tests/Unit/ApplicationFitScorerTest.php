<?php

namespace Tests\Unit;

use App\Models\Application;
use App\Services\ApplicationFitScorer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationFitScorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_fit_scorer_returns_score_in_valid_range(): void
    {
        $application = Application::factory()->make([
            'source' => 'referral',
            'years_experience' => 8,
            'resume_text' => str_repeat('Complex backend architecture and mentoring.', 8),
        ]);

        $score = app(ApplicationFitScorer::class)->score($application);

        $this->assertGreaterThanOrEqual(1, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}
