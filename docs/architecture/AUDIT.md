# Security audit trail

`audit_events` stores actor, action, subject type and identifier, safe JSON metadata and creation time. `AuditWriter` recursively drops metadata keys containing password, token, secret, session, cookie or hash. Callers should pass only necessary metadata and never raw requests. User creation, update, self-profile changes, lifecycle, role assignment/removal, role permission changes, department lifecycle, login, logout, password changes, MFA secret and recovery-code changes, and bootstrap actions produce events.

Audit rows have no update/delete UI and `AuditEventPolicy` denies create, update and delete. The Audit Filament resource exposes list/detail only to an active user with a global `audit.view` grant. Database administrators and direct SQL remain outside the application's immutability boundary. Agree retention and backup controls before production.
