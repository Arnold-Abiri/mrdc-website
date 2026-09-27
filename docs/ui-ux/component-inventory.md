# Theme 1 component inventory

`PublicLayout` supplies the skip link, utility bar, text identity, primary navigation, mobile menu, main landmark and structured footer. The menu exposes `aria-expanded`, closes with Escape and returns focus to its trigger. Future destinations remain noninteractive until routes and approved content exist. The identity mark is text until an approved council logo is supplied.

`Home` composes `Hero`, `QuickAccess`, `NewsAndEvents` and `FeatureCallouts` from `resources/js/Components/public/HomeSections.tsx`. News and event previews have empty and populated states; cards support missing images and long titles. Development fixture arrays and types live in `resources/js/fixtures/home.ts` and must be replaced by approved CMS data in a later stage.

`public/images/hero-development.webp` and the three `feature-*-development.webp` cards are generated **development images**, not verified photographs of Mutoko or council activity. Visible captions identify this status. Replace all four with approved local photography before production. No official crest, contact details, events, statistics or news have been fabricated.

Theme tokens and responsive styles are centralized in `resources/css/app.css`. Screenshots from the Stage 1 review are in `artifacts/theme-1/`.
