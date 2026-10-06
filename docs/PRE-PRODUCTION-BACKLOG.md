# Pre-Production Backlog — Final Reconciliation (Stage 8, 6 October 2026)

| ID | Item | Final Status | Owner | Production Blocking? |
|---|---|---|---|---|
| PP-01 | Interface strings into `localization.ts` | CLOSED — Stage 6 audited all 30 public pages; ~90 keys; council content intentionally untranslated | Build | No |
| PP-02 | Shona wording (interface + validation) | COUNCIL DEPENDENCY (see PP-18) | Council | Acceptance-gated (fallback works) |
| PP-03 | Admin Playwright login instability | SUPERSEDED — owner updated the spec (199cd25); Stage 7 added 3 passing authenticated journeys; backend authorization suites green | Build | No |
| PP-04 | Remaining admin E2E workflows | ACCEPTED RISK — representative browser coverage (contact/document/media/department/enquiry/Stage 7 admin journeys) + full backend lifecycle coverage | Build/QA | No |
| PP-05 | Exhaustive attack matrix | SUPERSEDED by Stage 8 release review (representative IDOR/XSS/CSRF/mass-assignment/upload/rate-limit/authz evidence, 0 Critical/High) | Build | No |
| PP-06 | Production content verification | COUNCIL DEPENDENCY | Council | Yes (content gate) |
| PP-07 | Mail credentials, worker supervision, failed-job alerting | HOSTING DEPENDENCY — config + runbook + supervisor docs done; live creds pending | Ops/Stage 8 | Yes (ops gate) |
| PP-08 | Hosting/HTTPS/DNS/TLS/secrets on host | HOSTING DEPENDENCY — documented, unverified without a host | Ops/Stage 8 | Yes (ops gate) |
| PP-09 | Pen-test + TLS header review | ACCEPTED RISK — Stage 8 release review completed; residual exotic permutations noted | Build | No |
| PP-10 | Stage 4 seed drafts review | COUNCIL DEPENDENCY | Council | Yes (content gate) |
| PP-11 | Shona for citizen-service sections | COUNCIL DEPENDENCY (see PP-18) | Council | Acceptance-gated |
| PP-12 | Real-reference rule for open tenders/vacancies | COUNCIL DEPENDENCY (standing rule) | Council | Yes (content gate) |
| PP-13 | Governance/financial content supply | COUNCIL DEPENDENCY | Council | Yes (content gate) |
| PP-14 | Billing/payment integration | COUNCIL DEPENDENCY | Council/Ops | Yes if required |
| PP-15 | M&E dashboard | CLOSED — built in Stage 7 | Build | No |
| PP-16 | Project/tourism/award content | COUNCIL DEPENDENCY | Council | Yes (content gate) |
| PP-17 | Complaint ops readiness | HOSTING DEPENDENCY (staffing/SLA/monitoring) | Ops | Yes (ops gate) |
| PP-18 | Shona approvals | COUNCIL DEPENDENCY | Council | Acceptance-gated |
| PP-19 | Ndebele approvals | COUNCIL DEPENDENCY | Council | Acceptance-gated |
| PP-20 | Screen-reader pass | STAGE 9-adjacent QA — structural checks automated (15 pass); manual NVDA/VoiceOver outstanding | QA | Acceptance-gated |
| PP-21 | Indexation review | HOSTING DEPENDENCY (post-content) | Ops | Yes (ops gate) |
| PP-22 | Uptime polling + alert recipients/SMTP | HOSTING DEPENDENCY | Ops/Stage 8 | Yes (ops gate) |
| PP-23 | Prune scheduling + log aggregation | HOSTING DEPENDENCY — scheduler wired; cron + aggregation pending host | Ops/Stage 8 | Yes (ops gate) |
| PP-24 | Privacy wording approval | COUNCIL DEPENDENCY | Council | Yes (legal gate) |

## Original item detail (historical record)

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

## Stage 5 additions (6 October 2026)

| ID | Area | Item | Severity | Production Blocking | Target |
|---|---|---|---|---|---|
| PP-16 | Content | Council supply/approval: project profiles, progress figures, tourism wording, award publications | High | Yes | Pre-production (council) |
| PP-17 | Operations | Complaint handling readiness: staff routing coverage, response SLAs, notification monitoring | Medium | Yes | Pre-production (ops) |

## Stage 6 additions (6 October 2026)

| ID | Area | Item | Severity | Production Blocking | Target |
|---|---|---|---|---|---|
| PP-18 | Content | Council-approved Shona interface wording + content translations (review via Translation status page) | High | Yes | Pre-production (council) |
| PP-19 | Content | Council-approved Ndebele interface wording + content translations | High | Yes | Pre-production (council) |
| PP-20 | Accessibility | Full manual screen-reader pass (NVDA/VoiceOver) over homepage, services, documents, news, projects, feedback/contact | Medium | Yes | Pre-production (QA) |
| PP-21 | SEO | Production indexation review (Search Console, canonical spot-checks, sitemap submission) after content approval | Medium | Yes | Pre-production (ops) |

## Stage 7 additions (6 October 2026)

| ID | Area | Item | Severity | Production Blocking | Target |
|---|---|---|---|---|---|
| PP-22 | Ops | External uptime polling configuration against `/up` + production alert recipients/SMTP (`OPS_ALERT_RECIPIENTS`) | High | Yes | Stage 8 (ops) |
| PP-23 | Ops | Schedule `analytics:prune` (retention default 365 days) and confirm hosted log aggregation | Medium | Yes | Stage 8 (ops) |
| PP-24 | Content | Council/legal approval of `privacy-policy` draft wording | High | Yes | Pre-production (council) |
