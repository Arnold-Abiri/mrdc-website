# Security Release Review — Stage 8 (6 October 2026)

Method: representative release tests (`Stage8SecurityTest`, 7 tests), prior
stage evidence (S3-01–S3-09 fixed/retained, PP-05 backlog), fresh dependency
audits, configuration/secret review. No findings manufactured.

| ID | Severity | Area | Evidence | Status |
|---|---|---|---|---|
| R8-01 | — | Authorization/IDOR | Guest redirected from admin/reporting; disabled user denied panel; limited staff 403 on dashboard, audit report, export; scoped staff denied foreign-department tender view/update/award, allowed own — `Stage8SecurityTest` | No bypass found |
| R8-02 | — | Stored/reflected XSS | Hostile `<script>` in project description escaped on detail + search (`assertDontSee` raw); React escapes by default; plain-text-only validation retained; no `v-html`/raw HTML rendering introduced | No bypass found |
| R8-03 | — | CSRF | `web` group carries `PreventRequestForgery`; live 419 verified for `/locale` (e2e); contact/feedback/locale POSTs all in `web` | Protected |
| R8-04 | — | Mass assignment | Lifecycle/verification/owner fields rejected from create payloads (validator allowlist + DB defaults); tested for tenders + vacancies | Protected |
| R8-05 | — | Uploads | Prior S3-01 controls retained (MIME/extension consistency, PDF-only documents, generated paths); release suite green | Retained |
| R8-06 | — | Rate limiting/abuse | `throttle:5,1` on contact/feedback stores (6th request 429, tested); login throttling via Filament/Livewire limiter | Protected |
| R8-07 | — | Auth/session | Disabled users denied panel (`canAccessPanel`); MFA storage pre-existing; session encrypt + http_only defaults; secure cookies env-gated for HTTPS | No defect found |
| R8-08 | — | Dependencies | `composer audit`: no advisories. `npm audit --omit=dev`: 0 vulnerabilities. No major upgrades applied | Clean |
| R8-09 | Informational | Analytics surfaces | Reporting pages permission-tested; absent from search/sitemap; no PII recorded; export endpoints Gate-checked | No leak found |
| R8-10 | Informational | Error handling | Sanitized error events; correlation IDs without secrets; debug-off verified via `production:check` design | No exposure found |

**Critical unresolved: 0. High unresolved: 0. Medium unresolved: 0.
Known authorization bypass: No. Known exposed secret: No.**

Residual: exhaustive permutation matrix remains a pre-production note (PP-05);
representative coverage above is the release basis. Screen-reader pass is
PP-20 (no Critical/High a11y blocker found in structural checks).
