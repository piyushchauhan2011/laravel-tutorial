# Checklist: Scheduler and Custom Artisan Tasks

- Commands are documented and tested.
- Scheduler runs expected tasks at expected cadence.
- At least one idempotent operational task exists.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
