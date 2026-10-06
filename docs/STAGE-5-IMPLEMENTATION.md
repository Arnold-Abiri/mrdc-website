# Stage 5 Implementation — Opportunities, Projects & Citizen Participation

Traceability: `docs/STAGE-5-REQUIREMENTS.md`. Stages 3/4 preserved; Tenders,
Vacancies, Investment extended, never rebuilt.

## Delivered

- **Tender awards**: `award_status`/`awarded_to`/`awarded_at`/`award_amount`/
  `award_reference`/`award_document_id`/`award_remarks` on tenders;
  `TenderManager::recordAward()` gated by new `tenders.award` permission
  (awarded state requires contractor + date); awarded tenders read closed via
  existing date/lifecycle logic; public detail shows an award section only when
  recorded — no evaluation internals, no fictional awards.
- **Procurement compliance**: `TenderManager::complianceSummary()` (total, open,
  closed, awarded, cancelled, past-closing-not-closed, published, drafts) with a
  `TenderCompliance` admin page. No export (no existing export convention found);
  not the Stage 7 M&E dashboard.
- **Vacancies verified/completed**: added `reference` (unique, optional) and
  `employment_type` (full/part/contract/temporary/internship); HR publish flow,
  expiry derivation, and advert documents already worked and are covered by tests.
- **Projects & Programmes**: `CouncilProject` (type, department, multi-ward
  pivot, location, timeline, council-managed progress, featured image, document
  pivot, contact context, full lifecycle) + `ProjectUpdate` (date, title,
  summary, progress, media, publish/unpublish); `/projects` with
  status/type/department/ward filters; `/projects/{slug}` with timeline,
  progress, updates, documents, wards; search-integrated; homepage section.
- **Investor enquiries**: enquiry `context_type`/`context_reference`/
  `organisation`; `/contact?context=investment:slug` resolved server-side
  against published records only (forgeries dropped); category recorded as
  `investment_enquiry`; staff identify origin in the Enquiry admin (Origin
  column/entry); existing routing/assignment/audit/notifications reused.
- **Tourism (Option A, CMS-based)**: `/tourism` renders managed
  `tourism-mutoko` page + investment links; draft seeded with established
  facts only. No new model, no Masvingo content.
- **Feedback & complaints**: `/feedback` (type incl. complaint, department
  selector mapped server-side to validated departments, consent checkbox,
  honeypot, throttle); citizen reference from `public_id` shown on confirmation;
  complaints are private (absent from search/sitemap/public lists); submit-time
  staff notification follows the established pattern; full lifecycle/routing/
  assignment/audit reused. No separate status engine, no GIS fault system
  (road/refuse/infrastructure issues fit complaint categories; dedicated
  fault-management stays a future enhancement).
- **Service discovery**: service detail gains related department documents and
  a context-aware “Ask about this service” CTA (`service_enquiry` stored with
  verified slug); no inapplicable required fields.
- **Rates/payments**: Stage 4B foundation preserved; no online payment built
  (no approved provider) — accurate informational states only.
- **Homepage/nav**: featured projects section, feedback quick-access card,
  Projects/Tourism nav entries; Theme 1 retained.

## Tests

`Stage5ParticipationTest` (7 tests, 41 assertions): award lifecycle/validation/
permission, vacancy fields/expiry, project publish/filter/detail/search/home,
investor context + forgery rejection + privacy, complaint reference/consent/
privacy, service context. `stage5-public.spec.ts` (4 journeys): projects filter→
detail; investment→enquiry form; feedback→reference; tender/vacancy/tourism
rendering.

## Content

Nothing fictional seeded (one `tourism-mutoko` draft with established facts).
All project/tender/vacancy/complaint content is council-managed.
