# API Routes, Sanctum, Versioning

## Objectives

- Design versioned Laravel APIs.
- Secure APIs with Sanctum token authentication.
- Enforce unified request validation and response envelopes.

## Recommended Order

- Complete after auth lesson 02.

## Lesson Sequence

1. Create /api/v1 route group and versioned controllers.
2. Implement token issue/revoke endpoints with Sanctum.
3. Return resource responses in { data, meta } envelope.
4. Add global API exception handling with { error: { code, message, details } } format.

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
- Keep API routes under /api/v1/* and use the shared response envelope contract.
