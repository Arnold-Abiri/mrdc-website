# Operations Checklist (ICT quick triage)

## Site down
Check: `/up`, web server process, PHP-FPM, DB reachable (`production:check`),
recent deploy. Escalate: hosting support. Ref: runbook + DR (server failure).

## Queue stopped
Check: supervisor status, worker processes, `queue:failed` count, Redis ping.
Restart: supervisor `reread/update`, `queue:restart`. Ref: runbook §workers.

## Mail not sending
Check: mailer config presence (no secret printing), queue depth/failed jobs,
SMTP credentials validity with provider, FROM address. Escalate: mail provider.

## Database unavailable
Check: MySQL process, credentials (inventory), disk space, connection count.
Do not `migrate:fresh`. Ref: DR (corruption/failure).

## Redis unavailable
Check: redis process, binding/firewall, memory. Public site degrades
(sessions/cache/queues); restore service, restart workers, record incident.

## Upload failing
Check: disk space, `storage/` permissions/link, `CMS_MAX_UPLOAD_KB`, file
type/size of the attempt, error events.

## Disk/storage issue
Check: `df`, log sizes, backup directory growth, livewire-tmp and cache
accumulation. Prune per retention; never delete production uploads blindly.

## Failed deployment
Check: release commit, migration batch, build artifacts, worker restart.
Decide: forward-fix vs rollback (runbook). Record incident.

## Backup failed
Check: disk space, mysqldump availability, DB credentials, manifest from last
good backup. Re-run manually; escalate if repeated.

## TLS/DNS issue
Check: certificate expiry, DNS propagation, APP_URL match, HSTS state.
Revert to last-known-good with registrar/host; record incident.
