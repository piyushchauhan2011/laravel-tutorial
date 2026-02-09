# Checklist: Background Jobs, Queues, Workers

- Long-running work is processed asynchronously.
- Failed jobs can be inspected and retried.
- Queue behavior has feature or integration tests.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
