<?php

namespace App\Console\Commands;

use App\Models\ScheduledTaskRun;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SchedulerHeartbeatCommand extends Command
{
    protected $signature = 'ops:scheduler:heartbeat
                            {--source=schedule : Trigger source (schedule/manual)}';

    protected $description = 'Record scheduler heartbeat for observability';

    public function handle(): int
    {
        $now = now();
        $run = ScheduledTaskRun::query()->updateOrCreate(
            [
                'task_name' => 'scheduler_heartbeat',
                'run_key' => $now->format('YmdHi'),
            ],
            [
                'status' => 'ok',
                'details' => ['source' => $this->option('source')],
                'ran_at' => $now,
            ]
        );

        Log::info('Scheduler heartbeat recorded', ['run_id' => $run->id]);
        $this->info("Heartbeat recorded: {$run->run_key}");

        return self::SUCCESS;
    }
}
