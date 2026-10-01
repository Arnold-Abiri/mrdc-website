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

## Outstanding Stage 3 work

Media, documents, institutional seeded content, department public profiles, officials, wards, services, news, notices, contact details, enquiries, search, translations, homepage data integration, full adversarial testing, Playwright CMS workflows, disposable MySQL fresh and forward-upgrade verification, and stakeholder content review are outstanding. Static Stage 1 content remains classified as demo/unverified under the development content policy.
