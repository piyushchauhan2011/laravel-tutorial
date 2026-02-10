# Capstone Production-style App

## Objectives

- Integrate CRUD, auth, API, queues, scheduler, analytics, and admin into one product.
- Demonstrate code quality with comprehensive tests.
- Rehearse production deployment flow end to end.
- Use Laravel Pennant for safe feature rollout in the capstone.

## Recommended Order

- Final lesson after completing 00-10.

## Lesson Sequence

1. Define capstone scope and domain model.
2. Implement core feature set across web, API, and admin surfaces.
3. Add async jobs, scheduled tasks, and CTE-backed analytics.
4. Add feature-flag rollout controls with Laravel Pennant.
5. Pass unit, integration, and E2E tests; execute deployment rehearsal.

## Run This Lesson

1. Activate lesson 11 docroot:
   - `ddev config --docroot=lessons/11-capstone-production-style-app/app/public --project-type=php --auto`
   - `ddev restart`
2. Install dependencies and migrate:
   - `ddev exec bash -lc 'cd lessons/11-capstone-production-style-app/app && composer install && npm install && php artisan migrate --force'`
3. Open capstone control center:
   - `https://laravel-tutorial.ddev.site/capstone`
4. Check flags via API:
   - `curl -s https://laravel-tutorial.ddev.site/api/v1/feature-flags | jq`
5. Check flags via Artisan:
   - `ddev exec bash -lc 'cd lessons/11-capstone-production-style-app/app && php artisan capstone:flags'`
6. Run tests:
   - `ddev exec bash -lc 'cd lessons/11-capstone-production-style-app/app && php artisan test'`

## Pennant in This Lesson

- Feature definitions live in:
  - `lessons/11-capstone-production-style-app/app/app/Support/CapstoneFeatures.php`
  - `lessons/11-capstone-production-style-app/app/app/Providers/AppServiceProvider.php`
- Web control center:
  - `GET /capstone`
  - `POST /capstone/flags/{feature}`
- API control endpoints:
  - `GET /api/v1/feature-flags`
  - `PATCH /api/v1/feature-flags/{feature}` with `{ "enabled": true|false }`
- CLI visibility:
  - `php artisan capstone:flags`

## Domain Model Notes

- Laravel already has an infra queue table named `jobs` from `0001_01_01_000002_create_jobs_table.php`.
- Capstone hiring domain uses:
  - `job_posts` (represents domain “jobs”)
  - `applications`
  - `application_status_events`
  - `interviews`
- This separation avoids collision between queue internals and business entities.

## Implemented Capstone Surfaces

- Web:
  - `GET /jobs`
  - `GET /jobs/create`
  - `GET /jobs/{job}`
  - `POST /jobs`
  - `POST /jobs/{job}/applications`
  - `PATCH /jobs/{job}/applications/{application}/stage`
- API:
  - `GET /api/v1/jobs`
  - `POST /api/v1/jobs`
  - `GET /api/v1/jobs/{job}`
  - `GET /api/v1/jobs/{job}/applications`
  - `POST /api/v1/jobs/{job}/applications`
  - `PATCH /api/v1/applications/{application}/stage`
  - `GET /api/v1/analytics/pipeline`
- Async:
  - `ScoreApplicationFitJob`
  - `SendApplicationReceivedNotificationJob`
- Scheduler/CLI:
  - `capstone:daily-metrics`
  - `capstone:stale-applications`
  - `capstone:scheduler-health`

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
- Keep API routes under /api/v1/* and use the shared response envelope contract.
