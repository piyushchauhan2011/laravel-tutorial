<?php

namespace App\Services;

use App\Models\Issue;
use App\ValueObjects\IssuePriority;

class IssueSeverityScorer
{
    public function score(Issue $issue): int
    {
        $priorityWeight = IssuePriority::from($issue->priority)->weight();
        $contentFactor = min(strlen($issue->title) + strlen($issue->description), 120);

        return min(100, (int) round(($priorityWeight * 0.75) + ($contentFactor * 0.25)));
    }
}
