# ICT Quick Reference (safe commands; DANGER marked)

```text
php artisan up                                  # also: /up endpoint
php artisan production:check                   # read-only readiness signals
php artisan queue:monitor                      # depth/failures (or jobs table)
php artisan queue:restart                       # after every deploy
php artisan queue:failed                        # inspect; retry/forget deliberately
php artisan schedule:list                       # confirm jobs
php artisan backup:create --path=/var/backups/mrdc
php artisan analytics:prune
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan migrate --force                     # after backup, never fresh
php artisan storage:link
php artisan tinker                              # inspect only, no mass writes
tail -f storage/logs/laravel.log
```

DANGER (explicit approval + backup first):
```text
php artisan migrate:fresh          # NEVER on production
php artisan migrate:rollback       # forward-fix preferred
php artisan db:wipe / tinker writes
rm -rf storage/app/*               # never blindly
```

No passwords are stored here or printed by these commands.
