# Postgres CTEs and Analytics

## Objectives

- Build analytics queries using PostgreSQL CTEs.
- Use window functions for ranking/trend analysis.
- Expose validated reporting endpoints for dashboards.

## Recommended Order

- Complete after API and queue lessons for realistic datasets.

## Lesson Sequence

1. Implement non-recursive CTE analytics query for aggregates.
2. Add one recursive CTE example (hierarchy or dependency graph).
3. Build API endpoint with params: from, to, group_by.
4. Add indexes and compare query plans with EXPLAIN ANALYZE.

## Run This Lesson

1. Activate lesson 06:
   - `ddev config --docroot=lessons/06-postgres-ctes-analytics/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB:
   - `ddev exec bash -lc 'cd lessons/06-postgres-ctes-analytics/app && php artisan migrate:fresh --seed'`
3. Run tests (PostgreSQL-backed):
   - `ddev exec bash -lc 'cd lessons/06-postgres-ctes-analytics/app && php artisan test'`

## Endpoints

- `GET /analytics/revenue?from=YYYY-MM-DD&to=YYYY-MM-DD&group_by=day|week|month`
  - non-recursive CTE aggregate
  - includes window functions: `running_revenue`, `revenue_rank`
- `GET /analytics/category-rollup?from=YYYY-MM-DD&to=YYYY-MM-DD`
  - recursive CTE hierarchy rollup with ranked root categories
- `GET /health`

## EXPLAIN ANALYZE Workflow

Use this to inspect query plan against seeded data:

```bash
ddev exec psql -U db -d lesson_06_postgres_ctes_analytics -c "
EXPLAIN ANALYZE
WITH filtered_sales AS (
  SELECT date_trunc('day', sold_at)::date AS bucket_start, amount
  FROM sales
  WHERE sold_at::date BETWEEN '2026-02-01'::date AND '2026-02-28'::date
),
grouped AS (
  SELECT bucket_start, SUM(amount) AS revenue
  FROM filtered_sales
  GROUP BY bucket_start
)
SELECT bucket_start, revenue
FROM grouped
ORDER BY bucket_start;
"
```

The lesson adds indexes on `sales(sold_at)` and `sales(category_id, sold_at)` plus `categories(parent_id)` to support these queries.

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
