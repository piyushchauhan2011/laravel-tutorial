<?php

namespace App\Http\Controllers;

use App\Models\OperationalMetric;
use App\Models\ScheduledTaskRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SchedulerStatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'latest_metric' => OperationalMetric::query()
                    ->where('metric_name', 'daily_platform_snapshot')
                    ->latest('metric_date')
                    ->first(),
                'recent_scheduler_runs' => ScheduledTaskRun::query()
                    ->latest('ran_at')
                    ->limit(10)
                    ->get(),
                'queue' => [
                    'pending_jobs' => DB::table('jobs')->count(),
                    'failed_jobs' => DB::table('failed_jobs')->count(),
                ],
            ],
        ]);
    }
}
