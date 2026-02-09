#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
LESSONS_DIR="$ROOT_DIR/lessons"

# shellcheck source=./lib-lessons.sh
source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib-lessons.sh"

write_common_footer() {
  local file="$1"
  local slug="$2"

  {
    echo
    echo "## Standards"
    echo
    echo "- Use Pest for unit and feature tests by default."
    echo "- Keep lesson code documented with concise rationale comments where needed."
    echo "- Record evidence of completion in CHECKLIST.md before moving on."
    if lesson_is_api_focused "$slug"; then
      echo "- Keep API routes under /api/v1/* and use the shared response envelope contract."
    fi
  } >> "$file"
}

write_docs() {
  local slug="$1"
  local title="$2"
  local lesson_dir="$LESSONS_DIR/$slug"

  local objectives tasks checklist recommended_order

  case "$slug" in
    00-laravel-cli-foundations)
      objectives='- Understand Laravel folder structure and runtime lifecycle.
- Use Artisan for scaffolding, inspection, and maintenance commands.
- Configure environment variables and config caching safely.'
      tasks='1. Run artisan about, route:list, and config:show app to inspect runtime state.
2. Scaffold a sample model, migration, factory, seeder, and controller using Artisan.
3. Practice env/config caching commands and verify behavior before/after cache clear.
4. Add a simple /health route returning JSON with app name, env, and timestamp.'
      checklist='- Can explain request lifecycle and service container basics.
- Can generate common artifacts from Artisan without documentation lookup.
- Health endpoint exists and returns valid JSON.'
      recommended_order='- Start here before all other lessons.'
      ;;
    01-crud-and-data-modeling)
      objectives='- Design normalized relational schema with migrations.
- Implement Eloquent relationships, factories, and seeders.
- Build end-to-end CRUD with validation and authorization stubs.'
      tasks='1. Model projects, tasks, and labels including pivot table relationships.
2. Build CRUD pages for projects and tasks with form requests.
3. Add seeders/factories to generate realistic linked data.
4. Add feature tests for create, edit, delete, and validation failures.'
      checklist='- CRUD lifecycle works for at least two related resources.
- Validation errors are tested and user-visible.
- Database seeding produces usable demo data.'
      recommended_order='- Complete after lesson 00.'
      ;;
    02-authn-authz-policies-roles)
      objectives='- Implement session-based authentication.
- Enforce authorization with policies and gates.
- Introduce role model and role-aware access rules.'
      tasks='1. Install Laravel Breeze with Inertia + Vue.
2. Create roles schema and role assignment workflow.
3. Define and apply policies for project/task ownership and admin actions.
4. Add tests covering allowed and forbidden behavior.'
      checklist='- Register/login/logout flows pass.
- Policy decisions are enforced in controllers/routes.
- Non-authorized actions return correct HTTP status.'
      recommended_order='- Build on top of CRUD concepts from lesson 01.'
      ;;
    03-api-routes-sanctum-versioning)
      objectives='- Design versioned Laravel APIs.
- Secure APIs with Sanctum token authentication.
- Enforce unified request validation and response envelopes.'
      tasks='1. Create /api/v1 route group and versioned controllers.
2. Implement token issue/revoke endpoints with Sanctum.
3. Return resource responses in { data, meta } envelope.
4. Add global API exception handling with { error: { code, message, details } } format.'
      checklist='- Protected routes reject requests without valid token.
- Response and error formats follow shared contract.
- Integration tests cover auth, validation, and 404/403 cases.'
      recommended_order='- Complete after auth lesson 02.'
      ;;
    04-background-jobs-queues-workers)
      objectives='- Move slow operations into queued jobs.
- Configure retries/backoff and failed job recovery.
- Operate and observe workers in development.'
      tasks='1. Implement at least one queued job (report/email/export).
2. Configure queue connection and run worker with sensible flags.
3. Handle failed jobs and implement retry strategy.
4. Add queue health command and tests around dispatch behavior.'
      checklist='- Long-running work is processed asynchronously.
- Failed jobs can be inspected and retried.
- Queue behavior has feature or integration tests.'
      recommended_order='- Complete after lessons 01-03.'
      ;;
    05-scheduler-and-custom-artisan-tasks)
      objectives='- Build reusable custom Artisan commands.
- Schedule recurring jobs with safety controls.
- Add operational tasks for maintenance/reporting.'
      tasks='1. Create commands with arguments/options and proper output.
2. Register scheduled tasks with frequency and overlap protection.
3. Add logging/alerts for scheduled task outcomes.
4. Verify scheduler operation locally and document production invocation.'
      checklist='- Commands are documented and tested.
- Scheduler runs expected tasks at expected cadence.
- At least one idempotent operational task exists.'
      recommended_order='- Complete after queue fundamentals in lesson 04.'
      ;;
    06-postgres-ctes-analytics)
      objectives='- Build analytics queries using PostgreSQL CTEs.
- Use window functions for ranking/trend analysis.
- Expose validated reporting endpoints for dashboards.'
      tasks='1. Implement non-recursive CTE analytics query for aggregates.
2. Add one recursive CTE example (hierarchy or dependency graph).
3. Build API endpoint with params: from, to, group_by.
4. Add indexes and compare query plans with EXPLAIN ANALYZE.'
      checklist='- CTE endpoints return correct grouped metrics.
- Parameter validation rejects invalid ranges/grouping.
- Query plan improvements are documented.'
      recommended_order='- Complete after API and queue lessons for realistic datasets.'
      ;;
    07-vue-inertia-advanced-ui)
      objectives='- Build complex UX with Vue + Inertia.
- Implement resilient filter/sort/pagination state management.
- Improve UX for loading/error/empty states.'
      tasks='1. Build listing and detail pages with server-driven pagination.
2. Add composables for query params and persisted filter state.
3. Implement optimistic updates where appropriate.
4. Add E2E coverage for core UI journeys.'
      checklist='- Navigation preserves filters and sort state.
- UI handles loading/error/empty states cleanly.
- E2E tests cover at least two critical flows.'
      recommended_order='- Complete after lessons 01-03.'
      ;;
    08-filament-admin-panel)
      objectives='- Build admin interfaces quickly with Filament.
- Integrate Filament actions with authorization policies.
- Add admin dashboards/widgets for operations.'
      tasks='1. Install Filament and create admin panel.
2. Scaffold resources/forms/tables for key models.
3. Restrict visibility/actions using policies and roles.
4. Add widgets for queue backlog, scheduled tasks, or business KPIs.'
      checklist='- Filament resources function with role restrictions.
- Admin workflows are tested (integration or E2E).
- Dashboard exposes operationally useful metrics.'
      recommended_order='- Complete after auth lesson 02 and analytics lesson 06.'
      ;;
    09-testing-unit-integration-e2e)
      objectives='- Build a practical Laravel testing strategy.
- Use Pest and PHPUnit together effectively.
- Validate user flows via Playwright.'
      tasks='1. Standardize Pest setup across apps.
2. Add unit tests for services/policies/value objects.
3. Add integration tests for API, DB, queue interactions.
4. Configure Playwright for auth + CRUD + admin restrictions flows.'
      checklist='- Unit/integration suites pass reliably.
- E2E suite covers critical user journeys.
- Test commands are documented for local and CI usage.'
      recommended_order='- Run in parallel while doing other lessons, then consolidate here.'
      ;;
    10-deployment-frankenphp-vps)
      objectives='- Build production image using FrankenPHP.
- Configure Nginx reverse proxy and process supervision.
- Define safe deployment, verification, and rollback workflow.'
      tasks='1. Create production Dockerfile based on FrankenPHP.
2. Configure Nginx reverse proxy and TLS termination strategy.
3. Define migration, queue worker, and scheduler runtime model.
4. Write deployment runbook with rollback checklist and health probes.'
      checklist='- Containerized app serves traffic correctly.
- Health checks pass after deployment.
- Rollback steps are documented and testable.'
      recommended_order='- Complete near the end after core app concerns are stable.'
      ;;
    11-capstone-production-style-app)
      objectives='- Integrate CRUD, auth, API, queues, scheduler, analytics, and admin into one product.
- Demonstrate code quality with comprehensive tests.
- Rehearse production deployment flow end to end.'
      tasks='1. Define capstone scope and domain model.
2. Implement core feature set across web, API, and admin surfaces.
3. Add async jobs, scheduled tasks, and CTE-backed analytics.
4. Pass unit, integration, and E2E tests; execute deployment rehearsal.'
      checklist='- End-to-end feature set works with correct authz boundaries.
- Tests pass across unit, integration, and E2E.
- Deployment runbook executed successfully in staging/local rehearsal.'
      recommended_order='- Final lesson after completing 00-10.'
      ;;
    *)
      echo "Unknown lesson slug: $slug" >&2
      exit 1
      ;;
  esac

  mkdir -p "$lesson_dir"

  cat > "$lesson_dir/README.md" <<TXT
# $title

## Objectives

$objectives

## Recommended Order

$recommended_order

## Lesson Sequence

$tasks
TXT

  write_common_footer "$lesson_dir/README.md" "$slug"

  cat > "$lesson_dir/TASKS.md" <<TXT
# Tasks: $title

## Implementation Steps

$tasks

## Deliverables

- Working Laravel code in lessons/$slug/app
- Tests for implemented behavior (unit/integration and E2E where relevant)
- Notes on design decisions and tradeoffs in the lesson README
TXT

  cat > "$lesson_dir/CHECKLIST.md" <<TXT
# Checklist: $title

$checklist

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
TXT
}

for lesson in "${LESSON_SPECS[@]}"; do
  slug="${lesson%%|*}"
  title="${lesson#*|}"
  write_docs "$slug" "$title"
done

echo "Generated lesson docs under: $LESSONS_DIR"
