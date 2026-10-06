# Disaster Recovery Plan (Operational)

RPO/RTO below are **proposed — require council/hosting approval** (PP backlog).
They assume daily automated backups and the runbook procedure.

- Proposed RPO: 24 hours (daily backup cadence).
- Proposed RTO: 4 business hours (restore + verify + cutover).

## Scenarios

### Server failure
1. Provision replacement host per runbook (web server, PHP, MySQL client).
2. Restore latest backup (database + files) to the new host.
3. Set environment from the sealed production inventory (never from Git).
4. Run migrations, caches, worker restart, smoke tests, health check.
5. Repoint DNS/TLS after verification.

### Database corruption
1. Stop writes (maintenance mode: `php artisan down`).
2. Restore `database.sql.gz` into a scratch database; verify table count and
   `migrate:status` (all Ran); spot-check users/documents/permissions.
3. Swap application to the verified database; `php artisan up`; monitor.

### Accidental deletion (content or files)
1. Identify scope from audit events (`audit-report`).
2. Restore only the affected rows/files from backup into scratch; re-apply via
   administration (preferred) or targeted SQL/file copy.
3. Never restore a full backup over production to fix a partial deletion
   without a fresh pre-restore backup.

### Failed deployment
Follow the runbook rollback section (code rollback; forward-fix preferred;
database restore only with a fresh backup and explicit approval).

### Compromised credentials
1. Rotate the affected secret at its source (SMTP, DB user, APP_KEY only as a
   last resort — rotation invalidates sessions/encrypted data).
2. Update the sealed inventory; redeploy environment; restart workers.
3. Review audit report for misuse in the exposure window.

### Lost uploaded files
Restore `files.tar.gz` from the latest backup into scratch; verify document/
media references resolve; copy into place; clear relevant caches.

### Redis/queue unavailable
Application degrades (sessions/cache/queues) but public pages served from
database remain available. Restore Redis service, restart workers
(`queue:restart`, supervisor), drain and inspect failed jobs
(`queue:failed`). Record an incident for the monthly report.

### DNS/TLS incident
Revert to the last known-good DNS/TLS state with the registrar/host;
verify `APP_URL`, canonical, and hreflang; keep HSTS off until HTTPS is
stable and correctly configured.

## Restore rehearsal record
6 Oct 2026 (local): `backup:create` produced `database.sql.gz` (45 tables) +
`files.tar.gz` (62 files) + manifest; restored into a disposable database
(45 tables, `migrate:status` 24 Ran, boot + counts verified); files
extracted and enumerated; disposable resources destroyed afterwards.
