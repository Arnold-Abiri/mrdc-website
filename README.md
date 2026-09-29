# Mutoko Rural District Council Website

Stage 1 production foundation for a civic website and future content administration portal. The current public pages use provisional demonstration content; they are not approved council publications.

## Architecture and stack

One Laravel 13 application owns routes, authentication, authorization and persistence. React 19 and TypeScript render public pages through Inertia 3. Filament 5 provides the interim admin panel. MySQL 8 is the target database, Redis supports cache, sessions and queues, Tailwind 4 supplies styling, and Vite 8 builds the frontend. No separate Node backend is deployed.

## Local setup

Use PHP 8.4+, Composer, Node 24+, MySQL 8 and Redis. Run `composer install`, `npm ci`, copy `.env.example` to `.env`, set local database and Redis credentials, then run `php artisan key:generate`. For local HTTP, set `APP_ENV=local`, `APP_DEBUG=true` and `SESSION_SECURE_COOKIE=false`. Run `php artisan migrate`, `npm run build`, and `php artisan serve`. Use `npm run dev` while editing frontend code. Never commit `.env`.

The default seeder creates no users. Admin access requires an explicitly provisioned user with the interim `is_admin` grant. Do not use this interim flag as final RBAC.

## Verification

Run `php artisan test --compact`, `./vendor/bin/phpstan analyse --no-progress`, `./vendor/bin/pint --test`, `npm run typecheck`, `npm run lint`, `npm run test`, `npm run build` and `npm run e2e`. Use a disposable MySQL database for `php artisan migrate:fresh --seed` and explicitly set MySQL environment variables for backend tests. Playwright needs Chromium; `PLAYWRIGHT_CHROMIUM_EXECUTABLE` can point to an installed executable.

## Stage 1 status and documentation

Stage 1 includes a responsive Theme 1 shell, Inertia homepage, interim Filament admin boundary and verification tooling. Organisation, final RBAC and CMS business modules belong to later stages. The [development content policy](docs/content/DEVELOPMENT-CONTENT-POLICY.md) inventories provisional data and defines the production replacement gate. See `docs/architecture/` for architecture, development, security and deployment guidance, and `docs/ui-ux/` for the Theme 1 standard and component inventory.
