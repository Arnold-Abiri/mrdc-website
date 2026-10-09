<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class SystemHealth extends FilamentPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $navigationLabel = 'System health';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Monitoring';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'System health';

    protected string $view = 'filament.pages.system-health';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('system-health.view');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return ['checks' => self::checks()];
    }

    /**
     * @return array<string, array{status: string, detail: string}>
     */
    public static function checks(): array
    {
        $checks = [];
        try {
            DB::select('select 1');
            $checks['database'] = ['status' => 'Healthy', 'detail' => 'Primary database reachable.'];
        } catch (Throwable $exception) {
            report($exception);
            $checks['database'] = ['status' => 'Unavailable', 'detail' => 'Primary database unreachable.'];
        }
        try {
            Redis::connection()->ping();
            $checks['redis'] = ['status' => 'Healthy', 'detail' => 'Cache/rate-limit store reachable.'];
        } catch (Throwable $exception) {
            report($exception);
            $checks['redis'] = ['status' => 'Unavailable', 'detail' => 'Cache/rate-limit store unreachable.'];
        }
        try {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            $checks['queue'] = ['status' => $failed > 50 ? 'Degraded' : 'Healthy', 'detail' => "{$pending} queued job(s), {$failed} failed job(s)."];
        } catch (Throwable $exception) {
            report($exception);
            $checks['queue'] = ['status' => 'Unavailable', 'detail' => 'Queue tables unreadable.'];
        }

        return $checks;
    }
}
