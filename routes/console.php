<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('analytics:prune')->weekly()->sundays()->at('02:30')->withoutOverlapping();
Schedule::command('queue:prune-failed --hours=720')->monthly();
Schedule::command('auth:clear-resets')->everyFifteenMinutes();
