# Stage 5 Requirements Traceability

Baseline: `8c3eff8` + `c8cf389`. Stages 3/4 not reopened.

| ID | Requirement | Current State | Implementation | Evidence | Status |
|---|---|---|---|---|---|
| TOR-TENDER-STATUS | Upcoming/Open/Closed/Awarded/Cancelled with honest date-driven availability | EXISTING — COMPLETE | Preserved; award display reuses `displayStatus()` | `Tender::displayStatus()` | Kept |
| TOR-TENDER-AWARD | Award info (contractor, date, reference, amount if published, document, remarks) | EXISTING — EXTEND | Award columns + `recordAward()` gated by new `tenders.award` permission; public detail shows award only when recorded | Migration, `TenderManager`, `TenderResource` award action, `Tender.tsx` | Built |
| TOR-TENDER-COMPLY | Procurement compliance reporting foundation | MISSING | `complianceSummary()` + admin compliance page (totals, open/closed/awarded/cancelled, overdue-close, publication state) | `TenderCompliance` Filament page | Built |
| TOR-VAC-XXX | HR vacancy publishing (reference, employment type, dates, advert, lifecycle) | EXISTING — EXTEND | Added `reference` + `employment_type`; expiry/derivation already worked | Migration, `VacancyManager`, resource fields | Built |
| TOR-PROJECT-XXX | Projects & Programmes directory with filters and detail | MISSING | `CouncilProject` + `ProjectUpdate` models, managers, policies, resources, `/projects` routes, search | New files, `Stage5Test` | Built |
| TOR-INVEST-XXX | Investment module adequacy | EXISTING — COMPLETE | Preserved; no ROI claims; enquiry context added (see below) | Unchanged module | Kept |
| TOR-INVEST-ENQ | Investor enquiry via existing workflow with safe context | MISSING | Enquiry `context_type`/`context_reference`/`organisation`; `/contact?context=investment:slug` resolved server-side; staff see origin | `EnquiryManager::submit`, `PublicEnquiryController`, `EnquiryResource` | Built |
| TOR-TOURISM-XXX | Discoverable `/tourism` experience | MISSING | **Option A (CMS-based)**: `/tourism` renders managed `tourism-mutoko` page + investment links; no over-engineered model | Route, `Tourism.tsx`, draft seed | Built |
| TOR-FEEDBACK-XXX | Feedback/complaint mechanism on enquiry architecture | MISSING | Categories extended (`complaint`, `investment_enquiry`, `service_enquiry`); `/feedback` with type/department/consent; citizen reference from `public_id`; server-side routing; notifications follow established pattern | `FeedbackController`, `Feedback.tsx`, manager | Built |
| TOR-SERVICE-XXX | Service discovery, related documents, service enquiries | EXISTING — EXTEND | Service detail gains related documents + context CTA; context stored safely | `Service.tsx`, routes, `Contact.tsx` context | Built |
| TOR-RATES-XXX | Rates/payment | EXISTING — COMPLETE | Stage 4B foundation preserved; no fake payments | Unchanged | Kept |
