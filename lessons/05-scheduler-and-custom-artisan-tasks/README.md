# Scheduler and Custom Artisan Tasks

## Objectives

- Build reusable custom Artisan commands.
- Schedule recurring jobs with safety controls.
- Add operational tasks for maintenance/reporting.

## Recommended Order

- Complete after queue fundamentals in lesson 04.

## Lesson Sequence

1. Create commands with arguments/options and proper output.
2. Register scheduled tasks with frequency and overlap protection.
3. Add logging/alerts for scheduled task outcomes.
4. Verify scheduler operation locally and document production invocation.

## Run This Lesson

1. Activate lesson 05:
   - `ddev config --docroot=lessons/05-scheduler-and-custom-artisan-tasks/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB:
   - `ddev exec bash -lc 'cd lessons/05-scheduler-and-custom-artisan-tasks/app && php artisan migrate:fresh --seed'`
3. Inspect schedules:
   - `ddev exec bash -lc 'cd lessons/05-scheduler-and-custom-artisan-tasks/app && php artisan schedule:list'`
4. Simulate one scheduler tick:
   - `ddev exec bash -lc 'cd lessons/05-scheduler-and-custom-artisan-tasks/app && php artisan schedule:run'`

## Commands Implemented

- `ops:metrics:daily --date=YYYY-MM-DD --dry-run`
  - idempotent daily metrics snapshot (`operational_metrics` table)
- `ops:failed-jobs:cleanup --days=14 --dry-run`
  - prune old `failed_jobs` rows
- `ops:scheduler:heartbeat --source=schedule|manual`
  - records scheduler execution heartbeat (`scheduled_task_runs` table)

## Scheduled Tasks

- `ops:scheduler:heartbeat --source=schedule`
  - every minute, `withoutOverlapping()`
- `ops:metrics:daily`
  - daily at 01:00, `withoutOverlapping()->onOneServer()`
- `ops:failed-jobs:cleanup --days=14`
  - daily at 01:30, `withoutOverlapping()->onOneServer()`

## Endpoints

- `GET /health`
- `GET /scheduler/status`

## Production Scheduler Invocation

- Use cron to run Laravel scheduler each minute:
  - `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
