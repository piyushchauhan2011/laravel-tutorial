# Deployment Validation Checklist

1. Build and push image.
2. Pull and start services on VPS.
3. Run `php artisan migrate --force`.
4. Run smoke checks:
   - `/health` returns success.
   - Auth endpoint reachable.
   - Queue worker processes test job.
   - Scheduler executes expected task.
5. If failure, rollback to previous image tag.
