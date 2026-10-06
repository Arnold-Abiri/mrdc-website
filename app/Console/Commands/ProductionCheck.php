<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class ProductionCheck extends Command
{
    protected $signature = 'production:check';

    protected $description = 'Report non-secret production readiness signals (read-only, never mutates)';

    public function handle(): int
    {
        $failures = 0;
        $check = function (string $label, bool $ok, string $hint = '') use (&$failures): void {
            $ok ? $this->info("PASS {$label}") : $this->error("FAIL {$label}".($hint !== '' ? " ({$hint})" : ''));
            $failures += $ok ? 0 : 1;
        };

        $check('APP_ENV is production', config('app.env') === 'production', 'current: '.config('app.env'));
        $check('APP_DEBUG is disabled', config('app.debug') === false);
        $appUrl = (string) config('app.url');
        $check('APP_URL is an https URL', str_starts_with($appUrl, 'https://'), 'current: '.$appUrl);
        $check('APP_KEY is set', config('app.key') !== null && config('app.key') !== '');
        $check('SESSION_SECURE_COOKIE is enabled', (bool) config('session.secure') === true);
        try {
            DB::select('select 1');
            $check('database reachable', true);
        } catch (Throwable $exception) {
            $check('database reachable', false, 'connection failed');
        }
        try {
            Redis::connection()->ping();
            $check('redis reachable', true);
        } catch (Throwable $exception) {
            $check('redis reachable', false, 'connection failed');
        }
        $check('storage is writable', is_writable(storage_path()) && is_writable(storage_path('logs')));
        $check('queue connection configured', (string) config('queue.default') !== 'sync' || config('app.env') !== 'production');
        $check('mail mailer configured', (string) config('mail.default') !== '');
        $check('alert recipients configured', is_array(config('ops.alert_recipients')) && config('ops.alert_recipients') !== []);
        $check('analytics retention sane', (int) config('ops.analytics_retention_days', 0) >= 30);

        if ($failures > 0) {
            $this->warn("{$failures} production check(s) failing.");

            return self::FAILURE;
        }
        $this->info('All production checks passed.');

        return self::SUCCESS;
    }
}
