# Security baseline

Laravel's web middleware provides sessions and request forgery protection. Filament applies authentication, and `User::canAccessPanel` rejects users without the explicit admin grant. Passwords use Laravel's hashed cast. The public web middleware sets content-type, frame, referrer and permissions headers. Blade and React escape ordinary output. Avoid raw HTML for unpublished content.

Production must set `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL` to the approved HTTPS host, `SESSION_SECURE_COOKIE=true`, and secrets in environment management. Uploaded files remain private unless a later module explicitly publishes validated files. Stage 1 does not provide an upload endpoint. Do not log credentials or tokens. Backups, MFA, detailed RBAC and audit workflows need Stage 2 infrastructure and approval.

## Stage 1 verification

`tests/Feature/FoundationTest.php` exercises guest, ordinary-user and explicit interim-admin panel access. It also drives Filament's Livewire login: six wrong-password attempts reach the five-attempt rate limit, successful login changes the session ID, and logout invalidates the session and restores the guest redirect. Public HTTP responses are checked for content type, frame, referrer and permissions headers. A forced 500 response under `APP_DEBUG=false` is checked for absence of its exception marker. The default database seeder creates no account.

These checks do not establish production HTTPS, secure-cookie delivery, infrastructure hardening, or final RBAC. Those remain deployment and later-stage gates.
