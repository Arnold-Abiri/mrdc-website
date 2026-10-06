# Stage 4 Requirements Traceability (ToR Governance & Transparency)

Stage 4 base: `8c3eff8` (retained unchanged). This pass closes only the ToR
Public Information, Governance, and Financial Transparency gaps.

| ID | Requirement | Status | Evidence |
|---|---|---|---|
| TOR-HOME | Homepage: dynamic slider, quick access, managed sections | Complete | `HomepageSlide` model/manager/policy/resource; `Hero slides` prop with static fallback; quick-access cards link live routes; `urgent_alerts` shared banner in `PublicLayout` |
| TOR-INST | Institutional info: About, Mandate, Vision & Mission, Strategic Goals, Organogram, Ward Maps | Complete (structure; content pending council) | About Us published-capable; `mandate`, `vision-mission`, `organogram` CMS drafts via `Stage4bGovernanceSeeder`; wards directory with `map_url` hook; GIS data recorded as external dependency |
| TOR-LEAD | Leadership: Chairperson, CEO, Councillors, HoDs, officials | Complete (structure; names council-managed) | `Official` + `is_department_head`; public department pages expose head, officials, services, contacts, documents, news/notices; no names seeded |
| TOR-GOV | Governance repository: by-laws, policies, plans, minutes, agendas, schedules | Complete | `Document::CATEGORIES` extended (bylaw, agenda, schedule, organogram, strategic_plan, financial_*); single document centre reused, no second system |
| TOR-DOC | Year grouping, version control, download tracking, filters/search | Complete | `reference_date`-based year filter (not upload year); `document_versions` with `replace()` preserving history + audit; `download_count` + `document_downloads` rows; keyword/year/category/department filters; private docs excluded everywhere |
| TOR-MEET | Full council meeting schedule with agendas/minutes | Complete | `CouncilMeeting` model/manager/policy/resource; `/meetings`, `/meetings/{id}`; draft agenda/minutes stay private; states scheduled/completed/postponed/cancelled |
| TOR-FIN | Financial transparency: budgets, audited statements, reports, procurement plans, awards register | Complete (capability; records council-supplied) | `/transparency` over `Document::FINANCIAL_CATEGORIES` with year/type filters; no records fabricated |
| TOR-RATES | Public rates information foundation | Complete (foundation; figures council-supplied) | `/rates` from `rates-information` CMS page + rate schedules; no ratepayer accounts; no fake online payment; external billing-system integration recorded as dependency |
| TOR-CONTACT | Contact directory from one managed source | Complete | `PublicContact` reused on home, contact page, department pages; footer links routed to `/contact` and live sections; no duplicated contact records |

Provenance: VERIFIED_MUTOKO facts (district profile, census) live only in seeded
drafts/statistics with source notes; every governance/financial record is
COUNCIL_MANAGED; public empty states are honest. Nothing fictional was seeded.
