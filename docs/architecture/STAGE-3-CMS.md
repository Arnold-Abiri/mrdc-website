# Stage 3 CMS implementation status

This document records the current page foundation. Stage 3 is incomplete and must not be approved for production or Stage 4.

## Page content model

`pages` stores a unique slug, title, summary, structured blocks, SEO text, lifecycle state, verification state, optional department, editor IDs, and publication timestamp. `page_revisions` stores snapshots on creation, update, verification, publication, unpublication, archive, and restoration. Editing a published page returns it to draft and demo verification so changes require a fresh review and publication decision. Restoring a revision creates a new draft revision. Historical revisions are not modified.

Supported blocks are heading, paragraph, and internal link. The server validates block types, text length, and link paths. React renders text nodes without raw HTML. The public route uses a published query scope and only returns public fields. Draft, unpublished, archived, and future-dated pages return 404.

## Editorial and content governance

Lifecycle: draft → published → unpublished or archived. Publishing requires `pages.publish`. Verification requires `pages.verify`. The verification states are `demo`, `verified`, and `publishable`. New pages default to demo. Production publication accepts only publishable content. Downgrading verification in production automatically unpublishes a page. The existing development-preview notice and noindex behavior remain in the public shell.

Verification is an application state, not evidence of council approval by itself. Council review, source records, and approval dates still need a dedicated workflow before production. No official content is seeded by this change.

## Authorization and scope

Page viewing, creation, editing, publication, and verification use granular permissions and Stage 2 role scopes. A department scope applies to pages assigned to that department. Own scope applies to pages created by the actor. Council-wide pages with no department require a global grant unless the actor owns a new page. List queries use `DataScopeAuthorizer::apply`; direct records and all manager mutations use `allows` through the policy or explicit create/target-scope checks.

## Audit and database

`PageManager` performs mutations in transactions and writes audit events through the existing redacting `AuditWriter`. The migration adds indexes for public queries, foreign keys to users and departments, a unique slug, unique revision numbers per page, and MySQL checks for lifecycle and verification values. The schema is designed for future translation records; translations are not implemented yet.

## Current implementation status

Stage 3 management and public read paths now exist for media, documents, department profiles, services, news, notices, wards, officials and public contacts. Enquiries support routing, assignment, notes and workflow notifications. Search spans eligible public resource types, and the homepage consumes approved services, documents, department profiles, news and notices. Static Stage 1 content remains demo/unverified under the development content policy.

Release blockers remain: administration Playwright workflows have not been implemented; localization requirements have not been established and translation storage/editor/public routes do not exist; the full adversarial security matrix is incomplete; and council stakeholders have not approved real content. Notifications dispatch after committed state changes, but mailbox/worker delivery, retry behavior and operational alerting have not been integration tested. The homepage does not yet integrate contacts, officials or wards as managed sections.

## Remediation audit (1 October 2026)

| Area | Existing | Partial | Missing | Action |
|---|---|---|---|---|
| CMS pages | Structured blocks, revisions, governance, Filament, scopes, audit, public detail | No translations or media blocks | — | Preserve and extend |
| Media and documents | Storage configuration only | — | Managed library, document centre | Build governed upload and publication chains |
| Departments | Stage 2 organisation entity and security scope | — | Public profiles | Extend the authoritative department entity |
| Officials, wards, services | — | Static service previews | Structured resources and public directories | Build domain models, policies and public views |
| News and notices | — | Static demo fixtures removed from public display | Governed publication | Build lifecycle and public views |
| Contacts and enquiries | Public enquiry intake, staff status audit | Unverified static contact claims removed; enquiry routing and assignment incomplete | Managed contacts | Finish contact management and enquiry workflow |
| Search | — | Published CMS page search with bounded query and literal wildcard handling | Other Stage 3 resource adapters | Extend search as resources become available |
| Localization | — | English content only | Translation storage and governance | Add localized records without fabricating translations |
| Homepage | Theme 1 shell and provisional copy | Unverified figures and fictional updates removed | Managed sections | Integrate verified CMS resources |
| Testing | CMS page feature tests and public shell Playwright | Search isolation regression test | Stage 3 workflows and full adversarial matrix | Add module and browser tests |

Search uses per-resource public eligibility scopes for CMS pages, documents, services, department profiles, news, notices, wards and officials. It returns public fields only, escapes wildcard characters, bounds queries and caps combined results at 20. Enquiries and unapproved content are excluded. Localization remains unimplemented.

The prior Stage 3 NO-GO remains in force. The homepage no longer displays unsupported ward/project/growth-point counts, fictional news or events, or unapproved phone and email details. Other provisional wording and imagery still require council review.

## Public enquiry increment

`enquiries` stores a UUID public identifier, contact fields, category, optional department, message, status, assignment field, and timestamps. The public `/contact` form accepts validated submissions only; it has CSRF protection, a five-per-minute route throttle and a honeypot field. Public routes do not accept status or assignment, and do not expose enquiry lists or detail. A successful submission redirects to a generic acknowledgement, without disclosing a record identifier.

Staff can view scoped records in Filament and change status through `EnquiryManager`. The manager reauthorizes inside a transaction and records status changes without message or contact data in audit metadata. `enquiries.view` and `enquiries.update` are seeded idempotently for System Administrator. Department scope applies to both list queries and direct records. Enquiries have no owner relationship, so Own scope deliberately yields no records. The shared scope query helper now accepts a null owner column to enforce this.

This is an increment, not a completed contact or enquiry programme. Department routing, assignment, a managed public contact source and Stage 3 browser workflows remain outstanding. Fresh seeded MySQL migration and Stage 2 to current Stage 3 forward migration passed on disposable databases on 2 October 2026. The forward test preserved one department, one user, four roles and one role scope. Repeated seeding left record counts unchanged. Both disposable databases were removed afterward. Future migrations require the same verification.

The six Playwright tests now run against a migrated disposable MySQL database by setting `DB_DATABASE` for the test process. The Playwright web server uses PHP's built-in server because `artisan serve` rereads `.env` and ignored the test database override. Two Stage 3 browser cases cover empty search and contact page rendering at mobile and desktop widths; the administration and submission workflows in the remediation brief remain untested in Playwright.


## Enquiry routing increment (2 October 2026)

The enquiry manager now enforces explicit status transitions: new to in progress or closed; in progress to resolved or closed; resolved to in progress or closed; and closed to in progress. Repeating a state or skipping the allowed path is rejected. Department routing requires `enquiries.route` for both the current record and destination department. Routing clears any staff assignment. Assignment requires `enquiries.assign` on the record and an active staff member in the routed department who can view that enquiry. Manager operations reauthorize after row locking and audit status, routing, assignment and internal note events. Audit metadata excludes note text and resident contact details. Staff notes are stored in `enquiry_notes` and only shown on the scoped administrative view. Public enquiry routes expose neither notes nor sequential IDs.

The normal development database was brought forward with the pending CMS, enquiry and note migrations on 2 October 2026 without `migrate:fresh`. Security seeding was executed twice successfully. Redis responded to `PING`. The full backend suite, PHPStan, Pint, TypeScript, ESLint, Vitest, build and existing six Playwright tests passed. This remains an increment: Stage 3 administration E2E, notification integration, and the other resources in the blocker matrix are outstanding. The Stage 3 verdict remains NO-GO.


### Database verification for this increment

A disposable MySQL database `mutoko_stage3_verify_20261002` completed `migrate:fresh --seed` and a second seed. A second disposable database `mutoko_stage3_upgrade_20261002` received Stage 2 migrations, then representative data: one user, one department, four roles, one role scope and one audit event. After all Stage 3 migrations and a repeat seed, those counts remained 1, 1, 4, 1 and 1; `enquiry_notes` existed. Both disposable databases were removed. The normal `mutoko_rdc` database was migrated forward without a fresh reset, and `migrate:status` shows no pending migrations. Redis returned PONG, while Laravel cache and queue drivers are configured as database, so no Redis integration is required by the current runtime configuration.

## Phase A implementation (2026-10-02)

Managed media and documents have been introduced with forward-only schema changes. Media files use a configurable private Laravel disk (`CMS_MEDIA_DISK`, default `local`) and a configurable upload limit (`CMS_MAX_UPLOAD_KB`, default 10240). Uploads are checked by `finfo` against an allowlist of JPEG, PNG, WebP and PDF. Stored names are generated UUIDs with an extension derived from the detected MIME type. Original names are sanitized and retained only as metadata. Images must decode successfully. Archive status removes media from public document eligibility. The media resource supports upload, metadata editing and archive actions.

Documents reference active managed PDF media. They have draft, published, unpublished and archived states, plus demo, verified and publishable verification states. Only publishable public documents with active media and a current publication timestamp appear in the public listing and detail routes. Public downloads resolve the managed storage path server-side, return an attachment with a sandbox Content Security Policy and `nosniff`, and return 404 for nonpublic documents. Editing a document resets it to draft and demo verification. Server-side managers and Stage 2 policies enforce create, update, verify and publish permissions and scopes; audit events contain only action and identifiers, not uploaded contents.

Phase A has focused upload, publication, and download tests. The fresh MySQL reconstruction and Stage 2 migration preservation gates passed on 5 October 2026 using disposable databases, both of which were removed. Administration Playwright workflows remain untested. Stage 3 remains NO-GO.


## Phase B/C implementation update (2026-10-05)

The existing Department model now carries a separate public profile with managed name, summary, description, responsibilities, order, publication and verification fields. Organizational department name/code/status remain separate. Department public profiles use the existing Department records, require the department publish and verify permissions, and are filtered from public listing, details and search unless active, approved and published.

Managed Services support name, slug, summary, description, requirements, steps, fee information, department association, display order and SEO metadata. Updates reset them to draft/demo. Public pages, home page and search consume only publishable published services.

News and notices use a shared editorial record with distinct types. Editors manage plain text body content, category, featured image, department, display order, expiry and associated managed documents. The public renderer outputs body as text, and only active, publishable, published entries appear. Expired notices are omitted from public listing, details and search. Documents associated with editorials are filtered through the public document scope.

The homepage now reads approved services, public department profiles, documents, news and notices from managed records. Empty states are rendered where there is no approved content. Static development/tourism preview sections were removed from that page. The shared layout supplies the single main landmark.

Verification since the previous entry: 58 backend tests passed with one skipped before editorial additions; focused service, department, document/media and editorial regression suites pass. PHPStan, Pint, TypeScript, ESLint, Vitest, production build and the existing six public Playwright cases pass as of 5 October 2026. The new media, document, service, department and editorial administration flows still lack Stage 3 administration E2E coverage. Full database reconstruction and Stage 2 preservation verification must be repeated after these migrations. Stage 3 remains NO-GO.


## Search architecture

Public search is implemented in the `/search` route with per-resource published scopes. It currently indexes CMS pages, documents, services, approved department profiles, news and notices. Queries are bounded, wildcard characters are escaped, each resource contributes at most 20 candidates, and the combined response is capped at 20. Search results contain resource type, title, summary, public URL and review marker. Enquiries and admin-only records are not queried. Wards and officials are indexed through their public eligibility scopes.

## Localization status

No translation tables, editor controls, translated public routes or locale-aware search are implemented. Existing content remains single-language. Translation fixtures and official translations have not been fabricated. A localized resource must preserve the same lifecycle and authorization rules as its source content before localization can be called complete.

## Enquiry notification status

Enquiry route, assignment and status changes dispatch generic workflow notifications after their state transaction commits. Messages omit resident contact details and message text; dispatch exceptions are reported without reverting committed state. Feature tests cover dispatch and failure isolation. Actual mail transport and queue-worker delivery, retry policy and operational alerting remain unverified. The current queue and cache drivers are configured as database.

## Security review status

Focused feature tests cover scoped CMS access, private document visibility, upload MIME rejection, publication gates, notice expiry, plain-text editorial rendering and search exclusions for private states. A full adversarial review has not been completed. Horizontal/vertical privilege escalation across every resource, direct-record and cross-department access, mass assignment, all lifecycle bypasses, upload size/path traversal matrix, stored XSS matrix, resident PII and audit leakage matrix, and administration HTTP authorization still require explicit tests. Do not treat the focused tests as Stage 3 security approval.

## Verification evidence (5 October 2026)

| Gate | Result | Evidence |
|---|---|---|
| Backend | PASS | 68 tests; 67 passed, 1 skipped, 284 assertions (latest full run before final editorial image wiring) |
| PHPStan | PASS | 0 errors |
| Pint | PASS | `vendor/bin/pint --test` |
| TypeScript | PASS | `npm run typecheck` |
| ESLint | PASS | `npm run lint` |
| Vitest | PASS | 14 tests |
| Production build | PASS | `npm run build` |
| Public Playwright | PASS | 6 tests; run on isolated port 18437 |
| Admin Playwright | NOT RUN | No Stage 3 admin E2E suite exists |
| Normal migrations | PASS | Latest editorial migration applied; `migrate:status` current |
| Fresh MySQL | PASS | Fresh reconstruction and repeated `SecuritySeeder` in `mutoko_stage3_verify_20261005`; empty content tables, 59 permissions |
| Stage 2 upgrade | PASS | Representative user, department, four roles, scope and audit event preserved in `mutoko_stage3_upgrade_20261005` |
| Seed idempotency | PASS | SecuritySeeder repeated twice after reconstruction and upgrade with preserved counts |
| Redis | PASS | `redis-cli ping` returned `PONG` |
| Cache/queue config | PASS | Both configured as database drivers |
| Diff whitespace | PASS | `git diff --check` |

Both disposable verification databases were dropped after the checks. The normal development database was only migrated forward.

## Current Stage 3 verdict

**NO-GO — STAGE 3 REMEDIATION REQUIRED.** Mandatory release blockers are Stage 3 administration Playwright coverage, localization requirements and implementation, a complete adversarial security review, integration verification of notification delivery, and council stakeholder approval of production content. Officials, wards, managed contacts and notification dispatch are implemented in code, but their workflows still need browser or operational acceptance where noted above. Stage 4 must not begin.


## Public editorial media display (2026-10-05)

News and notice list/detail routes now pass their managed featured image URL to the public pages. The URL is served only through the eligibility-checked managed media endpoint, which requires an active image attached to a currently public editorial item or official. Editorial feature tests, TypeScript and Pint passed after this change.
