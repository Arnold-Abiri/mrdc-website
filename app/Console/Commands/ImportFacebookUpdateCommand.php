<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportFacebookUpdateCommand extends Command
{
    protected $signature = 'mrdc:import-fb-update';

    protected $description = 'Retired unsafe Facebook update importer';

    public function handle(): int
    {
        $this->error('This command is retired. Review the manifest and use mrdc:import-facebook --dry-run, then --apply.');

        return self::FAILURE;
    }
}
