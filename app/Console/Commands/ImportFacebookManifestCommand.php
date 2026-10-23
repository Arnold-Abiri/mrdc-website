<?php

namespace App\Console\Commands;

use Database\Seeders\FacebookContentSeeder;
use Illuminate\Console\Command;

class ImportFacebookManifestCommand extends Command
{
    protected $signature = 'mrdc:import-facebook {--dry-run : Report without writing} {--apply : Create missing drafts} {--include-statistics : Include inactive, unverified statistics (requires --apply)}';

    protected $description = 'Review or apply the Facebook content manifest without publishing';

    public function handle(FacebookContentSeeder $importer): int
    {
        if ($this->option('include-statistics') && ! $this->option('apply')) {
            $this->error('--include-statistics requires --apply.');

            return self::FAILURE;
        }
        if ($this->option('apply') === $this->option('dry-run')) {
            $this->error('Specify exactly one of --dry-run or --apply.');

            return self::FAILURE;
        }
        if ($this->option('apply') && app()->environment('production') && ! $this->confirm('Apply unverified draft content to production?')) {
            return self::FAILURE;
        }
        try {
            foreach ($importer->import((bool) $this->option('apply'), (bool) $this->option('include-statistics')) as $group => $counts) {
                $this->line("{$group}: create {$counts['create']}, skip {$counts['skip']}");
            }
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
