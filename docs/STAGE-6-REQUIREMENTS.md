# Stage 6 Requirements Traceability

| ID | Requirement | State | Implementation | Evidence | Status |
|---|---|---|---|---|---|
| TOR-I18N-LANG | en/sn/nd supported, English fallback, no fabricated translations | EXISTING — EXTEND | `nd` added to URL allowlist, session, `localization.ts` (empty `ndebele` map → English), `normalizeLocale` | `SetPublicLocale`, `localization.ts`, `PublicLocaleTest`, vitest | Done |
| TOR-I18N-SELECT | Accessible header selector preserving the page | EXISTING — EXTEND | Labeled native select (keyboard/screen-reader/mobile, no flags, current identifiable); POST swaps the locale segment (`/en/services` → `/sn/services`) | `PublicLayout`, `/locale` handler, stage3-public spec | Done |
| TOR-I18N-PREF | Predictable preference, correct `<html lang>` | EXISTING — EXTEND | Session persists; URL prefix wins on prefixed pages; blade `lang` from app locale | Middleware, specs | Done |
| TOR-I18N-URL | `/en`, `/sn`, `/nd` without duplicating logic | MISSING → built | All public routes in `{locale}` groups; single closure set; admin untouched (`/admin` never prefixed) | `routes/web.php`, `public_route()` helper | Done |
| TOR-I18N-LEGACY | Unprefixed compatibility, no duplicate indexing | MISSING → built | `/` → session/default locale; known public paths 302 to `/{locale}/…` (session-aware); canonical always the prefixed URL | `root`, `legacy-redirect` routes, specs | Done |
| TOR-I18N-CONTENT | Translation tables, no duplicated records | MISSING → built | Polymorphic `content_translations` (sn/nd only) + `HasTranslations` on 10 models; IDs/dates/references/files/audit never translated | Migration, trait, `Stage6LocalizationTest` | Done |
| TOR-I18N-SCOPE | Translatable descriptive fields (7 priority + tender/vacancy/document/meeting) | MISSING → built | `TRANSLATABLE_FIELDS` per model; public rendering overlays with English fallback via `toLocalizedArray()` | Models, routes | Done |
| TOR-I18N-ADMIN | Manage en/sn/nd without raw JSON; visible source/missing state | MISSING → built | Collapsible Translations section in 10 resources; `HandlesTranslations` on 31 pages; helper text marks fallback | `TranslationFields`, resources | Done |
| TOR-I18N-COMPLETE | Completeness from required public fields | MISSING → built | `translationCompleteness()` (complete/partial/missing + counts); `TranslationStatus` admin page per entity × locale | Manager, Filament page | Done |
| TOR-I18N-PUB | Publish without blocking on translations; safe fallback; no raw keys | MISSING → built | Publication independent of translations; `translated()` falls back; keys never rendered | Trait, tests | Done |
| TOR-I18N-IFACE | Interface strings localized (nav/buttons/forms/empty states) | EXISTING — EXTEND | Audited all 30 public pages; converted remnants (~60 keys added); council content untouched | `localization.ts`, pages | Done |
| TOR-SEO-HREFLANG | Alternate-language metadata + x-default | MISSING → built | Blade emits en/sn/nd + x-default alternates on prefixed pages | `app.blade.php`, stage6 spec | Done |
| TOR-SEO-CANON | One canonical per page | EXISTING — COMPLETE | Canonical is the current (prefixed) URL; legacy redirects prevent duplicates | blade, redirects | Done |
| TOR-SEO-SITEMAP | Full public sitemap, no private records | EXISTING — EXTEND | All Stage 3–5 entities × 3 locales; no admin/drafts/enquiries | `sitemap.xml`, test | Done |
| TOR-SEO-ROBOTS | Allow public, exclude admin | EXISTING — COMPLETE | `Allow: /`, `Disallow: /admin` | `public/robots.txt` | Done |
| TOR-SEO-META | Organization/NewsArticle/JobPosting + Open Graph | MISSING → built | `SeoHead` (OG + JSON-LD, managed-image reuse, no demo hard-coding) on Home/EditorialDetail/Vacancy | `Seo.tsx` | Done |
| TOR-A11Y-KBD | Keyboard operation, no traps | EXISTING — EXTEND | Native controls, ESC menu close, carousel buttons keyboard-verified | Specs, manual check | Done |
| TOR-A11Y-FOCUS | Visible focus | EXISTING — COMPLETE | `:focus-visible` outline (retained + high-contrast variant) | CSS, manual check | Done |
| TOR-A11Y-SKIP | Skip to main content | EXISTING — COMPLETE | Skip link + `<main id="main" tabindex="-1">`, focus-visible verified | Layout, spec | Done |
| TOR-A11Y-ALT | Alt text required where meaningful | EXISTING — EXTEND | Media alt-missing badge in admin; featured/official images carry meaningful alt (title/name), never filenames | `MediaResource`, pages | Done |
| TOR-A11Y-FORMS | Labels, required identification, understandable errors | EXISTING — EXTEND | Associated labels; server error summary `role="alert"` on Contact/Feedback via shared `errors` prop | Pages, spec | Done |
| TOR-A11Y-CONTRAST | AA foreground/background | Audited | No white-on-`#0B8F62` text; muted `#607080` = 5.09:1; buttons 7.89:1; no token changes required | Computed audit | Done |
| TOR-A11Y-HC | High-contrast display option | MISSING → built | Toggle + persistence, token overrides, works across surfaces | `AccessibilitySettings`, CSS | Done |
| TOR-A11Y-FONT | Font resize (decrease/reset/increase) | MISSING → built | rem-scalable steps 87.5–125%, persisted, layouts verified at 390/1280 | Component, specs | Done |
| TOR-A11Y-MOTION | Respect reduced motion; accessible carousel | MISSING → built | Media query kills non-essential animation; manual carousel (no auto-rotation) with prev/next + status + live region | CSS, Hero, manual check | Done |
| TOR-A11Y-RESP | 390px + increased text usability | Verified | Overflow assertion in specs (fixed desktop nav wrap); settings persistence re-checked after reload | Specs, CSS fix | Done |

External council dependencies (NOT blockers): approved Shona/Ndebele interface wording
and content translations; content review of translated fields.
