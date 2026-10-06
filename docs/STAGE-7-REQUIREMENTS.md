# Stage 7 Requirements Traceability

| ID | Requirement | Current State | Implementation | Evidence | Status |
|---|---|---|---|---|---|
| TOR-ME-VISITOR | Visitor/session metrics (privacy-safe approximation) | MISSING | `analytics_events` + `visitor_key` (session+day hash); labeled “visitor-days (approximate)” | Migration, `RecordAnalytics`, reporter | Built |
| TOR-ME-PAGEVIEW | Page views by date/route/type/locale | MISSING | Middleware page_view events; exclusions (admin/health/assets/bots/downloads) | Middleware, tests | Built |
| TOR-ME-DOWNLOAD | Download reporting | EXISTING — COMPLETE | Reused `document_downloads` + `download_count`; reporter aggregates | `AnalyticsReporter` | Kept |
| TOR-ME-TENDER | Tender detail views | MISSING | content_view events on tender detail; open/closed/awarded context at report time | Routes unchanged, reporter | Built |
| TOR-ME-VACANCY | Vacancy detail views | MISSING | content_view events; open/expired context at report time | Reporter | Built |
| TOR-ME-NEWS | News engagement = article/detail views | MISSING | content_view on news/notice detail; labeled exactly what is measured | Reporter | Built |
| TOR-ME-CONTENT | Content update frequency | EXISTING — EXTEND | Aggregated from `audit_events` (no duplication) | Reporter | Built |
| TOR-ME-REFERRAL | Referral source aggregates | MISSING | Normalized category+domain at capture; no query strings stored | Classifier, tests | Built |
| TOR-AUDIT-REPORT | Filterable audit reporting + CSV export | EXISTING — EXTEND | `AuditReport` page (date/actor/action/type) + permission-gated CSV export; no second log | Page, export, tests | Built |
| TOR-MON-UPTIME | Health endpoint + app-side status data | EXISTING — EXTEND | `/up` preserved unlocalized; `SystemHealth` view (app/db/redis/queue) | Page, FoundationTest | Built |
| TOR-MON-ERROR | Error logging foundation | EXISTING — EXTEND | Laravel log kept; `error_events` structured capture (class/excerpt/route/correlation, sanitized) | Reporter handler, page | Built |
| TOR-MON-MONTHLY | Monthly performance report | MISSING | `MonthlyReport` page (selector, printable, CSV export of metrics) | Page, tests | Built |
| TOR-MON-ALERT | Downtime alert foundation | MISSING | `OperationalAlert` notification + `IncidentManager::notify()` to configured recipients; array-transport tested | Notification, config | Built |

Production/infrastructure dependencies (Stage 8): external uptime polling,
production alert recipients/SMTP, hosted log aggregation.
