<?php

namespace App\Jobs;

use App\Models\Application;
use App\Services\ApplicationFitScorer;
use App\Support\CapstoneFeatures;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\Pennant\Feature;

class ScoreApplicationFitJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $applicationId)
    {
    }

    public function handle(ApplicationFitScorer $scorer): void
    {
        if (! Feature::active(CapstoneFeatures::AI_TRIAGE_ASSISTANT)) {
            return;
        }

        $application = Application::query()->find($this->applicationId);

        if (! $application) {
            return;
        }

        $application->update([
            'fit_score' => $scorer->score($application),
            'reviewed_at' => now(),
        ]);
    }
}
