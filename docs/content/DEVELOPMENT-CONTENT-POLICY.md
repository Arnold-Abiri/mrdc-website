# Development content policy

Stage 1 uses static representative content to demonstrate page layout while council content and CMS modules are pending. Every item below is **DEMO / UNVERIFIED**. No current item is classified **VERIFIED / APPROVED** because no source approval has been recorded. The public shell displays a development-preview notice. Demo contact and social details are not actionable links.

| Content | Location | Classification |
|---|---|---|
| Wards, projects, growth points and other homepage figures | `HomeSections.tsx` | DEMO / UNVERIFIED |
| Dated news and events | `resources/js/fixtures/home.ts` | DEMO / UNVERIFIED |
| Service, tourism, council and investment presentation copy | `HomeSections.tsx`, `home.ts` | DEMO / UNVERIFIED |
| Phone, email, locality detail and social channel icons | `PublicLayout.tsx` | DEMO / UNVERIFIED |
| Hero, feature and homepage images | `public/images/` | DEMO / UNVERIFIED |
| Logo files `logo.png` and `logo@2x.png` | `public/images/` | DEVELOPMENT BRANDING ASSET — REPLACE BEFORE PRODUCTION |

The logo has no independently documented council approval and must not be described as the official crest. Development images must not be presented as verified documentary photographs. No legal, financial or statutory claim may be invented for a demo. Do not use demo credentials, sensitive personal information or realistic security details.

Before production, a designated council content owner must provide source material and approve each claim, contact, social account, image rights, logo and editorial item. The release checklist must record its source, reviewer, approval date and replacement in the application. Remove any demo item lacking approval. The publication gate fails if any **DEMO / UNVERIFIED** item remains visible as production information. The Stage 1 shell also emits a noindex directive; remove that directive only after all content approval checks pass.
