# Lesson 11 App: Capstone + Pennant

This app is the capstone workspace for combining concepts from lessons 00-10, with Laravel Pennant added for feature-flag rollout control.

Domain naming note:
- Queue infra uses `jobs` table (Laravel default).
- Hiring domain “jobs” are stored in `job_posts`.

## What is implemented now

- Capstone feature-flag dashboard: `GET /capstone`
- Capstone feature-flag toggle endpoint: `POST /capstone/flags/{feature}`
- API flag endpoints:
  - `GET /api/v1/feature-flags`
  - `PATCH /api/v1/feature-flags/{feature}`
- CLI status command:
  - `php artisan capstone:flags`

## Pennant files

- Feature catalog: `app/Support/CapstoneFeatures.php`
- Feature definitions: `app/Providers/AppServiceProvider.php`
- Controller: `app/Http/Controllers/CapstoneFeatureFlagController.php`
- UI: `resources/views/capstone/dashboard.blade.php`

## Local run commands

```bash
php artisan migrate --force
php artisan serve
php artisan capstone:flags
php artisan test
```
