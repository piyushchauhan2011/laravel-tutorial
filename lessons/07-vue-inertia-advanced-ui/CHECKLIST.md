# Checklist: Vue + Inertia Advanced UI

- Navigation preserves filters and sort state.
- UI handles loading/error/empty states cleanly.
- E2E tests cover at least two critical flows.
- Listing supports server-side `q/status/featured/per_page` filtering.
- Sort toggles work for `name`, `price`, and `created_at`.
- Product featured toggle works with optimistic UI and server rollback handling.
- Product detail page is reachable from listing and shows core metadata.

## Cross-Lesson Quality Gates

- App uses PostgreSQL with lesson-specific DB_DATABASE.
- Core behavior includes at least one positive-path and one negative-path automated test.
- Documentation in README.md is updated with implementation notes.
