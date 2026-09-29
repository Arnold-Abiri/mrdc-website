# Theme 1 component inventory

`PublicLayout` supplies the skip link, utility bar, development notice, provisional branding mark, primary navigation, mobile menu, main landmark and structured footer. The menu exposes `aria-expanded`, closes with Escape and returns focus to its trigger. Available destinations use real homepage anchors or the sitemap route. Sections without implementation lead to the Stage 1 `/coming-soon` information page. Demo phone, email and social icons are noninteractive.

`Home` composes the hero, quick access, council introduction, news/events previews, service and tourism previews, development presentation, feature callouts and CTA from `resources/js/Components/public/HomeSections.tsx`. The static data in `resources/js/fixtures/home.ts` is **DEMO / UNVERIFIED**. News/events have empty and populated states. These are presentation components, not live council publications or CMS modules.

The logo files in `public/images/` are **DEVELOPMENT BRANDING ASSETS — REPLACE BEFORE PRODUCTION**. No approval for an official crest is documented. Hero, feature and homepage images are also provisional. See [the development content policy](../content/DEVELOPMENT-CONTENT-POLICY.md) for the inventory and release gate.

Theme tokens and responsive styles are in `resources/css/app.css`. Final council identity, photography, contacts, statistics and editorial content remain pre-production dependencies. Stage 2 and later modules are not implemented.
