# Checklist: API Routes, Sanctum, Versioning

- Protected routes reject requests without valid token.
- Response and error formats follow shared contract.
- Integration tests cover auth, validation, and 404/403 cases.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
