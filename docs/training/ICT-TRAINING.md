# ICT Training Track (2 staff)

Covers the full application and operations. References (not duplicates):
`docs/DEPLOYMENT-RUNBOOK.md`, `docs/DISASTER-RECOVERY.md`,
`docs/MAINTENANCE-SOP.md`, `docs/TECHNICAL-HANDOVER.md`,
`docs/ICT-QUICK-REFERENCE.md`.

## 1. Architecture
Laravel 13 monolith + Inertia + React/TypeScript + Filament admin
(`/admin`), MySQL 8, Redis (cache/session/queue), database queues,
managed local storage. Public routes from `routes/web.php` via
`app/Http/Controllers/Public/*`; domain logic in `app/Domain/*`;
RBAC via Spatie roles + `user_role_scopes` (global/department).

## 2. Environment & configuration
`.env` inventory (never commit secrets); APP_KEY discipline (generated once,
rotation invalidates sessions/encrypted data); `SESSION_SECURE_COOKIE=true`
under HTTPS; trusted proxies behind a load balancer; `production:check`.

## 3. Data & storage
MySQL users least-privilege; migrations (`migrate --force`, never `fresh`);
`storage/` layout and `storage:link`; permissions (owner-writable, never 777).

## 4. Runtime services
Redis; supervised queue workers (`queue:work`, `queue:restart`,
`queue:failed`); scheduler cron (`schedule:run` every minute;
`analytics:prune` weekly); SMTP/API mail config; `/up` health semantics.

## 5. Operate
`SystemHealth`, `ErrorEvents`, `Incidents`, M&E dashboard, monthly report,
audit report + CSV export; `backup:create`; restore rehearsal procedure;
deployment + rollback; `route/config/view:cache` compatibility (no closures
in routes); log locations and rotation; failed-job triage.

## 6. Administer
User/role administration (`security:bootstrap-admin` for the first admin
only); assigning scoped roles; disabling accounts (immediate panel lockout);
translation administration (English source, sn/nd fallback, completeness
dashboard); permission matrix (`docs/ROLE-PERMISSION-MATRIX.md`).

## 7. Secure
Security release review findings; upload controls; throttle behavior; audit
accountability; incident + credential-compromise response (see DR plan).
