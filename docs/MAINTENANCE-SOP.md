# Maintenance SOP

Support/escalation: **content issue** → authorized content staff; **user/access
issue** → ICT/system administrator; **application defect** → software support;
**infrastructure outage** → hosting support; **security incident** → incident
procedure (DR plan). No vendor contacts invented — fill in real ones at handover.

## Daily (ICT or delegated operator)
- Open the public homepage; confirm it renders.
- Check `/up` responds successfully.
- Review new error events (resolve handled ones with a factual note).
- Check failed jobs; retry or escalate genuine failures.
- Confirm no unexpected downtime alert arrived overnight.

## Weekly
- Queue/worker health (depth, failures, restarts) and scheduler heartbeat
  (`schedule:list`; last-run evidence from logs).
- Backup status: latest `backup-*` directory has database dump + files archive
  + manifest, within the last 24h; storage/disk usage trend sane.
- Security/error review: new error classes, repeated 500s, throttle spikes.
- Content sanity: expired notices withdrawn automatically (spot-check one).

## Monthly
- Generate the Monthly performance report; file it with release/operations notes.
- Content freshness: unpublished drafts aging, closed tenders/vacancies marked,
  outdated officials flagged to owners.
- Dependency/security review: `composer audit`, `npm audit --omit=dev`;
  assess (do not blindly major-upgrade production).
- Backup restoration sampling where appropriate (scratch environment only).
- User access review where appropriate (leavers, dormant accounts, scope changes).

## Quarterly / periodic
- Full restore rehearsal (disposable environment; record evidence).
- Role/access review with department owners.
- Content governance review (ownership, accuracy, translation progress).
- Translation review via Translation status page.
- Dependency updates in a tested release (never direct on production).
- Security review of admin users, audit exports, and header posture.
