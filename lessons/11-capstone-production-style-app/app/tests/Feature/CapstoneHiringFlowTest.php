<?php

namespace Tests\Feature;

use App\Jobs\ScoreApplicationFitJob;
use App\Jobs\SendApplicationReceivedNotificationJob;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CapstoneHiringFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_create_job_and_application_and_queue_follow_up_jobs(): void
    {
        Queue::fake();

        $jobResponse = $this->postJson('/api/v1/jobs', [
            'title' => 'Senior Backend Engineer',
            'department' => 'Engineering',
            'location' => 'Remote',
            'employment_type' => 'full_time',
            'status' => 'open',
            'is_remote' => true,
            'salary_min' => 140000,
            'salary_max' => 190000,
            'description' => str_repeat('Build and operate core APIs. ', 4),
        ]);

        $jobResponse->assertCreated()
            ->assertJsonPath('data.title', 'Senior Backend Engineer');

        $jobId = (int) $jobResponse->json('data.id');

        $applicationResponse = $this->postJson("/api/v1/jobs/{$jobId}/applications", [
            'candidate_name' => 'Alex Candidate',
            'email' => 'alex@example.com',
            'source' => 'referral',
            'years_experience' => 7,
            'resume_text' => str_repeat('Led distributed systems and hiring process improvements. ', 3),
            'cover_letter' => 'Excited to contribute.',
        ]);

        $applicationResponse->assertCreated()
            ->assertJsonPath('meta.queued_assessment', true);

        Queue::assertPushed(ScoreApplicationFitJob::class);
        Queue::assertPushed(SendApplicationReceivedNotificationJob::class);
    }

    public function test_web_flow_can_create_job_and_update_application_stage(): void
    {
        $job = JobPost::factory()->create();
        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'stage' => Application::STAGE_APPLIED,
        ]);

        $response = $this->patch("/jobs/{$job->id}/applications/{$application->id}/stage", [
            'stage' => Application::STAGE_INTERVIEW,
        ]);

        $response->assertRedirect(route('jobs.show', $job));
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'stage' => Application::STAGE_INTERVIEW,
        ]);
    }
}
