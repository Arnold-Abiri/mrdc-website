<?php

namespace App\Filament\Pages;

use App\Domain\Identity\AuditWriter;
use App\Filament\Clusters\Reports\ReportsCluster;
use App\Models\AuditEvent;
use App\Services\Mail\MailConfigurationService;
use App\Services\Mail\SmtpDiagnosticsService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Throwable;

class EmailOperations extends FilamentPage
{
    protected static ?string $cluster = ReportsCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $navigationLabel = 'Email operations';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Monitoring';

    protected static ?int $navigationSort = 7;

    protected static ?string $title = 'Email Operations & Queue Health';

    protected string $view = 'filament.pages.email-operations';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('mail_operations.view');
    }

    public function retryJob(string $uuid): void
    {
        if (! auth()->user()?->can('mail_operations.retry')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        try {
            Artisan::call('queue:retry', ['id' => [$uuid]]);

            app(AuditWriter::class)->record(auth()->user(), 'queue.job_retried', auth()->user(), [
                'job_uuid' => $uuid,
            ]);

            Notification::make()
                ->title('Job Dispatched for Retry')
                ->body("Queued job {$uuid} was released back to the worker queue.")
                ->success()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Retry Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function retryAllFailed(): void
    {
        if (! auth()->user()?->can('mail_operations.retry')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        try {
            Artisan::call('queue:retry', ['id' => ['all']]);

            app(AuditWriter::class)->record(auth()->user(), 'queue.all_retried', auth()->user(), [
                'action' => 'Retried all failed jobs in queue.',
            ]);

            Notification::make()
                ->title('All Failed Jobs Queued for Retry')
                ->success()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Retry All Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function forgetJob(string $uuid): void
    {
        if (! auth()->user()?->can('mail_operations.retry')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        try {
            Artisan::call('queue:forget', ['id' => $uuid]);

            app(AuditWriter::class)->record(auth()->user(), 'queue.job_deleted', auth()->user(), [
                'job_uuid' => $uuid,
            ]);

            Notification::make()
                ->title('Job Deleted')
                ->body("Failed job {$uuid} removed from failed records.")
                ->info()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Delete Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $activeConfig = app(MailConfigurationService::class)->getActiveConfiguration();

        // Queue metrics
        $pendingJobsCount = 0;
        $failedJobsCount = 0;
        $failedJobsList = [];

        try {
            $pendingJobsCount = DB::table('jobs')->count();
            $failedJobsCount = DB::table('failed_jobs')->count();
            $failedJobsList = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(25)
                ->get()
                ->map(function ($job) {
                    $payload = json_decode((string) $job->payload, true);
                    $displayName = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown Job');

                    // Extract safe exception summary (first line of exception)
                    $firstLine = strtok((string) $job->exception, "\n");
                    $cleanException = app(SmtpDiagnosticsService::class)->sanitizeError((string) $firstLine);

                    return [
                        'id' => $job->id,
                        'uuid' => $job->uuid,
                        'queue' => $job->queue,
                        'name' => class_basename($displayName),
                        'failed_at' => $job->failed_at,
                        'exception_summary' => $cleanException,
                    ];
                })
                ->all();
        } catch (Throwable) {
            // Queue tables might be unavailable
        }

        // Recent email and queue audit events
        $recentEmailAudits = AuditEvent::query()
            ->whereIn('action', [
                'enquiries.citizen_notified',
                'mail.test_dispatched',
                'mail.settings_saved',
                'mail.activated',
                'mail.reverted_to_env',
                'queue.job_retried',
                'queue.all_retried',
            ])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return [
            'activeConfig' => $activeConfig,
            'pendingJobsCount' => $pendingJobsCount,
            'failedJobsCount' => $failedJobsCount,
            'failedJobsList' => $failedJobsList,
            'recentEmailAudits' => $recentEmailAudits,
        ];
    }
}
