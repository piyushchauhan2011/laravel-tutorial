<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateExportReportJob;
use App\Models\ExportReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueDemoReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'should_fail' => ['sometimes', 'boolean'],
        ]);

        $user = User::query()->firstOrFail();

        $report = ExportReport::query()->create([
            'user_id' => $user->id,
            'topic' => $validated['topic'],
            'status' => 'queued',
        ]);

        GenerateExportReportJob::dispatch($report->id, [
            'topic' => $validated['topic'],
            'should_fail' => (bool) ($validated['should_fail'] ?? false),
        ]);

        return response()->json([
            'data' => $report->fresh(),
            'meta' => [
                'queue_connection' => config('queue.default'),
                'hint' => 'Run php artisan queue:work --tries=3 --backoff=5 in a separate terminal.',
            ],
        ], 202);
    }

    public function show(ExportReport $exportReport): JsonResponse
    {
        return response()->json([
            'data' => $exportReport,
        ]);
    }

    public function retry(ExportReport $exportReport): JsonResponse
    {
        if ($exportReport->status !== 'failed') {
            return response()->json([
                'error' => [
                    'code' => 'invalid_state',
                    'message' => 'Only failed reports can be retried.',
                    'details' => ['status' => $exportReport->status],
                ],
            ], 422);
        }

        $exportReport->update([
            'status' => 'queued',
            'error_message' => null,
            'summary' => null,
            'processed_at' => null,
        ]);

        GenerateExportReportJob::dispatch($exportReport->id, [
            'topic' => $exportReport->topic,
            'should_fail' => false,
        ]);

        return response()->json([
            'data' => $exportReport->fresh(),
            'meta' => [
                'message' => 'Retry dispatched.',
            ],
        ]);
    }
}
