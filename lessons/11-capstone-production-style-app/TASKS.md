# Tasks: Capstone Production-style App

## Domain Model (Target)

1. `job_posts` table/model (domain “jobs”)
2. `applications` table/model
3. `application_status_events` table/model (audit trail for state transitions)
4. `interviews` table/model for scheduling stage

## Phase 1: Core CRUD + Relationships

1. Build CRUD for `jobs` (create, list, edit, archive).
2. Build CRUD for `applications` scoped to a job.
3. Add relationships:
   - Job `hasMany` Applications
   - Application `belongsTo` Job
   - Application `hasMany` StatusEvents
4. Add validation rules and form requests.
5. Add factories + seeders with realistic sample data.

## Phase 2: API Surface (/api/v1)

1. Add routes:
   - `GET /api/v1/jobs`
   - `POST /api/v1/jobs`
   - `GET /api/v1/jobs/{job}`
   - `GET /api/v1/jobs/{job}/applications`
   - `POST /api/v1/jobs/{job}/applications`
2. Keep shared JSON envelope:
   - success: `{ data, meta }`
   - error: `{ error: { code, message, details } }`
3. Add request validation + error responses.

## Phase 3: Async Jobs + Queue Workers

1. Add queue job: `ScoreApplicationFitJob`
   - inputs: `application_id`
   - output: updates `fit_score` on applications
2. Add queue job: `SendApplicationReceivedNotificationJob`
3. Dispatch both after application submission.
4. Add failure/retry behavior and demo command(s) for queue health.

## Phase 4: Scheduled Tasks + Custom Commands

1. Add `capstone:daily-metrics` command to compute/report daily summaries.
2. Add `capstone:stale-applications` command to mark stale applications.
3. Schedule both in scheduler (`routes/console.php`).
4. Add `capstone:scheduler-health` command for heartbeat/status.

## Phase 5: CTE + Analytics

1. Add analytics endpoint(s):
   - `GET /api/v1/analytics/pipeline?from=&to=&group_by=`
2. Use CTEs for:
   - funnel counts by stage
   - conversion rates by source
   - time-to-hire metrics
3. Add at least one recursive CTE example (org/team hierarchy or referral chain).
4. Add tests asserting query correctness on seeded fixtures.

## Phase 6: Feature Flags with Laravel Pennant

1. Keep current Pennant control center:
   - `GET /capstone`
   - `POST /capstone/flags/{feature}`
   - `GET /api/v1/feature-flags`
   - `PATCH /api/v1/feature-flags/{feature}`
2. Gate real features with flags, e.g.:
   - `analytics_v2_dashboards` gates advanced CTE dashboard route/UI.
   - `ai_triage_assistant` gates async recommendation feature.
   - `canary_release_banner` gates canary-only UI messaging.
3. Add tests proving enabled vs disabled behavior paths.

## Phase 7: Testing and Production Readiness

1. Unit tests:
   - model scopes
   - fit scoring service
   - policy decisions
2. Integration tests:
   - jobs/applications lifecycle
   - queue dispatch + DB side effects
   - analytics endpoint contracts
3. Browser/E2E tests:
   - create job
   - submit application
   - move application stage
   - verify feature-flag-gated screen
4. Deployment rehearsal:
   - run migrations
   - run worker + scheduler checks
   - verify health endpoint and rollback checklist

## Deliverables

- Working Laravel code in lessons/11-capstone-production-style-app/app
- Tests for implemented behavior (unit/integration and E2E where relevant)
- Notes on design decisions and tradeoffs in the lesson README
- Pennant-backed feature flag definitions with web, API, and CLI visibility
- Capstone domain implemented around `jobs` and `applications`
- CTE-backed analytics endpoints with test coverage
- Queue and scheduler workflows exercised with concrete commands
