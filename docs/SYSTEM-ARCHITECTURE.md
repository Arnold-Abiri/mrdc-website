# System Architecture (production view)

```text
Browser (HTTPS)
   ↓
Web Server → Laravel public/
   ↓
Laravel + Inertia ──→ React public frontend (built assets)
   ↓                      Filament administration (/admin)
Laravel
 ├── MySQL 8 (content, users, audit, analytics, jobs)
 ├── Redis (cache, sessions, queues, rate limiting)
 ├── Queue Workers (supervised; notifications, mail)
 ├── Scheduler (single cron → schedule:run)
 ├── Mail (SMTP/API in production; log/array only for tests)
 └── Managed Storage (storage/app: media, documents, backups excluded)
```

## Major modules
- **CMS & content**: Pages (+revisions), Media, Documents (+versions,
  downloads), Departments, Officials, Wards, Services, Editorial (news/
  notices), Homepage slides, Public contacts, District statistics.
- **Citizen services**: Tenders (+awards, compliance), Vacancies, Projects
  (+updates), Investment, Tourism page, Rates page, Contact/Feedback enquiries
  (workflow, routing, assignment, notes, notifications).
- **Governance**: Council meetings (+agendas/minutes), Transparency
  (finance categories + year filters).
- **Platform**: multilingual URLs + content translations (EN source, SN/ND
  fallback), RBAC (roles + global/department scopes), audit events, analytics
  events, error events, incidents, M&E/audit/health/monthly reporting.
- **HTTP layer**: locale-prefixed public controllers (`App\Http\Controllers\Public\*`),
  Filament admin, security-header + locale + analytics middleware; closure-free
  routes so `route:cache` works.
