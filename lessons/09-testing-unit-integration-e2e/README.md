# Testing: Unit, Integration, E2E

## Objectives

- Build a practical Laravel testing strategy.
- Use Pest and PHPUnit together effectively.
- Validate user flows via Playwright and Pest Browser Testing.

## Recommended Order

- Run in parallel while doing other lessons, then consolidate here.

## Lesson Sequence

1. Standardize Pest setup across apps.
2. Add unit tests for services/policies/value objects.
3. Add integration tests for API, DB, queue interactions.
4. Configure Playwright for auth + CRUD + admin restrictions flows.

## Run This Lesson

1. Activate lesson 09 docroot:
   - `ddev config --docroot=lessons/09-testing-unit-integration-e2e/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB:
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && php artisan migrate:fresh --seed'`
3. Install frontend test deps:
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && npm install'`
4. Run unit + integration suite:
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && php artisan test'`
5. Run Pest Browser E2E:
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && npm install --save-dev playwright'`
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && sudo npx playwright install-deps'` (one-time per DDEV web container)
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && npx playwright install chromium'`
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && ./vendor/bin/pest tests/Browser'`
6. Run Playwright E2E:
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && npm run e2e:install'`
   - `ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && APP_URL=https://laravel-tutorial.ddev.site npm run e2e'`

## Command Cheat Sheet

Use this sequence when switching from another lesson and you see `404` on `/issues`:

```bash
cd /Users/piyushchauhan/Documents/scratch/laravel-tutorial

ddev config --docroot=lessons/09-testing-unit-integration-e2e/app/public --project-type=php --auto
ddev restart
ddev mutagen reset

ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && php artisan optimize:clear'
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && php artisan route:list | grep issues'
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && php artisan migrate:fresh --seed'
```

Create sample issues quickly:

```bash
ddev exec bash -lc "cd lessons/09-testing-unit-integration-e2e/app && php artisan tinker --execute='App\\Models\\Issue::factory()->count(3)->create([\"priority\"=>\"high\",\"status\"=>\"open\",\"reported_by\"=>\"manual\"]);'"
```

Run backend tests:

```bash
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && php artisan test'
```

Run Pest browser tests:

```bash
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && npm install --save-dev playwright'
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && sudo npx playwright install-deps'
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && npx playwright install chromium'
ddev exec bash -lc 'cd lessons/09-testing-unit-integration-e2e/app && ./vendor/bin/pest tests/Browser'
```

Run E2E from host terminal:

```bash
cd lessons/09-testing-unit-integration-e2e/app
npm install
npx playwright install chromium
APP_URL=https://laravel-tutorial.ddev.site npm run e2e
```

Notes:

- `npm WARN EBADENGINE` here is a warning (not a blocker) unless install fails.
- If Playwright still fails, inspect traces:
  - `npx playwright show-trace test-results/<trace-file>.zip`

## Implemented Test Targets

- Web issue flow:
  - `GET /issues`
  - `POST /issues`
  - `PATCH /issues/{issue}/resolve`
- API issue flow:
  - `POST /api/v1/issues`
  - `GET /api/v1/issues/{issue}`
  - `PATCH /api/v1/issues/{issue}/resolve`
- Queue job:
  - `AssessIssueSeverity` computes `severity_score` and can auto-promote issue status to `in_progress`.
- Unit targets:
  - `IssuePriority` value object
  - `IssueSeverityScorer` service
- Pest browser targets:
  - `tests/Browser/IssueBrowserTest.php` covers the end-to-end issue submission flow using `visit(...)`.

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
