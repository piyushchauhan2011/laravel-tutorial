<?php

namespace App\Services;

use App\Models\Application;

class ApplicationFitScorer
{
    public function score(Application $application): int
    {
        $resumeLengthFactor = min(35, (int) floor(strlen($application->resume_text) / 50));
        $experienceFactor = min(40, $application->years_experience * 4);
        $sourceFactor = match ($application->source) {
            'referral' => 20,
            'career_site' => 15,
            'linkedin' => 10,
            'agency' => 8,
            default => 5,
        };

        return max(1, min(100, $resumeLengthFactor + $experienceFactor + $sourceFactor));
    }
}
