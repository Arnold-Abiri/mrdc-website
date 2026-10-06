<?php

namespace App\Console\Commands;

use App\Models\AnalyticsEvent;
use Illuminate\Console\Command;

class PruneAnalyticsEvents extends Command
{
    protected $signature = 'analytics:prune {--days= : Retention days (defaults to ops.analytics_retention_days)}';

    protected $description = 'Delete raw analytics events older than the retention window (audit logs are never touched)';

    public function handle(): int
    {
        $days = $this->option('days') !== null ? (int) $this->option('days') : (int) config('ops.analytics_retention_days', 365);
        if ($days < 30) {
            $this->error('Retention must be at least 30 days.');

            return self::FAILURE;
        }
        $cutoff = now()->subDays($days);
        $deleted = AnalyticsEvent::query()->where('created_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deleted} analytics event(s) older than {$cutoff->toDateString()}.");

        return self::SUCCESS;
    }
}
