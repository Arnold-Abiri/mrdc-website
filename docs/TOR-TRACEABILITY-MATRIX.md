# Final ToR Traceability Matrix (contractual reference)

Format: Requirement → Capability → Route/Admin → Docs → Verification → Status.

- Public website (home, pages, search, contact) → `/en…` routes + 30 React
  pages → Stage 3–6 docs → 120 backend + 34 public browser tests → Delivered.
- Institutional/leadership/departments/wards → CMS + Officials/Departments/
  Wards resources → ADMIN-USER-GUIDE → backend + browser → Delivered
  (content: council acceptance pending).
- Governance repository + meetings → Document centre (18 categories, versions,
  year filters) + CouncilMeeting → Stage 4B → Delivered (records: council pending).
- Financial transparency + rates → Transparency + Rates + award flow →
  Stage 4B/5 → Delivered (records: council pending).
- Tenders/vacancies (lifecycle, awards, compliance) → Tender/Vacancy resources
  → Stage 4/5 → Delivered.
- Projects/investment/tourism → CouncilProject/Investment/CMS tourism →
  Stage 4/5 → Delivered.
- Enquiries/feedback/complaints (private workflow, reference, routing) →
  Enquiry admin + `/contact`, `/feedback` → Stage 3/5 → Delivered.
- Multilingual (en/sn/nd URLs, translations, fallback, hreflang, sitemap,
  sitemap locales) → Translation* + locale routing → Stage 6 →
  Delivered (wording: council pending).
- Accessibility (AA baseline, controls, reduced motion, carousel, forms) →
  Stage 6 → Delivered (screen-reader pass: QA pending, PP-20).
- SEO/OG/schema/robots → Stage 6 → Delivered (indexation review: ops pending).
- M&E/audit/monitoring/alerts/monthly → Analytics*/Audit*/SystemHealth/
  MonthlyReport + commands → Stage 7 → Delivered (polling/recipients: ops pending).
- Security (release review, audits, headers, throttles, policies) → Stage 8
  SECURITY-RELEASE-REVIEW → 0 Critical/High → Delivered.
- Deployment/backups/DR/runbook/checklist → Stage 8 docs + rehearsed restore
  → Delivered (hosting execution: ops pending).
- Training/documentation/handover → Stage 9 package → Materials ready
  (delivery/acceptance pending).

Stage detail lives in `docs/STAGE-*-REQUIREMENTS.md` and
`docs/STAGE-*-IMPLEMENTATION.md`; this file is the consolidated contractual map.
