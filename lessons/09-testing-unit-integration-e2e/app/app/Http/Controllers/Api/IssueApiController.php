<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResolveIssueRequest;
use App\Http\Requests\StoreIssueRequest;
use App\Jobs\AssessIssueSeverity;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;

class IssueApiController extends Controller
{
    public function store(StoreIssueRequest $request): JsonResponse
    {
        $issue = Issue::query()->create([
            ...$request->validated(),
            'status' => 'open',
        ]);

        AssessIssueSeverity::dispatch($issue->id);

        return response()->json([
            'data' => $this->toPayload($issue),
            'meta' => [
                'queued_assessment' => true,
            ],
        ], 201);
    }

    public function show(Issue $issue): JsonResponse
    {
        return response()->json([
            'data' => $this->toPayload($issue),
        ]);
    }

    public function resolve(ResolveIssueRequest $request, Issue $issue): JsonResponse
    {
        $issue->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return response()->json([
            'data' => $this->toPayload($issue->refresh()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function toPayload(Issue $issue): array
    {
        return [
            'id' => $issue->id,
            'title' => $issue->title,
            'priority' => $issue->priority,
            'status' => $issue->status,
            'severity_score' => $issue->severity_score,
            'reported_by' => $issue->reported_by,
            'resolved_at' => $issue->resolved_at?->toDateTimeString(),
            'created_at' => $issue->created_at?->toDateTimeString(),
        ];
    }
}
