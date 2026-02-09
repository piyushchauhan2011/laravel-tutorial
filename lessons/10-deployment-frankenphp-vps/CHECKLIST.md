# Checklist: Deployment with FrankenPHP on VPS

- Containerized app serves traffic correctly.
- Health checks pass after deployment.
- Rollback steps are documented and testable.
- `/health` returns `status=ok` with DB connectivity.
- `ops:health` command reports queue and scheduler runtime state.
- Compose file includes dedicated queue worker and scheduler services.
- Docker image is reproducible using multi-stage build.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
