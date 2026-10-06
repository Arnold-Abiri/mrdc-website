# Administrator User Guide — Mutoko RDC Website

For council staff. Conventions used everywhere: **Draft → Verified/Publishable
→ Published** (edit returns items to draft); **Unpublish/Archive** removes from
public; destructive deletes are disabled — nothing important can be deleted by
mistake. Every significant action is audit-logged under your name.

## Login & dashboard
`/admin` → sign in with your council account. Disabled accounts cannot enter.
Use **Dashboard** for an overview; role menus show only what you may use.

## CMS pages (Pages)
Purpose: managed informational pages (About, mandate, tourism, rates).
Create: title, URL slug (lowercase-words), heading/paragraph/CTA blocks.
Publish only council-approved wording. Editing a published page returns it to
draft. Mistake to avoid: changing a slug breaks existing links/bookmarks.

## Media library (Media)
Purpose: images and PDFs used across the site. Upload genuine council files
only; executables/mismatches are rejected — do not bypass rejections. Add
meaningful **alt text** for informative images (the list flags Missing alt).
Archive instead of deleting where possible.

## Document Centre (Documents) — read carefully
Purpose: the single repository for forms, policies, by-laws, plans, minutes,
agendas, budgets, statements, reports, adverts. Fields: title, slug, category
(18 governed types), file (PDF), department, visibility, applicable
**reference year** (the document's year, not upload year).
**Replace file** (row action) supersedes a file: history preserved, version
incremented, back to draft for re-verification. Create-new is only for
different publications. Public sees only the current approved version with
download counts. Privacy: `Private` visibility stays out of public/search.

## Departments / Officials / Wards
Departments: internal name/code plus public profile (name, summary,
description, responsibilities, display order). Officials: name, title,
biography, photo + alt, department, **Head of department** flag — no
unverified names or private contacts. Wards: name, description, boundaries
(no invented boundaries; GIS pending). Publish to appear publicly.

## Services
Name, summary, description, requirements, process steps, fees (only approved
figures), department, ordering, SEO fields. Keep steps factual and short.

## News & notices (News and notices)
Type News/Notice, title, body (plain text), category, image, department,
expiry for notices (expired leave public automatically), **Urgent** flag drives
the homepage alert banner until expiry/withdrawal. Linked public documents
attach read-only.

## Homepage slides
Headline, supporting text, button label + internal link, image, order,
active flag. Slides rotate manually (previous/next); no auto-play to manage.

## Contacts (Public contacts)
Office, type (phone/email/address), value, department, ordering. These feed
the homepage, contact page, and department pages — edit once, everywhere.
No unapproved numbers/addresses.

## Council meetings
Title, type (full council/committee/special/hearing), date/time/venue, status
(Scheduled/Completed/Postponed/Cancelled). Attach agenda/minutes **documents**
— drafts stay private; only published-public documents appear. Past meetings
keep minutes for the record.

## Financial transparency & rates
Publish approved budgets/statements/reports/procurement plans/awards-register
documents with correct year + category; they surface under Transparency with
filters. Rates: maintain the rates information page plus schedule documents.
Never publish private ratepayer data; no online payments exist.

## Tenders
Reference, title, category, description, open/close dates, lifecycle, contact
instructions, tender document, department. Public open/closed derives honestly
from dates. **Record award** (contractor, date, reference, amount only if
cleared, document, remarks) — evaluation internals are never published.
`Tender compliance` page shows totals/overdue items.

## Vacancies
Title, reference, grade, employment type, duties, requirements, open/close
dates, instructions, advert. Expired reads Closed automatically — never
republish old adverts as open.

## Projects & Investment & Tourism
Projects: title, type, department, wards (multi-select), location, timeline,
council-managed progress %, image, documents, contact notes; **Project
updates** record dated progress separately. Investment: sector, summary,
location, status, document; investor enquiries arrive as normal enquiries
marked with origin. Tourism is a managed page plus investment links.

## Enquiries & feedback
Enquiries list (scoped to your departments), internal notes (staff-only),
status workflow, routing/assignment with audit. Origin column shows general /
service / investment / feedback / complaint sources. Complaints are private
end-to-end. Notifications arrive by mail when queued workers run.

## Translations
English is the source. Shona/Ndebele tabs per record; empty = English
fallback. Only council-approved wording. **Translation status** page shows
complete/partial/missing per area.

## Reports (role permitting)
M&E dashboard (periods, KPIs, trends, popular content, referrals, languages —
visitor metrics are approximate); Monthly report (printable + CSV); Audit
report (filters + CSV export); System health; Error events (resolve when
handled); Incidents (record outages factually).

## Users, roles & audit
User/role administration is ICT-only. Auditors review; nobody edits audit
records. Report access problems to ICT — never share accounts.
