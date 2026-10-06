# Handover Checklist

Mark only with evidence. External items stay open until proven otherwise.

## Source code
- [ ] Repository access transferred (read/write per role)
- [ ] Release commit recorded: `011c601` (+ Stage 9 docs commit when made)

## Hosting
- [ ] Server access ownership confirmed
- [ ] Domain/DNS ownership confirmed
- [ ] TLS issuance + renewal confirmed

## Application
- [ ] Environment inventory sealed (no secrets in repo)
- [ ] Database created, least-privilege user, migrations applied
- [ ] Redis reachable, private; queue workers supervised
- [ ] Scheduler cron active; SMTP/API credentials installed

## Operations
- [ ] External uptime monitor polling `/up`
- [ ] Alert recipients configured and test-safe verified
- [ ] Logs + rotation + aggregation confirmed
- [ ] Scheduled backups verified (DB + files + manifest + off-server copy)
- [ ] DR plan read and understood by ICT

## Content
- [ ] Council-approved content set (pages, leadership, contacts, services)
- [ ] Financial information + rates approved or honest empty states
- [ ] Translations acceptance decision recorded

## Documentation
- [ ] Admin guide, quick references, maintenance SOP, runbook, DR, security
    review, training materials — location communicated to owners

## Training
- [ ] ICT / HR / Finance sessions delivered (see attendance records)
- [ ] Assessments completed; feedback collected
