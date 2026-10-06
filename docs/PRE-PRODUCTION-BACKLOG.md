# Pre-Production Backlog — Stage 3 deferred work

Stage 3 verdict: **GO — READY FOR STAGE 4 DEVELOPMENT** (development-progression
decision, 6 October 2026). This is NOT a production-release clearance.

A **development progression blocker** stops Stage 4 work (broken core workflow,
confirmed Critical/High, auth bypass, data corruption, broken migration).
A **production release blocker** stops public launch but not Stage 4 development.
Every item below is the second kind unless stated otherwise.

| ID | Area | Item | Severity | Production Blocking | Target |
|---|---|---|---|---|---|
| PP-01 | Localization | Remaining hard-coded public JSX interface strings (~60 heuristic candidates) moved into `localization.ts` with fallback tests | Medium | Yes | Pre-production |
| PP-02 | Localization | Council-approved Shona wording for interface keys and validation messages (`shona` map currently intentionally empty; no machine translation of council content) | Medium | Yes | Pre-production (council) |
| PP-03 | QA automation | Admin Playwright stability: systematic login-redirect failures observed 6 Oct 2026 (`toHaveURL(/\/admin\/?$/)` stuck on `/admin/login`; likely Livewire/Filament throttle-key mismatch in `beforeEach` rate-limiter clear). Subsequent 404-vs-200 mismatches are cascade artefacts (unauthenticated admin URLs redirect to the login page with HTTP 200), not confirmed auth bypass — backend scoping tests pass. Revision-restore modal flow still without stable browser coverage | Medium | Yes (release QA) | Pre-production |
| PP-04 | QA automation | Remaining admin E2E workflows: document replacement, media metadata/archive negatives, notice expiry boundaries, ward/official ordering, department edit/disable, enquiry reassignment, explicit notification/audit browser assertions | Medium | Yes (release QA) | Pre-production |
| PP-05 | Security | Exhaustive resource × attack matrix (all IDOR/scope permutations, stored-XSS payload variants per text field, mass-assignment/protected-field checks per resource, hidden Filament lifecycle-action invocation) — representative controls pass (uploads, CSRF, search bounds, guest/disabled/limited access, department scoping) | Medium | Yes (pen-test gate) | Pre-production |
| PP-06 | Content | Production content verification and stakeholder acceptance of all CMS pages, departments, officials, wards, services, news, notices, contacts | High | Yes | Pre-production (council) |
| PP-07 | Mail/queue | Production mail credentials (SMTP/API), live queue worker supervision/monitoring, failed-job alerting | High | Yes | Pre-production (ops) |
| PP-08 | Hosting | Hosting configuration, HTTPS/DNS/TLS, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, managed secrets verified on deployed host | High | Yes | Pre-production (ops) |
| PP-09 | Security | Pre-production penetration/security verification of PP-05 matrix plus production-header review behind TLS | Medium | Yes | Pre-production |

Confirmed unresolved Critical: **0**. Confirmed unresolved High: **0**.
No known authorization bypass (backend department-scope tests pass; see PP-03 for the browser artefact explanation).

## Stage 4 additions (6 October 2026)

| ID | Area | Item | Severity | Production Blocking | Target |
|---|---|---|---|---|---|
| PP-10 | Content | Council review and publish decision for all Stage 4 seed drafts (about page, 9 services, 3 investment prospects); seeded statistics values re-confirmed | High | Yes | Pre-production (council) |
| PP-11 | Localization | Shona wording for new citizen-service sections (tenders, vacancies, investment, statistics, about) | Medium | Yes | Pre-production (council) |
| PP-12 | Content | No tender or vacancy may be shown as open without a real council reference, document, and closing date | High | Yes | Pre-production (council) |

## Stage 4B additions (6 October 2026)

| ID | Area | Item | Severity | Production Blocking | Target |
|---|---|---|---|---|---|
| PP-13 | Content | Council supply/approval: budgets, audited statements, by-laws, policies, minutes, agendas, meeting schedules, rates figures, organogram chart, ward GIS/boundary data, leadership names and photos | High | Yes | Pre-production (council) |
| PP-14 | Integration | Account-specific rate checking if required (external billing system/API); online payment only via approved integration | Medium | Yes | Pre-production (council/ops) |
| PP-15 | Analytics | M&E dashboard consuming `document_downloads` aggregates | Low | No | Later stage |
