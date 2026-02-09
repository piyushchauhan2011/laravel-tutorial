# Filament Admin Panel

## Objectives

- Build admin interfaces quickly with Filament.
- Integrate Filament actions with authorization policies.
- Add admin dashboards/widgets for operations.

## Recommended Order

- Complete after auth lesson 02 and analytics lesson 06.

## Lesson Sequence

1. Install Filament and create admin panel.
2. Scaffold resources/forms/tables for key models.
3. Restrict visibility/actions using policies and roles.
4. Add widgets for queue backlog, scheduled tasks, or business KPIs.

## Run This Lesson

1. Activate lesson 08 docroot:
   - `ddev config --docroot=lessons/08-filament-admin-panel/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB and seed users/posts:
   - `ddev exec bash -lc 'cd lessons/08-filament-admin-panel/app && php artisan migrate:fresh --seed'`
3. Run tests:
   - `ddev exec bash -lc 'cd lessons/08-filament-admin-panel/app && php artisan test'`

## Admin URLs and Credentials

- Admin panel: `https://laravel-tutorial.ddev.site/admin`
- Health endpoint: `https://laravel-tutorial.ddev.site/health`

Seed users:

- Admin: `admin@example.com` / `password`
- Editor: `editor@example.com` / `password`
- Member (blocked from panel): `member@example.com` / `password`

## What This Lesson Implements

- Filament v5 panel at `/admin`.
- `Post` resource with:
  - form schema (title/slug/content/status/publish date/featured flag)
  - searchable table columns + status/featured filters
  - view/edit/create pages
- Role + policy authorization:
  - `admin`: full access
  - `editor`: create/view + update own posts only
  - `member`: cannot access panel
- Dashboard KPI widget:
  - total posts
  - published posts
  - draft/review posts

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
