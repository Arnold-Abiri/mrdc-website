<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditExportController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        Gate::authorize('audit.export');
        $filters = [
            'from' => is_string(request()->query('from')) ? request()->query('from') : null,
            'to' => is_string(request()->query('to')) ? request()->query('to') : null,
            'actor' => is_string(request()->query('actor')) ? request()->query('actor') : null,
            'action' => is_string(request()->query('action')) ? request()->query('action') : null,
            'subject_type' => is_string(request()->query('subject_type')) ? request()->query('subject_type') : null,
        ];
        $filename = 'audit-report-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($filters): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['timestamp', 'actor_id', 'action', 'subject_type', 'subject_id']);
            AuditEvent::query()
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
                })
                ->orderBy('created_at')->chunk(500, function ($events) use ($handle): void {
                    foreach ($events as $event) {
                        fputcsv($handle, [(string) $event->created_at, (string) $event->actor_id, $event->action, $event->subject_type, (string) $event->subject_id]);
                    }
                });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
