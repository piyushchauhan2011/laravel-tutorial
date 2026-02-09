<?php

use App\Jobs\AssessIssueSeverity;
use App\Models\Issue;
use Illuminate\Support\Facades\Queue;

it('creates issue via api and dispatches severity assessment job', function () {
    Queue::fake();

    $response = $this->postJson('/api/v1/issues', [
        'title' => 'Checkout latency is very high',
        'description' => str_repeat('The checkout endpoint is timing out under load. ', 3),
        'priority' => 'high',
        'reported_by' => 'QA Bot',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Checkout latency is very high')
        ->assertJsonPath('data.status', 'open')
        ->assertJsonPath('meta.queued_assessment', true);

    Queue::assertPushed(AssessIssueSeverity::class);

    $this->assertDatabaseHas('issues', [
        'title' => 'Checkout latency is very high',
        'priority' => 'high',
    ]);
});

it('validates required fields for issue api create', function () {
    $response = $this->postJson('/api/v1/issues', [
        'title' => '',
        'description' => 'tiny',
        'priority' => 'urgent',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'description', 'priority']);
});

it('resolves issue via api', function () {
    $issue = Issue::factory()->create([
        'status' => 'open',
        'resolved_at' => null,
    ]);

    $response = $this->patchJson("/api/v1/issues/{$issue->id}/resolve", []);

    $response->assertOk()
        ->assertJsonPath('data.status', 'resolved');

    $this->assertDatabaseHas('issues', [
        'id' => $issue->id,
        'status' => 'resolved',
    ]);
});
