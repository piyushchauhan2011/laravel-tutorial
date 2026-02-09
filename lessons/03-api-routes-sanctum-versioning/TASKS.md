# Tasks: API Routes, Sanctum, Versioning

## Implementation Steps

1. Create /api/v1 route group and versioned controllers.
2. Implement token issue/revoke endpoints with Sanctum.
3. Return resource responses in { data, meta } envelope.
4. Add global API exception handling with { error: { code, message, details } } format.

## Deliverables

- Working Laravel code in lessons/03-api-routes-sanctum-versioning/app
- Tests for implemented behavior (unit/integration and E2E where relevant)
- Notes on design decisions and tradeoffs in the lesson README
