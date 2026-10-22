# Mutoko RDC PHP 8.3 staging deployment manifest

This manifest describes a staging-only release candidate. It is not deployment approval. Do not point it at the live website, live database, or production media store.

## Package contents

Build the package from the release candidate commit and include:

- Laravel application source: `app/`, `bootstrap/`, `config/`, `database/migrations/`, `resources/`, `routes/`, and required root Laravel files.
- `composer.json` and `composer.lock`; install runtime dependencies with `composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction`.
- `public/index.php`, `public/.htaccess`, council public images, and the complete generated `public/build/` directory (Vite manifest, CSS, JavaScript, and emitted images).
- Empty writable directory structure for `storage/framework/{cache,sessions,views}`, `storage/logs`, `storage/app/private`, and `bootstrap/cache`.
- This manifest and the application license/readme files.

The current Vite build succeeds. `public/build/` is generated and Git-ignored, so a source checkout alone does not contain it: the release package assembler must copy the just-built directory into the package. The three previously reported image paths exist in `public/images/`: `about/dashboard-banner.webp`, `home/news-water.webp`, and `home/dev-background.webp`.

Exclude `.env` files, `database/seeders/` (deployment does not run seeders), database dumps or SQLite files, production credentials, user uploads, `storage/logs`, `node_modules/`, test results, development-only Composer packages, and Git metadata. Do not include the working copy's `vendor/`; construct the package's `vendor/` from the lock file with `--no-dev`. The tracked `.env.example` contains placeholders only and must never be copied as the staging `.env` without replacing all settings.

Keep all application source outside the web root. Configure the staging virtual host's document root to the package's `public/` directory only. Do not expose the repository root, `.env`, `vendor/`, `storage/`, or `bootstrap/` over HTTP. Enable Apache `mod_rewrite`, verify `.htaccess` is honored, and enforce directory listing denial at the vhost level as well.

## Staging environment variables

Set these outside version control. Values containing credentials or keys must be staging-specific secrets.

**Required application and database settings**

- `APP_NAME`, `APP_ENV=production`, `APP_KEY`, `APP_DEBUG=false`, `APP_URL` (HTTPS staging URL).
- `APP_LOCALE`, `APP_FALLBACK_LOCALE`.
- `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. `DB_URL` may be used instead of discrete connection values if the host requires it.
- `LOG_CHANNEL`, `LOG_LEVEL`.

**Required state, storage, and mail settings**

- `CACHE_STORE=file`, `SESSION_DRIVER=file`, `SESSION_LIFETIME`, `SESSION_ENCRYPT`, `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`, and `SESSION_DOMAIN` as appropriate for the staging hostname.
- `FILESYSTEM_DISK=local`, `CMS_MEDIA_DISK=local`, `CMS_MAX_UPLOAD_KB`.
- `QUEUE_CONNECTION=database` if queued email/notifications are enabled; set `DB_QUEUE`, `DB_QUEUE_RETRY_AFTER`, and `DB_QUEUE_TABLE` only when overriding Laravel defaults.
- For SMTP delivery: `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_SCHEME`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME`.

**Optional operational settings**

- `APP_MAINTENANCE_DRIVER=file`.
- `OPS_ALERT_RECIPIENTS` (approved staging recipients only) and `ANALYTICS_RETENTION_DAYS`.
- Redis settings only if staging chooses Redis for cache, sessions, or queues: `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PORT`, `REDIS_USERNAME`, `REDIS_PASSWORD`, and applicable database/connection settings.

Never place live database credentials, production mail credentials, production `APP_KEY`, or production alert recipients in the artifact or repository.

## Runtime and hosting requirements

- PHP CLI and web SAPI 8.3; MySQL PDO (`pdo_mysql`) for the configured database. Locally, `composer check-platform-reqs --no-dev` passes on PHP 8.3.33. Composer's required extensions are `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `intl`, `json`, `libxml`, `openssl`, `pcre`, `session`, `tokenizer`, `xmlreader`, and `zip`; `ctype` and `mbstring` are satisfied locally by Symfony polyfills. Confirm the web SAPI has the same required functionality and MySQL driver.
- `storage/` and `bootstrap/cache/` must be writable by the PHP account. On cPanel, apply the least permissive owner/group permissions that work; do not make the application tree world-writable. Private uploads live under local application storage and are served through authorized application routes; a `public/storage` symlink is not required for the configured private media disk.
- Configure cron to run `php /path/outside/public/artisan schedule:run` every minute. The app schedules analytics retention weekly, failed-job pruning monthly, and password-reset cleanup every 15 minutes.
- If using the database queue, configure a persistent worker. Where cPanel cannot keep a daemon alive, arrange a reviewed cron worker strategy such as `queue:work database --stop-when-empty --tries=3`; verify job latency and timeout behavior in staging.
- The optional `backup:create` command invokes `mysqldump`, `gzip`, and `tar` through Symfony Process and calls `shell_exec`. Confirm those binaries/functions are available before relying on that command; it is not a web request requirement.

## Database change review and safe staging sequence

- No schema migration is introduced by this release candidate. The existing `2026_10_11_152900_add_logo_setting_to_site_settings_table` migration was made insert-only (`insertOrIgnore`) so an existing council logo setting is preserved if the row already exists.
- Existing migrations have rollback methods that drop columns or remove rows. Do not run rollback commands against staging or production as part of this release. Review the target database's pending migration list and take a staging backup before applying pending `up()` migrations.
- On staging only, use the ordinary forward-only `php artisan migrate --force` after confirming the database connection and pending migrations. Do not run `migrate:fresh`, `db:wipe`, production seeders, or any seed command during deployment. This repository's local verification migrations and seeders used only isolated in-memory or `/tmp` SQLite databases; no production database or content was accessed or modified.

## Checks required on the actual cPanel staging account

Local checks cannot prove the cPanel web SAPI's extensions, disabled functions, cron behavior, PHP-FPM user, Apache modules, HTTPS proxy headers, email transport, MySQL version/permissions, quotas, or document-root configuration. Before staging acceptance, verify these on that account, including upload limits, storage/cache writability, queue execution, scheduled commands, SMTP delivery, and that requests cannot read files outside `public/`.
