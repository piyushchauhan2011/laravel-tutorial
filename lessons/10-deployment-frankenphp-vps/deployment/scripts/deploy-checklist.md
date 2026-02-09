# Deployment Validation Checklist

## 1) Pre-deploy

1. Confirm clean git state and tag release:
   - `git status`
   - `git tag lesson-10-release-<YYYYMMDD-HHMM>`
2. Run local checks:
   - `ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan test'`
3. Build image from lesson root:
   - `cd lessons/10-deployment-frankenphp-vps`
   - `docker compose -f deployment/docker/docker-compose.example.yml build app queue-worker scheduler`

## 2) Deploy

1. Upload `app/` and `deployment/` to VPS.
2. On VPS:
   - `docker compose -f deployment/docker/docker-compose.example.yml pull` (if using registry)
   - `docker compose -f deployment/docker/docker-compose.example.yml up -d postgres`
   - `docker compose -f deployment/docker/docker-compose.example.yml run --rm --profile ops migrate`
   - `docker compose -f deployment/docker/docker-compose.example.yml up -d app queue-worker scheduler nginx`

## 3) Smoke Validation

1. `curl -f http://<server>/health`
2. `docker compose -f deployment/docker/docker-compose.example.yml ps`
3. Queue/scheduler checks:
   - `docker compose -f deployment/docker/docker-compose.example.yml exec app php artisan ops:health`
   - `docker compose -f deployment/docker/docker-compose.example.yml logs --tail=50 queue-worker`
   - `docker compose -f deployment/docker/docker-compose.example.yml logs --tail=50 scheduler`

## 4) Rollback

1. Switch to previous image tag in compose env/config.
2. `docker compose -f deployment/docker/docker-compose.example.yml up -d app queue-worker scheduler nginx`
3. Re-run smoke checks.
