# Vue + Inertia Advanced UI

## Objectives

- Build complex UX with Vue + Inertia.
- Implement resilient filter/sort/pagination state management.
- Improve UX for loading/error/empty states.

## Recommended Order

- Complete after lessons 01-03.

## Lesson Sequence

1. Build listing and detail pages with server-driven pagination.
2. Add composables for query params and persisted filter state.
3. Implement optimistic updates where appropriate.
4. Add E2E coverage for core UI journeys.

## Run This Lesson

1. Activate lesson 07 docroot:
   - `ddev config --docroot=lessons/07-vue-inertia-advanced-ui/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare database and seed demo data:
   - `ddev exec bash -lc 'cd lessons/07-vue-inertia-advanced-ui/app && php artisan migrate:fresh --seed'`
3. Build frontend assets:
   - `ddev exec bash -lc 'cd lessons/07-vue-inertia-advanced-ui/app && npm install && npm run build'`
4. Run tests:
   - `ddev exec bash -lc 'cd lessons/07-vue-inertia-advanced-ui/app && php artisan test'`

## URLs and Flows

- App root: `https://laravel-tutorial.ddev.site/`
- Login: `https://laravel-tutorial.ddev.site/login`
  - seed user: `test@example.com` / `password`
- Product listing: `https://laravel-tutorial.ddev.site/products`
- Product detail: `https://laravel-tutorial.ddev.site/products/{id}`

The products page demonstrates:

- Server-driven pagination with persisted query state.
- Filter controls (`q`, `status`, `featured`, `per_page`) backed by URL params.
- Sort toggles (`name`, `price`, `created_at`) with direction changes.
- Optimistic featured toggle with JSON PATCH fallback handling.

## Key Backend Contracts

- `GET /products`
  - auth required
  - Inertia page: `Products/Index`
  - query params: `q`, `status`, `featured`, `sort`, `direction`, `per_page`, `page`
- `GET /products/{product}`
  - auth required
  - Inertia page: `Products/Show`
- `PATCH /products/{product}/toggle-featured`
  - auth required
  - returns JSON when `Accept: application/json`

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
