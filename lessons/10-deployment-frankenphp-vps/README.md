# Deployment with FrankenPHP on VPS

## Objectives

- Build production image using FrankenPHP.
- Configure Nginx reverse proxy and process supervision.
- Define safe deployment, verification, and rollback workflow.

## Recommended Order

- Complete near the end after core app concerns are stable.

## Lesson Sequence

1. Create production Dockerfile based on FrankenPHP.
2. Configure Nginx reverse proxy and TLS termination strategy.
3. Define migration, queue worker, and scheduler runtime model.
4. Write deployment runbook with rollback checklist and health probes.

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
