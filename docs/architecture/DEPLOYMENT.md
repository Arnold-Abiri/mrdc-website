# Deployment foundation

Deploy as one Laravel application with PHP 8.4+, MySQL 8 and Redis. Set a persistent `APP_KEY`, configure HTTPS, point the web root at `public`, run `composer install --no-dev --optimize-autoloader`, `npm ci`, `npm run build`, `php artisan migrate --force`, and cache configuration/routes/views after settings are final. Keep `APP_DEBUG=false`. Supervise `php artisan queue:work redis --tries=3 --backoff=30` and cron `* * * * * php /path/to/artisan schedule:run`. Configure daily encrypted database and file backups with restore testing. `/up` is Laravel's liveness endpoint; infrastructure-specific readiness checks must be added before production launch.

Production migrations must be verified against a provisioned MySQL 8 database before release. Redis must be reachable from workers and web processes. No automated production deployment is configured in Stage 1.
