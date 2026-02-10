<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\StoreJobPostRequest;
use App\Http\Requests\UpdateApplicationStageRequest;
use App\Jobs\ScoreApplicationFitJob;
use App\Jobs\SendApplicationReceivedNotificationJob;
use App\Models\Application;
use App\Models\ApplicationStatusEvent;
use App\Models\JobPost;
use Illuminate\Http\JsonResponse;

class CapstoneJobApiController extends Controller
{
    public function indexJobs(): JsonResponse
    {
        $jobs = JobPost::query()->latest()->get();

        return response()->json([
            'data' => $jobs,
            'meta' => [
                'count' => $jobs->count(),
            ],
        ]);
    }

    public function storeJob(StoreJobPostRequest $request): JsonResponse
    {
        $job = JobPost::query()->create([
            ...$request->validated(),
            'is_remote' => $request->boolean('is_remote'),
            'published_at' => now(),
        ]);

        return response()->json([
            'data' => $job,
            'meta' => [
                'created' => true,
            ],
        ], 201);
    }

    public function showJob(JobPost $job): JsonResponse
    {
        return response()->json([
            'data' => $job->loadCount('applications'),
            'meta' => [],
        ]);
    }

    public function indexApplications(JobPost $job): JsonResponse
    {
        $applications = $job->applications()->latest()->get();

        return response()->json([
            'data' => $applications,
            'meta' => [
                'count' => $applications->count(),
            ],
        ]);
    }

    public function storeApplication(StoreApplicationRequest $request, JobPost $job): JsonResponse
    {
        $application = $job->applications()->create([
            ...$request->validated(),
            'stage' => Application::STAGE_APPLIED,
            'applied_at' => now(),
        ]);

        ApplicationStatusEvent::query()->create([
            'application_id' => $application->id,
            'from_stage' => null,
            'to_stage' => Application::STAGE_APPLIED,
            'changed_by' => 'api_client',
            'changed_at' => now(),
        ]);

        ScoreApplicationFitJob::dispatch($application->id);
        SendApplicationReceivedNotificationJob::dispatch($application->id);

        return response()->json([
            'data' => $application,
            'meta' => [
                'queued_assessment' => true,
            ],
        ], 201);
    }

    public function updateApplicationStage(UpdateApplicationStageRequest $request, Application $application): JsonResponse
    {
        $fromStage = $application->stage;
        $toStage = $request->string('stage')->toString();

        $application->update([
            'stage' => $toStage,
            'reviewed_at' => now(),
            'hired_at' => $toStage === Application::STAGE_HIRED ? now() : $application->hired_at,
        ]);

        ApplicationStatusEvent::query()->create([
            'application_id' => $application->id,
            'from_stage' => $fromStage,
            'to_stage' => $toStage,
            'changed_by' => 'api_client',
            'notes' => $request->input('notes'),
            'changed_at' => now(),
        ]);

        return response()->json([
            'data' => $application->fresh(),
            'meta' => [
                'updated' => true,
            ],
        ]);
    }
}
