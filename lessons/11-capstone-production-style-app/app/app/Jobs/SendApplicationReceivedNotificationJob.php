<?php

namespace App\Jobs;

use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendApplicationReceivedNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $applicationId)
    {
    }

    public function handle(): void
    {
        $application = Application::query()->find($this->applicationId);

        if (! $application) {
            return;
        }

        Log::info('Application received notification dispatched.', [
            'application_id' => $application->id,
            'email' => $application->email,
        ]);
    }
}
