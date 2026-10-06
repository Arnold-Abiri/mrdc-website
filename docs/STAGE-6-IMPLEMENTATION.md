# Stage 6 Implementation — Multilingual, Accessibility & Discoverability

## Multilingual architecture

- **Locales**: `en` (authoritative fallback), `sn`, `nd` — allowlisted in routing,
  middleware, session, and validation. `PublicLocale` extended; empty `ndebele`
  map falls back to English. No machine-generated council wording anywhere.
- **URLs**: every public route lives in a `{locale}` group (`en|sn|nd`); admin
  stays unprefixed. One closure set (no duplicated logic); closures accept the
  positional `string $locale` first parameter (Laravel injects route params
  positionally). `route()` calls use the `public_route()` helper
  (`app/Support/helpers.php`, composer-autoloaded so test app refreshes cannot
  redeclare it).
- **Compatibility**: `/` redirects to the session/default locale; legacy
  unprefixed public paths 302 (session-aware) to `/{locale}/…`; unknown
  prefixes 404. Canonical is always the prefixed URL — no duplicate indexing.
- **Preference**: session persists; the language selector POSTs to `/locale`
  and redirects to the same page under the new locale (`/en/services` →
  `/sn/services`), verified end-to-end (en→sn→nd→en).
- **Content translations**: `content_translations` (morph, sn/nd only) +
  `HasTranslations::translated()` (English fallback, never raw keys) +
  `toLocalizedArray()` used by all public routes with `with('translations')`
  eager loading on lists. Translatable: pages, services, editorial, departments,
  projects, investment, tenders, vacancies, documents, meetings (descriptive
  fields only — IDs/dates/references/amounts/files/audit untouched).
- **Management**: `TranslationFields` collapsible section in 10 Filament
  resources; `HandlesTranslations` trait on 31 admin pages (hook-based,
  collision-checked); `TranslationManager::saveTranslations()` permission-gated
  (record `update` right), locale/field allowlisted, empties delete, audited.
- **Completeness**: per-record `translationCompleteness()` and a
  `TranslationStatus` admin page (totals + complete/partial per entity × locale).
- **Interface**: all 30 public pages audited; ~90 keys in `localization.ts`;
  remaining hardcoded text is council-managed content (by design).

## Accessibility (WCAG 2.1 AA target, Theme 1 retained)

- Skip link + `<main id="main" tabindex="-1">`; `:focus-visible` outlines.
- Keyboard-operable carousel (prev/next buttons, `aria-label`s, `role="status"`
  counter, `aria-live` region, no auto-rotation) — verified with real slides.
- `AccessibilitySettings`: font steps 87.5–125% (rem-scalable) + high-contrast
  toggle, both persisted in localStorage and restored; restrained utility-bar
  control (no floating widget).
- `prefers-reduced-motion` disables smooth scroll/transitions/animations.
- Associated form labels; required identification; server error summary
  (`role="alert"`) on Contact/Feedback via newly shared `errors` prop.
- Media library: alt-missing badge for images in Filament; meaningful public
  images carry real alt (title/name), decorative stay empty; filenames never
  used as alt.
- Contrast computed: muted 5.09:1, buttons 7.89:1, navy surfaces ≥6.4:1 — no
  token changes required. Desktop nav wraps at 1280px (overflow fix, covered
  by the no-overflow assertions).

## SEO / discoverability

- Canonical (current prefixed URL) + en/sn/nd + x-default hreflang in blade.
- Sitemap: 17 index routes + all published entities × 3 locales; excludes
  admin/drafts/enquiries/complaints/private.
- `robots.txt`: `Allow: /`, `Disallow: /admin`.
- `SeoHead`: OG (title/description/URL/image/site) + JSON-LD —
  GovernmentOrganization (home), NewsArticle/Article (editorial), JobPosting
  only while open (vacancies). Managed images reused; no demo hard-coding.

## Tests

- `Stage6LocalizationTest` (6): translation render/fallback, completeness,
  locale+field rejection, unauthorized denial, canonical/hreflang, sitemap
  locales, cross-locale draft exclusion.
- `PublicLocaleTest` rewritten for URL-wins semantics (prefix drives language,
  root/legacy resolution, 404 on bad prefix).
- `stage6-public.spec.ts` (5): skip link/landmarks/h1, selector options,
  settings persistence, form labels + error alert, canonical/hreflang.
- Manual checks performed: keyboard carousel cycle, contrast math, nav overflow
  fix, reduced-motion CSS, mobile 390px overflow assertions in-suite.

## Performance / security

- Eager-loaded translations on lists; bounded per-record lazy loads on details.
- Locales strictly allowlisted (`en|sn|nd`) in constraints, middleware,
  validation, and `normalizeLocale`; translation editing permission-gated;
  localized routes reuse publication scopes and policies unchanged.

## Deferred

- Stage 7: M&E dashboard, monitoring, deployment, training.
- Pre-production: council Shona/Ndebele approval (interface + content),
  full manual screen-reader pass (see backlog), production indexation review.
