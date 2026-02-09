# Tasks: Testing: Unit, Integration, E2E

## Implementation Steps

1. Standardize Pest setup across apps.
2. Add unit tests for services/policies/value objects.
3. Add integration tests for API, DB, queue interactions.
4. Configure Playwright for auth + CRUD + admin restrictions flows.

## Deliverables

- Working Laravel code in lessons/09-testing-unit-integration-e2e/app
- Tests for implemented behavior (unit/integration and E2E where relevant)
- Notes on design decisions and tradeoffs in the lesson README

## Suggested Hands-On Exercises

1. Add flaky-test detection by running feature suite multiple times in CI.
2. Add an E2E test for resolving an issue from the detail page.
3. Add contract tests that assert API error envelope shape.
4. Add mutation testing to validate assertion quality on core services.
