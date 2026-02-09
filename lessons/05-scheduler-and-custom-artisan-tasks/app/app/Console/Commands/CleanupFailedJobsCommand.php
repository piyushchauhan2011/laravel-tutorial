<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupFailedJobsCommand extends Command
{
    protected $signature = 'ops:failed-jobs:cleanup
                            {--days=14 : Delete failed jobs older than this many days}
                            {--dry-run : Show count only without deleting}';

    protected $description = 'Prune old records from failed_jobs';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');
        $threshold = now()->subDays($days);

        $query = DB::table('failed_jobs')->where('failed_at', '<', $threshold);
        $count = (clone $query)->count();

        if ($dryRun) {
            $this->info("Dry run: {$count} failed jobs older than {$days} days.");
            return self::SUCCESS;
        }

        $deleted = $query->delete();
        Log::info('Failed jobs cleanup executed', [
            'threshold' => $threshold->toDateTimeString(),
            'deleted' => $deleted,
        ]);

        $this->info("Deleted {$deleted} failed jobs older than {$days} days.");

        return self::SUCCESS;
    }
}
