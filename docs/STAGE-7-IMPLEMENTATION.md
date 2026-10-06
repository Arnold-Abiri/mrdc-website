# Stage 7 Implementation — M&E, Audit Reporting & Operational Monitoring

Traceability: `docs/STAGE-7-REQUIREMENTS.md`. No second audit log, no second
download tracker, no new logging framework, no third-party analytics.

## Event model

`analytics_events`: `event_type` (page_view/content_view), `route`, `locale`,
`subject_type`/`subject` (slug for tenders, vacancies, news, notices, services,
documents, projects, investment, meetings, pages, officials, wards,
departments), `visitor_key`, `referrer_category`, `referrer_domain`,
`created_at`. No arbitrary metadata JSON; no query strings stored; no
names/emails/enquiry bodies.

## Privacy approach

- First-party only; no advertising/tracking cookies; locale/a11y preferences
  stay in localStorage; sessions remain strictly necessary.
- `visitor_key` = sha256(app key, session id, date): one browser counts once
  per day. Reported strictly as **visitor-days (approximate)** and **sessions
  (≈ visitor-days)** — never “people”.
- Exclusions: admin, `/up`, assets, non-200s, downloads/media (covered by
  `document_downloads`), obvious bots/probes (UA list), requests without
  sessions. Metrics are labeled approximate operational figures.
- Retention: `analytics:prune` (default 365 days, minimum 30); audit logs are
  never pruned by it. `privacy-policy` CMS draft records collection, purpose,
  retention, and cookies for council/legal review.

## Metric definitions

- Page views: 200 OK public GETs by date/route/locale. Content views: detail
  routes with subject; tender/vacancy open context resolved at report time;
  news engagement = article/detail views only (no invented scores).
- Downloads: reused `document_downloads` (totals, periods, top docs, category).
- Content updates: audited `*.published|*.updated|documents.replaced` events.
- Referrals: direct / search / social / internal / external / other with
  normalized registrable domains (Facebook classified Social, never guessed).

## Capture path

`RecordAnalytics` middleware on the public locale groups only; `terminate()`
does one guarded INSERT (failure can never break the page). Rendering never
depends on analytics. Dashboard queries never run on citizen requests.

## Dashboards & reports (all `/admin`, permission-gated, GET-filtered)

- `AnalyticsDashboard` (`analytics.view`): period (today/7/30/month/prev-month/
  year/custom), KPI cards, daily trend table, popular content/tenders/vacancies/
  news, top downloads, referrals, language usage, recent content activity.
  Tables serve as the accessible alternative; honest empty states.
- `AuditReport` (`audit.view`): date/actor/action/entity filters over existing
  `audit_events`; CSV export (`audit.export`) via `/admin/reports/audit-export`.
- `MonthlyReport` (`analytics.view`): month selector, printable view, metrics
  CSV (`analytics.export`), KPIs, referrals, languages, incidents, error
  counts, content activity.
- `SystemHealth` (`system-health.view`): app/database/Redis/queue statuses
  (Healthy/Degraded/Unavailable) with safe details only.
- `ErrorEventResource` + `IncidentResource`: error list/view/resolve and
  manual incident CRUD; incident changes audited.

## Errors, health, alerts

- Laravel logging kept; `ErrorMonitor` (hooked via `report()`) stores class,
  sanitized 500-char summary (emails/secrets redacted), route, correlation ID;
  ignores validation/auth/4xx. Recursion-guarded and swallow-safe: a DB outage
  yields clean 500s, never OOM (regression found and fixed in-stage).
- `/up` preserved unlocalized and minimal. External polling, production
  recipients/SMTP, and hosted log aggregation are Stage 8.
- `DowntimeAlerter` + `OperationalAlert` (existing queued mail pattern) notify
  `ops.alert_recipients` (env, default empty = silent); array-transport tested.

## Permissions & roles

New: `analytics.view`, `analytics.export`, `audit.export`, `system-health.view`
(`audit.view` pre-existed). System Administrator holds all; Auditor gains
`analytics.view` + `audit.export`. Content roles unchanged (no global
visibility granted).

## Verification notes

- Backend covers tracking, referral normalization, exclusions, dashboard/audit/
  export/health authorization, error sanitization, incident lifecycle + alert,
  retention isolation, monthly aggregation.
- Browser: 3 authenticated journeys (dashboard + period, audit filter, health)
  against disposable MySQL; public suites unchanged and green.
