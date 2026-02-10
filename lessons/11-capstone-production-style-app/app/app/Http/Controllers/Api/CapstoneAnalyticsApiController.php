<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\CapstoneFeatures;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;

class CapstoneAnalyticsApiController extends Controller
{
    public function pipeline(Request $request): JsonResponse
    {
        if (! Feature::active(CapstoneFeatures::ANALYTICS_V2_DASHBOARDS)) {
            return response()->json([
                'error' => [
                    'code' => 'feature_disabled',
                    'message' => 'Analytics v2 dashboard is disabled.',
                    'details' => null,
                ],
            ], 403);
        }

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'group_by' => ['nullable', 'in:day,month'],
        ]);

        $from = $validated['from'] ?? now()->subDays(30)->toDateString();
        $to = $validated['to'] ?? now()->toDateString();
        $groupBy = $validated['group_by'] ?? 'day';

        $driver = DB::getDriverName();
        $bucketExpression = $groupBy === 'month'
            ? ($driver === 'pgsql' ? "to_char(applied_at, 'YYYY-MM')" : "strftime('%Y-%m', applied_at)")
            : ($driver === 'pgsql' ? "to_char(applied_at, 'YYYY-MM-DD')" : "strftime('%Y-%m-%d', applied_at)");

        $pipeline = DB::select(
            "WITH filtered AS (
                SELECT * FROM applications
                WHERE date(applied_at) BETWEEN ? AND ?
            )
            SELECT {$bucketExpression} AS bucket, stage, COUNT(*) AS total
            FROM filtered
            GROUP BY bucket, stage
            ORDER BY bucket ASC, stage ASC",
            [$from, $to]
        );

        $conversion = DB::select(
            "WITH filtered AS (
                SELECT * FROM applications
                WHERE date(applied_at) BETWEEN ? AND ?
            )
            SELECT source,
                COUNT(*) AS total,
                SUM(CASE WHEN stage = 'hired' THEN 1 ELSE 0 END) AS hired
            FROM filtered
            GROUP BY source
            ORDER BY source ASC",
            [$from, $to]
        );

        $referralDepth = DB::selectOne(
            "WITH RECURSIVE referral_chain AS (
                SELECT id, referred_by_application_id, 0 AS depth
                FROM applications
                WHERE referred_by_application_id IS NULL
                UNION ALL
                SELECT a.id, a.referred_by_application_id, rc.depth + 1
                FROM applications a
                JOIN referral_chain rc ON a.referred_by_application_id = rc.id
            )
            SELECT MAX(depth) AS max_depth, COUNT(*) AS nodes
            FROM referral_chain"
        );

        return response()->json([
            'data' => [
                'pipeline' => $pipeline,
                'conversion_by_source' => $conversion,
                'referral_tree' => $referralDepth,
            ],
            'meta' => [
                'from' => $from,
                'to' => $to,
                'group_by' => $groupBy,
            ],
        ]);
    }
}
