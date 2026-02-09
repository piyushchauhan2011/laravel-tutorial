<?php

use App\Jobs\AssessIssueSeverity;
use App\Models\Issue;
use Illuminate\Support\Facades\Queue;

it('submits issue via web form and redirects to details', function () {
    Queue::fake();

    $response = $this->post('/issues', [
        'title' => 'Payment button not working',
        'description' => str_repeat('Clicking pay does not trigger order placement. ', 2),
        'priority' => 'critical',
        'reported_by' => 'Support Engineer',
    ]);

    $issue = Issue::query()->firstOrFail();

    $response->assertRedirect(route('issues.show', $issue));
    Queue::assertPushed(AssessIssueSeverity::class, fn (AssessIssueSeverity $job) => $job->issueId === $issue->id);
});

it('processes severity assessment job and updates score', function () {
    $issue = Issue::factory()->create([
        'priority' => 'critical',
        'status' => 'open',
        'severity_score' => 0,
        'description' => str_repeat('High impact production outage. ', 8),
    ]);

    AssessIssueSeverity::dispatchSync($issue->id);

    $issue->refresh();

    expect($issue->severity_score)->toBeGreaterThan(0)
        ->and($issue->status)->toBe('in_progress');
});
