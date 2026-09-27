# Architecture

The application is one Laravel 13 deployment. Public routes return Inertia pages rendered by React and TypeScript. Filament provides the `/admin` panel. Laravel owns authentication, authorization, validation, persistence and publication decisions. MySQL 8 is the production database; Redis serves cache, sessions and queues. Domain code should be grouped under `app/Domain` when a real feature is added; Stage 1 does not create empty modules.

The public shell is `resources/js/Layouts/PublicLayout.tsx`; route pages are under `resources/js/Pages`. Theme tokens are centralized in `resources/css/app.css`. No public business content is seeded.
