# Data scopes

`user_role_scopes` holds one grant per user and role. `global` grants all records for permissions carried by that role; `department` requires a valid department ID; `own` refers to the actor's user ID. The MySQL CHECK constraint rejects mismatched scope and department combinations, and `UserManager` validates the same rule before writing. Role assignment and scope storage share a transaction.

`DataScopeAuthorizer::allows()` checks a direct record; `apply()` builds the matching scoped query. Only roles carrying the requested permission contribute scopes. Department and user Filament resources use the query filter, while their policies check direct records. Out-of-scope direct URLs return 404 through the scoped query. Create actions requiring no existing record require global scope. Future business modules should pass their department and owner columns to this service, then call the same `allows()` rule in policies. No CMS-specific scope is implemented here.
