# Technical Handover

Reference companion docs: deployment runbook, disaster recovery, maintenance
SOP, ICT quick reference, role/permission matrix. No secrets below.

## Repository & runtime
- App: `mrdc-website/` (Laravel 13, PHP ^8.3 — verified on 8.4; MySQL 8.0;
  Redis 7; Node 24 for builds). Key paths: `routes/web.php` (+`console.php`),
  `app/Http/Controllers/Public/*`, `app/Domain/*` (Cms, Identity, Content,
  Analytics, Operations), `app/Filament/{Resources,Pagers→Pages}`,
  `resources/js/{Pages,Layouts,Components}`, `database/migrations`,
  `database/seeders`, `config/{cms,ops}.php`.
- Install: `composer install --no-dev --optimize-autoloader`,
  `npm ci && npm run build`, `php artisan storage:link`,
  `migrate --force`, `config/route/view:cache`.

## Environment variables (see `.env.example`)
APP_*, DB_*, REDIS_*, SESSION_* (secure cookie under HTTPS), CACHE_*/QUEUE_*
(redis in production), MAIL_* (SMTP/API creds external), FILESYSTEM_DISK,
CMS_*, OPS_ALERT_RECIPIENTS, ANALYTICS_RETENTION_DAYS, VITE_APP_NAME.

## Services
MySQL (least-privilege app user), Redis (private network), supervised queue
workers + `queue:restart` on deploy, per-minute `schedule:run`, SMTP/API mail,
`/up` health, external uptime polling (hosting task).

## Operations
Monitoring (dashboard, monthly report, error events, audit report + CSV),
backups (`backup:create` → timestamped DB dump + files + manifest; retention
+ off-server copy), restore rehearsal (scratch only — never over production
except approved DR), logging (stack/single, OS rotation, redacted), failed-job
triage, incident recording + alerts.

## Key commands
`production:check`, `backup:create`, `analytics:prune`, `security:bootstrap-admin`
(first admin only), `queue:work|restart|failed`, `migrate --force`
(**never `migrate:fresh`** in production), cache commands, `schedule:list`.

## Troubleshooting entry points
Blank/500 pages → logs + error events (correlation IDs); login failures →
account status/role scopes; missing uploads → disk/permissions/link; stale
content → caches + publication state; mail silent → mailer config + queue
depth + failed jobs.
