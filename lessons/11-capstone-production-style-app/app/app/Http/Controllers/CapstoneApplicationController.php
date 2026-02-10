<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationStageRequest;
use App\Jobs\ScoreApplicationFitJob;
use App\Jobs\SendApplicationReceivedNotificationJob;
use App\Models\Application;
use App\Models\ApplicationStatusEvent;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;

class CapstoneApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request, JobPost $job): RedirectResponse
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
            'changed_by' => 'candidate_portal',
            'changed_at' => now(),
        ]);

        ScoreApplicationFitJob::dispatch($application->id);
        SendApplicationReceivedNotificationJob::dispatch($application->id);

        return redirect()
            ->route('jobs.show', $job)
            ->with('status', 'Application submitted and queued for follow-up.');
    }

    public function updateStage(UpdateApplicationStageRequest $request, JobPost $job, Application $application): RedirectResponse
    {
        abort_unless($application->job_post_id === $job->id, 404);

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
            'changed_by' => 'hiring_manager',
            'notes' => $request->input('notes'),
            'changed_at' => now(),
        ]);

        return redirect()
            ->route('jobs.show', $job)
            ->with('status', 'Application stage updated.');
    }
}
