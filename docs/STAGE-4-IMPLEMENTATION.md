# Stage 4 Implementation — Citizen Services, Resources & Council Information

## Scope delivered

Citizen service directory on top of Stage 3, reusing existing models (no duplicates):

- **Tenders & procurement** (`/tenders`, `/tenders/{slug}`): reference, category, description, opens/closes dates, lifecycle (upcoming/open/closed/awarded/cancelled) with date-derived public status, contact instructions, linked tender document, department. No fictional tenders seeded.
- **Vacancies** (`/vacancies`, `/vacancies/{slug}`): title, grade, description, responsibilities, requirements, opening/closing dates with open/closed derivation, application instructions, downloadable advert. Expired vacancies read closed; old vacancies are never seeded as open.
- **Investment** (`/investment`, `/investment/{slug}`): sector, summary, description, location, opportunity status, supporting document, enquiry CTA. Seeded prospects only (solar, mining, horticulture) as demo — no return claims.
- **District statistics** (homepage "Our district"): label, value, unit, icon, internal source note, ordering, active flag — fully council-editable, never hard-coded in React.
- **About Mutoko** (`/about` → `/pages/about-mutoko`): CMS page seeded from public sources (founded 1911, 143 km from Harare, ~4,092 km², 161,091 in 2022, 29 wards, Buja people, growth point, Jani). Seeded as draft/demo for council review and publication.
- **Downloads alias** (`/downloads` → `/documents`): the Stage 3 document centre is the single resource system.
- **Homepage**: quick-access cards now link to live `/tenders`, `/vacancies`, `/investment`; managed sections added for district statistics, tenders, and investment alongside existing services/documents/notices/leadership/wards/contacts.
- **Search**: discovers published tenders, vacancies, and investment opportunities; never drafts or internal records.
- **Mutoko service content**: nine verified service categories (education, environment, roads & works, health, business-centre servicing, property, recreation, natural resources, welfare) seeded as drafts for council review — no duplicates of Stage 3 records (idempotent by slug).
- **Enquiry integration**: tender, vacancy-adjacent, and investment pages link to `/contact` ("Ask about this service"-style CTAs). Citizen issue/fault reporting is NOT implemented: no agreed screenshot/SRS requirement was found — recorded below as a future enhancement.

## Models / schema

New migration `2026_10_06_000000_create_stage4_citizen_services`: `tenders`, `vacancies`, `investment_opportunities` (all with `status`, `verification_status`, `published_at`, `display_order`, `department_id`, audit columns mirroring Stage 3 lifecycle), plus `district_statistics` (label/value/unit/icon/source_note/order/active flag).

## Routes (new)

| Route | Purpose | Status |
|---|---|---|
| `/tenders`, `/tenders/{slug}` | Tender directory + detail with open/closed honesty | PASS |
| `/vacancies`, `/vacancies/{slug}` | Vacancy directory + detail with closing-date behavior | PASS |
| `/investment`, `/investment/{slug}` | Investment section + detail with enquiry CTA | PASS |
| `/about` → `/pages/about-mutoko` | Council history/profile via CMS page | PASS |
| `/downloads` → `/documents` | Alias to the managed document centre | PASS |

Existing services/documents/news/notices/wards/departments/officials/contact/search routes unchanged and extended (search + homepage props).

## Administration

| Resource | Capabilities | Status |
|---|---|---|
| Tenders | CRUD, verify/publish/unpublish/archive, lifecycle transitions, scoped visibility | PASS |
| Vacancies | CRUD, verify/publish/unpublish/archive, scoped visibility | PASS |
| Investment opportunities | CRUD, verify/publish/unpublish/archive, scoped visibility | PASS |
| District statistics | CRUD, active toggle, scoped to statistics permissions | PASS |

Permissions added (idempotent `SecuritySeeder` rerun): `tenders.*`, `vacancies.*`, `investment.*`, `statistics.*` (view/create/update/publish/verify as applicable). Stage 2 roles/scopes and audit events preserved; lifecycle changes audited.

## Mutoko content migration

| Content | Source | Destination | Status |
|---|---|---|---|
| Council history/profile | Public sources (district profile, census, gazetteer facts) | `pages/about-mutoko` (draft) | VERIFIED_MUTOKO, pending council publish |
| 9 service categories | Existing Mutoko site service areas | `services` (drafts) | VERIFIED categories, council-owned wording pending |
| Solar/mining/horticulture | Existing Mutoko site opportunity areas | `investment_opportunities` (drafts) | Sector facts verified; status prospecting |
| Wards 29 / population / area / founded | Brief + census + public profile | `district_statistics` (active) | Sourced per-row; replaceable by council |
| Tenders, vacancies, fees, payments, officials, councillors | — | NOT seeded | COUNCIL_MANAGED only; never fabricated |

## Tests

`tests/Feature/Stage4CitizenServicesTest.php` — 8 tests, 39 assertions: draft privacy, publish visibility, tender date-derived closed status, cancellation, vacancy expiry, investment lifecycle + search, statistics activation, alias redirects, unauthorized-create denial. `tests/e2e/stage4-public.spec.ts` — 4 smoke tests (homepage entry points, empty states, aliases, search).

## Deferred items

- Stage 5 candidates: council Projects/events section,ckan-style transparency datasets, online payments (only with approved integration — no fake "Pay Now" built), citizen issue/fault reporting (no agreed requirement found; future enhancement).
- Pre-production (added to `docs/PRE-PRODUCTION-BACKLOG.md` as PP-10–PP-12): council publish review of all Stage 4 seed drafts, Shona wording for new sections, production content approval for tenders/vacancies before any are shown as open.
