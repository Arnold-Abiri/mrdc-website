# Stage 3 content inventory — 2 October 2026

This inventory records public content sources and approval state. “Development review” is not council verification.

| Content | Source | Status | Verification | Stakeholder Review Required |
|---|---|---|---|---|
| Theme 1 homepage introduction, values, service and tourism previews | Existing Stage 1 static website | Development review | Unverified | Yes |
| Homepage news and event fixtures | Former demo fixture | Removed from public display | Unverified | Yes, before any replacement publication |
| Homepage ward, project and growth point counts | Former static template | Removed from public display | No reliable source in repository | Yes |
| Public phone and email | Former static template | Removed from public display | No approved contact record | Yes |
| CMS pages | Stage 3 editor | No official seeded pages | Per-page demo, verified or publishable | Yes |
| Public enquiries | Visitor submissions | Private administrative data | Not public content | Operational review |
| Managed contacts, documents, departments, services, officials, wards, news and notices | Managed workflows implemented; no production records seeded | Empty | None | Yes |

The development-preview banner remains until the actual production content approval gate is satisfied.


Enquiry internal notes and assignments are operational records, not public content. No fictional staff member, department contact or production content was added during the routing increment.

## Managed document inventory

The Document Centre is empty until authorized editors upload and approve real council PDF files. No production document records or council facts are seeded. Development test documents exist only in the isolated test database and are clearly labelled as development content.


## Managed resources added

Services, Department public profiles, News, Notices and the Document Centre are empty until council editors enter and approve verified information. The application contains no production directory or article facts for these resources. Unit and feature test fixtures are isolated to test databases.


The current schema also includes standalone official and ward resources and managed public contacts. Their public pages remain empty until council content owners provide source material and approve it. Workflow implementation does not constitute content verification.

## Homepage managed-resource integration (5 October 2026)

The homepage now displays up to four approved managed contacts, up to three approved official profiles, and a count and link for approved ward profiles. It displays clear empty states when these sources have no eligible records. No contact value, official identity, or ward fact was seeded for this change. Council source material and approval remain outstanding.

## Localization and QA data (6 October 2026)

English remains the only populated interface language. Shona can be selected, but missing approved interface strings fall back to English. No council-authored Shona translations were generated or published. All names, contact addresses, ward text, departmental text, images and PDF content used in the new browser tests are explicitly QA fixtures in a disposable MySQL database; the suite cleans its managed uploads and reconstructs that database after execution. Council content approval remains an external production gate.
