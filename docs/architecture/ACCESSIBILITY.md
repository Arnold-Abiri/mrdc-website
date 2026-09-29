# Stage 1 accessibility foundation verification

The public homepage and the Stage 1 coming-soon route were checked in the current repository. This is a foundation check, not a full WCAG conformance claim.

- Playwright covered 320, 360, 390, 768, 1024, 1280 and 1440 px without horizontal overflow.
- Browser keyboard checks on the 390 px viewport: Tab reaches the skip link first; its focus outline is visible; Enter moves focus to the main landmark. The menu opens with Enter, closes with Escape and returns focus to the trigger. The closed mobile navigation is hidden from focus.
- The homepage has one H1. Quick access now has an H2 before its H3 cards. Header, main, navigation and footer landmarks were checked. Images have alt attributes; the repeated footer mark and decorative feature images have empty alt text.
- Unimplemented destinations are genuine links to the informational route. Provisional phone/email and generic social icons are noninteractive, so there are no misleading call, mail or social actions. The menu button exposes `aria-expanded` and `aria-controls`.
- The reduced-motion media rule produces `scroll-behavior: auto` in Chromium. Focus styles are defined for links and buttons. The mobile menu target is at least 44 by 44 px.
- Representative text/background pairs were calculated using WCAG relative luminance: ink on page 15.3:1, muted on white 5.09:1, deep green on white 7.89:1, and preview notice text/background 8.78:1. Deep green now carries small green text and white action labels. This is a representative contrast check, not an exhaustive pixel-level audit.

No accessibility scanner dependency is installed, and none was added solely for this Stage 1 check. Automated evidence is the browser test in `tests/e2e/home.spec.ts`; source and computed-style inspection supplied the manual review. Before production, run a full WCAG 2.1 AA review with approved final text, imagery, translations and forms.
