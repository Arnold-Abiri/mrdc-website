# Stage 8 Implementation — Production Readiness

Requirements: `docs/STAGE-8-REQUIREMENTS.md`. No business modules added, no
redesign, no stage reopened.

## Configuration & secrets

- `.env.example` documents ops/analytics/CMS variables plus production guidance
  (`APP_ENV`, `APP_DEBUG=false`, HTTPS URL, secure cookies, mail, queue,
  `OPS_ALERT_RECIPIENTS`, APP_KEY discipline). No secrets committed; `.env`
  git-ignored; secret/key scan clean.
- `production:check` (read-only): env, debug, URL, key, cookies, DB, Redis,
  storage writability, queue/mail config presence, alert recipients, retention.

## Architecture for production caching

- All 37 public route closures + 2 redirect arrows converted to 22 controllers
  under `app/Http/Controllers/Public/` (behavior-preserving move; route names,
  constraints, middleware, throttles unchanged). `route:cache`, `config:cache`,
  and `view:cache` all verified working; caches cleared for development.
- Scheduler: `analytics:prune` weekly, `queue:prune-failed` monthly,
  `auth:clear-resets` quarterly-hourly; single-cron pattern documented.

## Security posture

- Headers retained (nosniff, referrer-policy, SAMEORIGIN/frame-ancestors,
  permissions-policy); framing-only CSP — no `unsafe-eval`, no external
  origins; HSTS documented as HTTPS-only (not forced locally).
- Session/cookies env-aware; trusted-proxy setup documented for reverse proxies.
- Release review: `docs/SECURITY-RELEASE-REVIEW.md` — 0 Critical, 0 High,
  0 Medium; no bypass, no exposed secrets.
- Dependencies: `composer audit` clean; `npm audit --omit=dev` 0 vulnerabilities.

## Backups & recovery

- `backup:create`: mysqldump (single-transaction) + storage tarball + manifest
  into 0700 timestamped dirs; verified locally (45 tables, 62 files).
- Restore rehearsal executed against a disposable database (45 tables,
  `migrate:status` 24 Ran, boot + counts verified) and temp file extraction;
  disposables destroyed. `docs/DISASTER-RECOVERY.md` covers 8 scenarios with
  proposed RPO 24h / RTO 4h (council/hosting approval required).

## Release mechanics

- `docs/DEPLOYMENT-RUNBOOK.md` (15-step deploy, rollback, post-deploy),
  `docs/PRODUCTION-CHECKLIST.md` (checkboxes across 10 areas), storage/link/
  permission rules (never 777), web-server hardening (document root, protected
  paths), DNS/TLS/APP_URL as external gates.

## Validation performed

- Release security tests (`Stage8SecurityTest`, 7): guest/disabled/limited
  denial, scoped IDOR, stored XSS escaping, CSRF middleware, mass-assignment
  rejection, throttle 429, admin surfaces absent from search/sitemap.
- Accessibility structural suite (`stage8-public.spec.ts`, 15): h1/landmark per
  page, meaningful alt (decorative correctly empty), labeled inputs, named
  controls.
- Performance: home 14 queries / ~1.4KB shell; search 14 queries; no N+1;
  analytics is a single guarded INSERT in `terminate()`.
- Mail flow verified on safe array transport (SMTP creds pending).
- Full suite: backend, PHPStan, Pint, tsc, ESLint, Vitest, public + admin
  Playwright, build, caches, migrations, Redis, queue config, scheduler list,
  backup + restore rehearsal, `git diff --check` — see report §N.

## Content & translation gates

Software readiness and council content readiness reported separately
(runbook + checklist). Translation-governance decision recorded as
**council acceptance required** (English fallback verified on all locales).
