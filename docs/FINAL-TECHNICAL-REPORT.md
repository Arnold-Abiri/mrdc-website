# Final Technical Report — Mutoko RDC Website

## Executive summary
A Laravel 13 + Inertia + React + Filament council website was built across
Stages 1–8 and prepared for handover in Stage 9. Final software status:
**CONDITIONAL GO — TECHNICALLY READY; EXTERNAL/COUNCIL GATES REMAIN.**
Training materials and handover package are ready; delivery, production
deployment, and council acceptance have not occurred.

## Project objectives
Replace/augment the council web presence with a managed, multilingual,
accessible, transparent citizen-services platform with back-office workflows,
reporting, and operational monitoring.

## Delivered solution
~30 public pages (home, CMS, services, documents/downloads, departments,
officials, wards, news, notices, tenders + awards, vacancies, projects,
investment, tourism, meetings, transparency, rates, contact, feedback,
search); 23 Filament resource areas; en/sn/nd URLs with content translations;
M&E/audit/health/monthly reporting; error/incident operations; backup tooling.

## Architecture
See `docs/SYSTEM-ARCHITECTURE.md`: monolith, MySQL 8, Redis, supervised
workers, scheduler, SMTP/API mail, managed storage, closure-free cacheable
routes, RBAC with department scopes, audit + analytics pipelines.

## Functional modules
Per acceptance checklist (all delivered as software; content/wording pending
council): CMS, media/documents (versions, year filters, download stats),
governance, transparency, tenders/awards/compliance, vacancies, projects,
investment/tourism, enquiries/feedback (private), meetings, contacts, search.

## Security
Release review: 0 Critical/High/Medium; representative IDOR/XSS/CSRF/mass-
assignment/upload/throttle/auth evidence; clean dependency audits; hardened
headers; redacted logs; permission matrix enforced and tested.

## Accessibility
WCAG 2.1 AA target: skip link, landmarks, focus treatment, keyboard carousel,
font/contrast controls, reduced-motion support, labeled forms with error
summaries, meaningful alt + admin missing-alt flags, 15 structural browser
checks. Full screen-reader pass outstanding (PP-20).

## Multilingual support
en/sn/nd prefixed routing with legacy redirects, session preference,
page-preserving selector, polymorphic content translations with fallback and
completeness tracking, hreflang/canonical/localized sitemap. Council wording
pending (PP-18/19).

## M&E
Privacy-preserving first-party events (visitor-days approximate, never
people), downloads/tenders/vacancies/news reporting, referrals, language
usage, content activity, monthly printable/CSV reports, 365-day raw retention.

## Deployment architecture
Runbook + checklist + `production:check` + cache-compatible routes +
supervised workers + scheduler + sealed-env discipline. Hosting/DNS/TLS/SMTP
are external gates.

## Backup & disaster recovery
`backup:create` (DB dump + files + manifest) verified; restore rehearsed into
disposable DB (45 tables, 24/24 migrations, boot verified); 8-scenario DR plan
with proposed RPO 24h / RTO 4h (approval required).

## Testing & QA
Backend 120 pass / 1 skip; PHPStan 0; Pint clean; tsc/ESLint/Vitest (16)
green; Playwright 34 public + 3 admin green; build green; caches verified;
restore rehearsed. Historical QA debt (PP-03/04/05/09) closed or superseded
with recorded rationale.

## Training
Materials ready for 7 staff across 6 sessions + ICT/HR/Finance/
cybersecurity/data-protection tracks, exercises, blank assessment/attendance/
feedback records. **Delivery pending.**

## Maintenance
Daily/weekly/monthly/quarterly SOP, operations checklist, support/escalation
model, change-management classification (content vs config vs fix vs feature
vs security).

## Outstanding external dependencies
Hosting, domain/DNS, TLS, APP_URL, SMTP, alert recipients, uptime polling,
prune/log activation, approved content, Shona/Ndebele, legal/privacy,
screen-reader decision, training delivery, handover performance, council
acceptance — see report §N.

## Recommendations
1. Stand up hosting per runbook; execute checklist gates in order.
2. Deliver training; complete attendance/assessment/feedback honestly.
3. Perform handover; collect sign-off per checklist.
4. Treat post-handover work as support/change-request, not stages.

## Conclusion
The software is complete and technically ready; what remains is operational:
infrastructure, content, training delivery, and acceptance.
