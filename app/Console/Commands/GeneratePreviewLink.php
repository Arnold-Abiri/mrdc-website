<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

/**
 * Generates signed stakeholder-preview URLs (valid 7 days).
 *
 * Share privately with council reviewers only. The preview displays
 * draft/demo content, is noindexed, and is never in the sitemap.
 */
class GeneratePreviewLink extends Command
{
    protected $signature = 'preview:link {locale=en : Preview locale (en, sn, nd)}';

    protected $description = 'Generate a signed stakeholder preview URL';

    public function handle(): int
    {
        $locale = $this->argument('locale');
        if (! in_array($locale, ['en', 'sn', 'nd'], true)) {
            $this->error('Locale must be one of: en, sn, nd.');

            return self::FAILURE;
        }

        $url = URL::temporarySignedRoute('preview.home', now()->addDays(7), ['locale' => $locale]);
        $this->line($url);
        $this->comment('Valid for 7 days. Share privately with council reviewers only.');

        return self::SUCCESS;
    }
}
