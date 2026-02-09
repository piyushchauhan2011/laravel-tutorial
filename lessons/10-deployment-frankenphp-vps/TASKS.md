# Tasks: Deployment with FrankenPHP on VPS

## Implementation Steps

1. Create production Dockerfile based on FrankenPHP.
2. Configure Nginx reverse proxy and TLS termination strategy.
3. Define migration, queue worker, and scheduler runtime model.
4. Write deployment runbook with rollback checklist and health probes.

## Deliverables

- Working Laravel code in lessons/10-deployment-frankenphp-vps/app
- Tests for implemented behavior (unit/integration and E2E where relevant)
- Notes on design decisions and tradeoffs in the lesson README

## Suggested Hands-On Exercises

1. Add GitHub Actions workflow that builds and tags the FrankenPHP image.
2. Add zero-downtime blue/green rollout notes for two app containers.
3. Add HTTPS termination with Let's Encrypt (or Cloudflare Tunnel).
4. Add alerting rules for failed jobs and stale scheduler heartbeat.
