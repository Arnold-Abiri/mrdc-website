# Stage 3 adversarial security review — 6 October 2026

This review records attacks that were actually executed. It is not a claim that every input and Filament action has been exhaustively assessed.

| ID | Severity | Area | Attack and evidence | Result | Remediation |
|---|---|---|---|---|---|
| S3-01 | Medium, fixed | Media uploads | A valid PNG named with a PHP extension was previously accepted because MIME alone determined storage type. `Stage3AdversarialTest` now attempts executable, HTML, SVG, spoofed MIME, double extension, oversized, malformed image, and extension mismatch uploads. | Hostile content rejected; generated UUID path and private delivery verified | Match the final client extension to detected MIME in `MediaManager` |
| S3-02 | Medium, fixed | Department administration | Authenticated browser creation with an empty public display order caused MySQL 500 because the form submitted null to a non-null column. | Browser create/publication now passes | Set form default zero and require the value |
| S3-03 | Low, fixed | Enquiry administration | After routing, department and assignee were not shown in the record view. | Reopened browser view shows both | Explicitly resolve the related names in the Filament infolist |
| S3-04 | Low, fixed | Framing | Public HTML lacked a CSP `frame-ancestors` directive although `X-Frame-Options` was set. | Header test passes | Add `frame-ancestors 'self'` where no route-specific CSP exists |
| S3-05 | Informational | Direct access | Guest admin route, private draft page/document slugs, and unattached media ID requested directly. | Login redirect or 404 | Existing server authorization and publication scopes retained |
| S3-06 | Informational | Role access | Disabled administrator login and a limited panel user attempted ten direct Filament resource URLs in Playwright; a department-scoped enquiry user attempted another department’s enquiry URL. | Login denied; ten resource URLs return 403 and cross-department enquiry returns 404 | Existing policy checks retained |
| S3-07 | Informational | Search | XSS, SQL-like text, literal wildcard, 101-character query, and malformed percent-encoded input sent to `/search`. | No script rendering or 500; excessive query returns 422 | Existing bounded search retained |
| S3-08 | Informational | CSRF | POST `/locale` without a token from Playwright's HTTP client. | 419 | Laravel web CSRF protection retained |
| S3-09 | Informational | Notification privacy | Safe transport test processes a queued enquiry message and verifies recipient; existing fake test checks resident email/message are absent. | Pass | No resident PII added to payload |

**Confirmed Critical findings remaining: 0. Confirmed High findings remaining: 0.** The review is not exhaustive enough to certify that no unknown Critical or High issue exists. A resource-by-resource IDOR, mass-assignment, stored-XSS, enquiry-abuse, and hidden Filament-action attack matrix remains a technical release gate.

Production controls require `APP_DEBUG=false`, HTTPS, `SESSION_SECURE_COOKIE=true`, managed application secrets, and operational review of SMTP and queue workers. These settings were inspected in configuration, not verified on a deployed host. The route-specific sandbox CSP on protected downloads is preserved by the security-header middleware.

## Resource attack matrix — current execution status

`Open` means the attack was not fully executed for that resource. Existing feature tests are cited only where they exercise the attack.

| Resource | IDOR/scope | Parameter injection | Stored XSS | Hidden/lifecycle actions | Result |
|---|---|---|---|---|---|
| Pages | Draft 404 and ordinary publish denial | Slug/CTA validation; protected fields open | Payload variants open | Restore backend passes; browser open | Partial |
| Page revisions | Cross-page guard exists; browser open | Revision ID manipulation open | Snapshot rendering open | Restore resets draft and audits; browser open | Partial |
| Media | Unattached ID 404 | Generated path/MIME tested | Title/alt payload open | Metadata/archive/direct access open | Partial |
| Documents | Draft 404 | Media relation validated; replacement open | Title/description open | Replacement/archive open | Partial |
| Departments | Limited-role URL 403 | Public profile fields open | Profile text open | Direct publish action open | Partial |
| Officials | Limited-role URL 403 | Photo/ownership open | Biography/title open | Direct action open | Partial |
| Wards | Limited-role URL 403 | Protected fields open | Description open | Direct action open | Partial |
| Services | Limited-role URL 403 | Protected fields open | Summary/steps open | Direct action open | Partial |
| News | Limited-role URL 403 | Publication fields open | Body/URL open | Direct action open | Partial |
| Notices | Limited-role URL 403 | Expiry/state open | Body open | Boundary/direct action open | Partial |
| Contacts | Limited-role URL 403 | Publication fields open | Office/value open | Direct action open | Partial |
| Enquiries | Department 404 and own-scope ID tested | Public status/assignee ignored | Subject/message variants open | Route/assign/status manager tested; browser open | Partial |
| Enquiry notes | Cross-department create denied | Author/owner open | Admin rendering open | Direct note action open | Partial |
| Assignment/routing | Cross-department destination denied | Invalid assignee manager check; request injection open | Not applicable | Reassignment browser/audit open | Partial |
| Search | Draft excluded; bounded query tested | SQL-like/wildcard tested | Script string safe; SVG/encoded open | Not applicable | Partial |
| Public forms | Throttling/CSRF/basic validation tested | Status/assignee ignored | Hostile variants open | Enumeration 404 tested | Partial |
| Admin actions | Guest/disabled/limited access tested | Livewire parameter injection open | Admin HTML rendering open | Direct invocation matrix open | Partial |

No new confirmed vulnerability was found in this pass. S3-01 (Medium), S3-02 (Medium), S3-03 (Low), and S3-04 (Low) remain fixed with existing regression evidence. S3-05 through S3-09 are informational observations, not proof that open cells are safe. Confirmed unresolved Critical: **0**. Confirmed unresolved High: **0**. Mandatory matrix coverage remains incomplete: **NO-GO**.
