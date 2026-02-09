# Observability Baseline

Each lesson app should implement:

- A `/health` endpoint.
- A queue health command, e.g. `php artisan app:queue-health`.
- A scheduler health command, e.g. `php artisan app:scheduler-health`.
- Structured application logs for domain events and failures.
