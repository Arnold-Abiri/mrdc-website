# Role / Permission Matrix (actual application state)

Roles (seeded): **System Administrator** (96 permissions — all), **Website
Administrator** (`admin.access`, `departments.view`), **Department Content
Owner** (`admin.access`, `departments.view`), **Auditor** (`admin.access`,
`audit.view`, `audit.export`, `analytics.view`).

## Permission families (`<area>.view/create/update/publish/verify`)
pages, media (view/create/update), documents, services, editorial,
wards, officials, contacts, tenders (+`tenders.award`), vacancies,
investment, projects, meetings, slides, statistics; enquiries
(view/update/route/assign); users (view/create/update/disable/assign_roles);
roles (view/create/update/assign_permissions); departments (view/create/
update/disable/publish/verify); `audit.view`, `audit.export`,
`analytics.view`, `analytics.export`, `system-health.view`.

## Scopes
`user_role_scopes`: **global** (all departments — administrators, auditors)
or **department** (content owners, department staff — own department only).
Cross-department access is denied by policy and covered by release tests.

## Training-group mapping (no invented roles)
- ICT → System Administrator (2 staff) — full operations.
- HR (3) → Department Content Owner pattern + enquiry rights per assignment —
  no technical administration.
- Finance (2) → scoped document/tender/award rights per assignment.
- Auditors → Auditor role (read + export, no content mutation).
