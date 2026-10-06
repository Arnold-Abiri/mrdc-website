# Production Deployment Runbook

Release candidate: Stage 8 commit (see report §B). Low-downtime procedure;
maintenance mode only if the release requires it — do not promise zero
downtime unless the host supports atomic releases.

## Pre-deployment
1. Confirm the approved release commit; verify clean `git status`.
2. Confirm `APP_URL=https://…` (final approved hostname), `APP_ENV=production`,
   `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`.
3. Take a full backup: `php artisan backup:create --path=/var/backups/mrdc`
   (database + files + manifest). Verify artifacts exist.
4. Put the scheduler in known state; note queue depth (`queue:monitor` or jobs count).

## Deploy
5. Fetch the release onto the host (Git archive/checkout — never expose `.git`
   under the document root; document root must be `public/`).
6. `composer install --no-dev --optimize-autoloader` (production dependencies only).
7. Build frontend assets (`npm ci && npm run build`) or deploy prebuilt assets
   per hosting strategy; never deploy `node_modules`.
8. Set/check environment from the sealed inventory (APP_KEY generated once and
   retained across deploys).
9. `php artisan migrate --force` (never `migrate:fresh` in production).
10. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
    (all three verified compatible), `php artisan storage:link` if required,
    storage permissions (owner-writable, never 777).
11. `php artisan queue:restart` (supervised workers pick up the release);
    confirm the `* * * * * … schedule:run` cron is active.

## Verify
12. `php artisan production:check` (must pass), `php artisan up` status,
    `/up` returns success, homepage + key public pages render, admin login
    works, a document downloads, Filament M&E dashboard loads.
13. Monitor error events and queue failures for 60 minutes.

## Rollback
- Code: redeploy the previous release tag/commit (assets rebuilt to match).
- Migrations: prefer forward-fix; `migrate:rollback` only for additive,
  data-safe steps and only with approval.
- Data loss/corruption: fresh backup first, then restore the pre-deployment
  backup into scratch, verify, then cut over (see DR plan).
- Workers: `queue:restart` after any rollback; re-run smoke verification.

## Post-deployment
Record the release (commit, time, operator, migration batch, backup path) and
attach it to the monthly report inputs.
