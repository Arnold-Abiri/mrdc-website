# Production Checklist (deployment day)

## Application
- [ ] Release commit approved and recorded
- [ ] `production:check` passes on the host
- [ ] `migrate --force` applied after backup
- [ ] `config:cache`, `route:cache`, `view:cache` built
- [ ] Frontend assets built, no localhost references
- [ ] Queue workers restarted and supervised
- [ ] Scheduler cron active (`schedule:list` verified)

## Infrastructure
- [ ] Document root is `public/`; project root not exposed
- [ ] `.env`, storage-private files, Git metadata, logs, backups not web-accessible
- [ ] HTTPS active with trusted CA certificate + renewal
- [ ] DNS records point at the verified host
- [ ] `APP_URL` is the final approved hostname
- [ ] MySQL user has least-privilege grants; no root credentials in app config
- [ ] Redis not publicly exposed; credentials from inventory
- [ ] SMTP/API credentials from inventory; FROM address/name set
- [ ] `OPS_ALERT_RECIPIENTS` approved and set
- [ ] External uptime monitor polling `/up` (interval/threshold/recipients)

## Security
- [ ] `APP_DEBUG=false`; error pages reveal nothing sensitive
- [ ] Security headers present (nosniff, referrer-policy, framing, permissions-policy)
- [ ] HSTS only if HTTPS is stable and correct
- [ ] `SESSION_SECURE_COOKIE=true`, http_only, SameSite=lax
- [ ] Dependency audits reviewed (composer/npm)

## Content
- [ ] Demo/unverified content removed or unpublished
- [ ] About, contacts, leadership, services, documents approved for launch set
- [ ] Rates/financial content approved (or sections show honest empty states)
- [ ] Placeholder images/contacts replaced or removed
- [ ] Privacy policy wording legally approved

## Localization
- [ ] English fallback verified on `/en`, `/sn`, `/nd`
- [ ] Translation-governance decision recorded (launch with fallback vs wait)

## Accessibility
- [ ] Critical journeys re-checked (home, services, documents, feedback, search)
- [ ] PP-20 screen-reader pass scheduled/completed per decision

## Monitoring
- [ ] M&E dashboard loads with live data
- [ ] Monthly report generates
- [ ] Error events flowing; alert path tested with safe transport

## Backups
- [ ] Pre-deployment backup verified (DB + files + manifest)
- [ ] Scheduled backups configured with retention and off-server copy
- [ ] Restore rehearsal recorded (see DR plan)

## Council approval
- [ ] Domain/DNS authorization
- [ ] Content approval
- [ ] Translation acceptance
- [ ] Alert recipients
- [ ] Legal (privacy) approval

## Post-deployment
- [ ] Smoke tests + `/up` green
- [ ] 60-minute error/queue watch
- [ ] Release recorded
