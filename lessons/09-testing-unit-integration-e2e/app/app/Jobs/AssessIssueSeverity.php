<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Services\IssueSeverityScorer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AssessIssueSeverity implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $issueId,
    ) {
    }

    public function handle(IssueSeverityScorer $scorer): void
    {
        $issue = Issue::query()->find($this->issueId);

        if (! $issue) {
            return;
        }

        $score = $scorer->score($issue);

        $issue->update([
            'severity_score' => $score,
            'status' => $score >= 85 ? 'in_progress' : $issue->status,
        ]);
    }
}
