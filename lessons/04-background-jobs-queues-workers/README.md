# Background Jobs, Queues, Workers

## Objectives

- Move slow operations into queued jobs.
- Configure retries/backoff and failed job recovery.
- Operate and observe workers in development.

## Recommended Order

- Complete after lessons 01-03.

## Lesson Sequence

1. Implement at least one queued job (report/email/export).
2. Configure queue connection and run worker with sensible flags.
3. Handle failed jobs and implement retry strategy.
4. Add queue health command and tests around dispatch behavior.

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
