# Cybersecurity Training (all seven; ICT notes marked [ICT])

Relates to the actual Mutoko RDC system — not generic theory.

1. **Passwords**: unique, strong, memorized or in an approved manager; never
   reuse council passwords elsewhere; never share accounts — every action in
   `/admin` is attributed to you in the audit trail.
2. **Phishing**: verify unexpected links/attachments, especially those
   referencing invoices, tenders, or vacancies; report to ICT before clicking
   when unsure. [ICT: check headers, quarantine, review audit events.]
3. **Sessions**: lock workstations; always log out of `/admin` on shared
   machines; sessions expire and are server-invalidated on logout.
4. **Accounts**: leavers and role changes must reach ICT same-day for
   disabling/re-scoping; disabled accounts cannot access the panel at all.
5. **Least privilege**: request only the permissions your function needs
   (see `docs/ROLE-PERMISSION-MATRIX.md`).
6. **Uploads**: only genuine council PDFs/images from trusted sources; the
   system rejects executables and mismatched files — do not attempt to bypass
   rejections; report them instead.
7. **Personal information**: enquiries/complaints are private; never publish
   them, never paste them into public CMS fields, never email them outside
   approved channels.
8. **MFA**: where enabled, protect authenticator/recovery codes like passwords;
   never store them in the CMS or logs.
9. **Incident reporting**: suspected compromise, disclosure, or malware →
   notify ICT immediately (who/when/what, preserve evidence); ICT follows the
   compromised-credentials procedure in `docs/DISASTER-RECOVERY.md`.
