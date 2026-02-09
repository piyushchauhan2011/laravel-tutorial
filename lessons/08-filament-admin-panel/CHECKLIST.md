# Checklist: Filament Admin Panel

- Filament resources function with role restrictions.
- Admin workflows are tested (integration or E2E).
- Dashboard exposes operationally useful metrics.
- `/admin` is available and protected by authentication.
- `member` role is denied panel access.
- Post resource supports create/edit/view with status + featured filters.
- Policy coverage verifies admin delete and editor ownership update rules.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
