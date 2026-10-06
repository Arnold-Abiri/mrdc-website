<?php

namespace App\Filament\Pages;

use App\Models\AuditEvent;
use BackedEnum;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class AuditReport extends FilamentPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Audit report';

    protected static ?string $title = 'Audit report';

    protected string $view = 'filament.pages.audit-report';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('audit.view');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $filters = [
            'from' => is_string(request()->query('from')) ? request()->query('from') : null,
            'to' => is_string(request()->query('to')) ? request()->query('to') : null,
            'actor' => is_string(request()->query('actor')) ? request()->query('actor') : null,
            'action' => is_string(request()->query('action')) ? request()->query('action') : null,
            'subject_type' => is_string(request()->query('subject_type')) ? request()->query('subject_type') : null,
        ];
        $events = $this->filtered($filters)->orderByDesc('created_at')->limit(500)->get();
        $actors = AuditEvent::query()->whereNotNull('actor_id')->select('actor_id')->distinct()->orderBy('actor_id')->pluck('actor_id');

        return ['filters' => $filters, 'events' => $events, 'actors' => $actors];
    }

    /** @param array<string, ?string> $filters */
    private function filtered(array $filters): Builder
    {
        return AuditEvent::query()
            ->when($filters['from'], fn ($query) => $query->where('created_at', '>=', $filters['from'].' 00:00:00'))
            ->when($filters['to'], fn ($query) => $query->where('created_at', '<=', $filters['to'].' 23:59:59'))
            ->when($filters['actor'], fn ($query) => $query->where('actor_id', $filters['actor']))
            ->when($filters['action'], function ($query) use ($filters): void {
                $escaped = addcslashes((string) $filters['action'], '%_\\');
                $query->where('action', 'like', '%'.$escaped.'%');
            })
            ->when($filters['subject_type'], function ($query) use ($filters): void {
                $escaped = addcslashes((string) $filters['subject_type'], '%_\\');
                $query->where('subject_type', 'like', '%'.$escaped.'%');
            });
    }
}
