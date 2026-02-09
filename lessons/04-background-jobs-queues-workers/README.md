# Background Jobs, Queues, Workers

## Objectives

- Move slow operations into queued jobs.
- Configure retries/backoff and failed job recovery.
- Operate and observe workers in development.

## Recommended Order

- Complete after lessons 01-03.

## Lesson Sequence

1. Implement at least one queued job (report/email/export).
2. Configure queue connection and run worker with sensible flags.
3. Handle failed jobs and implement retry strategy.
4. Add queue health command and tests around dispatch behavior.

## Run This Lesson

1. Activate lesson 04 docroot:
   - `ddev config --docroot=lessons/04-background-jobs-queues-workers/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB:
   - `ddev exec bash -lc 'cd lessons/04-background-jobs-queues-workers/app && php artisan migrate:fresh --seed'`
3. Start a worker in another terminal:
   - `ddev exec bash -lc 'cd lessons/04-background-jobs-queues-workers/app && php artisan queue:work --tries=3 --backoff=5 --sleep=1'`

## Queue Demo Endpoints

- `POST /queue-demo/reports`
  - body: `{ "topic": "weekly-product-metrics", "should_fail": false }`
  - enqueues `GenerateExportReportJob`
- `GET /queue-demo/reports/{exportReport}`
  - check queued/processing/completed/failed state
- `POST /queue-demo/reports/{exportReport}/retry`
  - requeue only failed reports
- `GET /health`
  - simple app health check

## Queue Operations

- List failed jobs:
  - `php artisan queue:failed`
- Retry failed job:
  - `php artisan queue:retry <job-uuid>`
- Flush failed jobs:
  - `php artisan queue:flush`
- Quick queue health summary:
  - `php artisan queue:health`

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
