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

## Run This Lesson

1. Activate lesson 10 docroot:
   - `ddev config --docroot=lessons/10-deployment-frankenphp-vps/app/public --project-type=php --auto`
   - `ddev restart`
2. Prepare DB:
   - `ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan migrate:fresh --seed'`
3. Run tests:
   - `ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan test'`
4. Validate app runtime endpoints:
   - `https://laravel-tutorial.ddev.site/`
   - `https://laravel-tutorial.ddev.site/health`
5. Validate ops command:
   - `ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan ops:health'`

## Deployment Artifacts

- Multi-stage Docker image with FrankenPHP runtime:
  - `deployment/docker/Dockerfile`
- Caddy config used by FrankenPHP:
  - `deployment/docker/Caddyfile`
- Container boot sequence (cache/migrate options):
  - `deployment/docker/entrypoint.sh`
- Nginx reverse proxy:
  - `deployment/nginx/default.conf`
- Compose topology for `app`, `nginx`, `postgres`, `queue-worker`, `scheduler`, and one-off `migrate` profile:
  - `deployment/docker/docker-compose.example.yml`
- VPS release checklist:
  - `deployment/scripts/deploy-checklist.md`

## Process Model

- Web traffic:
  - `nginx` -> `app` (FrankenPHP)
- Background processing:
  - `queue-worker` container runs `php artisan queue:work`
  - `scheduler` container runs `php artisan schedule:work`
- Health:
  - `/health` endpoint validates app + DB connectivity
  - `php artisan ops:health` reports pending jobs, failed jobs, and scheduler heartbeat timestamp

## Validation Commands Used

From repo root:

```bash
ddev config --docroot=lessons/10-deployment-frankenphp-vps/app/public --project-type=php --auto
ddev restart
ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan migrate:fresh --seed'
ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan test'
ddev exec bash -lc 'cd lessons/10-deployment-frankenphp-vps/app && php artisan ops:health'
```

From lesson 10 root:

```bash
cd lessons/10-deployment-frankenphp-vps
docker compose -f deployment/docker/docker-compose.example.yml config
docker compose -f deployment/docker/docker-compose.example.yml build
docker compose -f deployment/docker/docker-compose.example.yml up -d
docker compose -f deployment/docker/docker-compose.example.yml --profile ops run --rm migrate
docker compose -f deployment/docker/docker-compose.example.yml up -d queue-worker
docker compose -f deployment/docker/docker-compose.example.yml ps
docker compose -f deployment/docker/docker-compose.example.yml exec app php artisan ops:health
docker compose -f deployment/docker/docker-compose.example.yml logs --tail=20 queue-worker scheduler app
docker compose -f deployment/docker/docker-compose.example.yml exec nginx wget -qO- http://app:8000/health
```

Local access:

- Nginx published port for this lesson is `8081`, so use:
  - `http://localhost:8081/health`

## Standards

- Use Pest for unit and feature tests by default.
- Keep lesson code documented with concise rationale comments where needed.
- Record evidence of completion in CHECKLIST.md before moving on.
