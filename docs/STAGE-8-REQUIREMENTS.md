# Stage 8 Requirements — Production Readiness Gap Analysis

| Area | State | Notes |
|---|---|---|
| Production env documentation | NEEDS IMPLEMENTATION | `.env.example` lacks ops/analytics/CMS vars and production guidance |
| Secrets hygiene | READY | No secrets/keys committed; `.env` git-ignored; local dev credentials only |
| Debug exposure | NEEDS IMPLEMENTATION | `production:check` command to assert `APP_DEBUG=false` etc. |
| HTTPS/secure cookies | NEEDS CONFIGURATION | Session/cookie settings env-aware; `SESSION_SECURE_COOKIE=true` documented for production; trusted-proxy setup documented (no code default) |
| Security headers | READY (extend) | nosniff, referrer-policy, SAMEORIGIN, frame-ancestors, permissions-policy present; HSTS documented as HTTPS-only |
| CSP vs Inertia/Vite/Filament | READY | Policy is framing-only; no `unsafe-eval`; no external origins required |
| Database procedure | NEEDS IMPLEMENTATION | Runbook + backup/restore procedures + rehearsal |
| Migrations safety | READY | Additive-only history; `migrate --force` after backup; never `fresh` |
| Redis/queue | NEEDS CONFIGURATION | Supervisor/systemd worker docs; restart/retry/failed handling |
| Scheduler | NEEDS IMPLEMENTATION | `analytics:prune` scheduled; single cron pattern documented |
| SMTP | EXTERNAL DEPENDENCY | Config parsed + safe-transport verified; live creds pending |
| Alert recipients | NEEDS CONFIGURATION | `OPS_ALERT_RECIPIENTS` documented; actual addresses need approval |
| Uptime monitoring | EXTERNAL DEPENDENCY | `/up` ready; external polling is Stage 8-infra/hosting work |
| Backups | NEEDS IMPLEMENTATION | `backup:create` command + restore rehearsal (this stage) |
| DR plan | NEEDS IMPLEMENTATION | `docs/DISASTER-RECOVERY.md` |
| Release/rollback | NEEDS IMPLEMENTATION | `docs/DEPLOYMENT-RUNBOOK.md` |
| Storage/link/permissions | NEEDS IMPLEMENTATION | Documented in runbook |
| Web server config | NEEDS IMPLEMENTATION | Documented (document root, protections) |
| DNS/TLS/domain | EXTERNAL DEPENDENCY | Documented requirements; no live changes |
| SEO production check | NEEDS IMPLEMENTATION | Domain-dependent checklist |
| Content gate | COUNCIL APPROVAL | Checklist; software vs content reported separately |
| Translations gate | COUNCIL APPROVAL | Recorded as acceptance item, not silent |
| Accessibility PP-20 | NEEDS IMPLEMENTATION | Structured review this stage |
| Security matrix | NEEDS IMPLEMENTATION | Representative release tests this stage |
| Dependencies audit | NEEDS IMPLEMENTATION | composer/npm audit this stage |
| Performance | NEEDS IMPLEMENTATION | Practical checks this stage |
| Route caching | NEEDS IMPLEMENTATION | Closure routes block `route:cache`; converting to controllers |
| Logging | READY (document) | Stack/single, rotation via OS logrotate note, redaction in place |
| Backlog PP reconciliation | NEEDS IMPLEMENTATION | Final table this stage |

No BLOCKED items: every gap has an owner (implementation, configuration,
external, or council).
