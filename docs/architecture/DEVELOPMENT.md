# Development

Requirements: PHP 8.4+, Composer, Node 24+, npm, MySQL 8, Redis. Copy `.env.example` to `.env`, set local credentials, generate `APP_KEY`, and set `APP_ENV=local`, `APP_DEBUG=true`, `SESSION_SECURE_COOKIE=false` for local HTTP. The example configuration targets production services; local SQLite may be used for isolated tests only. Run `composer install`, `npm ci`, `php artisan migrate`, `npm run dev` and `php artisan serve`.

Checks: `php artisan test --compact`, `./vendor/bin/phpstan analyse --no-progress`, `./vendor/bin/pint --test`, `npm run typecheck`, `npm run test`, `npm run build`, `npm run e2e`. Playwright requires browser installation through `npx playwright install chromium`.

The default admin gate denies all users. A trusted operator must provision a real user and grant `is_admin` through a controlled one-time administration procedure. Never seed a default admin password. Keep this grant restricted until Stage 2 RBAC replaces it.
