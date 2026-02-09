# Testing Conventions

## Stack

- Unit and integration: Pest + PHPUnit
- End-to-end: Playwright

## Baseline Commands

- `php artisan test`
- `./vendor/bin/pest`
- `npx playwright test`

## Minimum Coverage by Lesson

- At least 1 unit test for a service/policy/command.
- At least 2 integration tests for happy path + negative path.
- E2E on user-critical flow for UI/API integration lessons.

## Data Strategy

- Use factories and seeders; avoid hard-coded IDs.
- Reset database per test class/suite using Laravel testing traits.
