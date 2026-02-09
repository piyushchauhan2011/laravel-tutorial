<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function revenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'group_by' => ['required', 'in:day,week,month'],
        ]);

        $rows = DB::select(
            <<<'SQL'
            WITH filtered_sales AS (
                SELECT date_trunc(?, sold_at)::date AS bucket_start, amount
                FROM sales
                WHERE sold_at::date BETWEEN ?::date AND ?::date
            ),
            grouped AS (
                SELECT bucket_start, SUM(amount) AS revenue, COUNT(*) AS sales_count
                FROM filtered_sales
                GROUP BY bucket_start
            )
            SELECT
                bucket_start,
                revenue,
                sales_count,
                SUM(revenue) OVER (ORDER BY bucket_start) AS running_revenue,
                RANK() OVER (ORDER BY revenue DESC) AS revenue_rank
            FROM grouped
            ORDER BY bucket_start
            SQL,
            [$validated['group_by'], $validated['from'], $validated['to']]
        );

        return response()->json([
            'data' => $rows,
            'meta' => [
                'filters' => $validated,
            ],
        ]);
    }

    public function categoryRollup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $rows = DB::select(
            <<<'SQL'
            WITH RECURSIVE category_tree AS (
                SELECT id, name, parent_id, id AS root_id, 0 AS depth
                FROM categories
                WHERE parent_id IS NULL

                UNION ALL

                SELECT c.id, c.name, c.parent_id, ct.root_id, ct.depth + 1
                FROM categories c
                JOIN category_tree ct ON c.parent_id = ct.id
            ),
            sales_window AS (
                SELECT category_id, SUM(amount) AS total_revenue
                FROM sales
                WHERE sold_at::date BETWEEN ?::date AND ?::date
                GROUP BY category_id
            ),
            rollup AS (
                SELECT
                    ct.root_id,
                    ct.id AS category_id,
                    ct.depth,
                    COALESCE(sw.total_revenue, 0) AS direct_revenue
                FROM category_tree ct
                LEFT JOIN sales_window sw ON sw.category_id = ct.id
            ),
            root_rollup AS (
                SELECT
                    root_id,
                    SUM(direct_revenue) AS tree_revenue,
                    COUNT(*) AS category_count,
                    MAX(depth) AS max_depth
                FROM rollup
                GROUP BY root_id
            )
            SELECT
                c.id AS root_category_id,
                c.name AS root_name,
                rr.tree_revenue,
                rr.category_count,
                rr.max_depth,
                DENSE_RANK() OVER (ORDER BY rr.tree_revenue DESC) AS revenue_rank
            FROM root_rollup rr
            JOIN categories c ON c.id = rr.root_id
            ORDER BY rr.tree_revenue DESC, c.id ASC
            SQL,
            [$validated['from'], $validated['to']]
        );

        return response()->json([
            'data' => $rows,
            'meta' => [
                'filters' => $validated,
            ],
        ]);
    }
}
