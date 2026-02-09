# Checklist: Testing: Unit, Integration, E2E

- Unit/integration suites pass reliably.
- E2E suite covers critical user journeys.
- Test commands are documented for local and CI usage.
- Unit tests cover value object and service scoring logic.
- Integration tests cover API validation + queue dispatch + DB writes.
- Playwright config and at least one runnable issue-creation spec exist.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
