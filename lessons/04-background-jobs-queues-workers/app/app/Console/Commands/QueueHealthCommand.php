<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueHealthCommand extends Command
{
    protected $signature = 'queue:health';

    protected $description = 'Show pending/failed queue job counts';

    public function handle(): int
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        $this->info("Pending jobs: {$pendingJobs}");
        $this->info("Failed jobs: {$failedJobs}");

        if ($failedJobs > 0) {
            $this->warn('Some jobs have failed. Inspect with php artisan queue:failed.');
        }

        return self::SUCCESS;
    }
}
