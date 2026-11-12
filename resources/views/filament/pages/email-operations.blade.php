<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Hero Header --}}
        <header class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
            <div class="flex flex-col gap-4 border-b border-slate-200/80 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-start gap-3.5">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-[0_12px_24px_-12px_rgba(15,23,42,0.8)] dark:bg-slate-100 dark:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">System operations</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-[-0.04em] text-slate-900 dark:text-white">Email & queue worker status</h2>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                    <span class="h-2.5 w-2.5 rounded-full {{ ($activeConfig?->mail_enabled ?? true) ? 'bg-emerald-500' : 'bg-amber-500' }}" aria-hidden="true"></span>
                    <span>Transport: <strong>{{ ($activeConfig?->mail_enabled ?? true) ? 'Active' : 'Disabled' }}</strong></span>
                </div>
            </div>

            <p class="px-5 py-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                Monitor email delivery, queue backlog, and retry or discard failed background jobs.
            </p>
        </header>

        {{-- Overview Metric Cards --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Mail and Queue Metrics">
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Mail Transport</span>
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold {{ ($activeConfig?->mail_enabled ?? true) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' }}">
                        {{ ($activeConfig?->mail_enabled ?? true) ? 'Operational' : 'Disabled' }}
                    </span>
                </div>
                <p class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    @if ($activeConfig)
                        {{ $activeConfig->mail_enabled ? 'Active (Database)' : 'Disabled' }}
                    @else
                        Active (Environment)
                    @endif
                </p>
                <p class="mt-1 font-mono text-xs text-slate-500 dark:text-slate-400 truncate">
                    Host: {{ $activeConfig ? $activeConfig->host.':'.$activeConfig->port : config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port') }}
                </p>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Pending Queue Jobs</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Queued</span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white tabular-nums">
                    {{ number_format($pendingJobsCount) }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Jobs waiting to run</p>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Failed Queue Jobs</span>
                    @if ($failedJobsCount > 0)
                        <button
                            type="button"
                            wire:click="retryAllFailed"
                            wire:confirm="Are you sure you want to retry all failed jobs?"
                            class="inline-flex items-center gap-1 rounded-xl bg-rose-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-500">
                            Retry All
                        </button>
                    @else
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Clean</span>
                    @endif
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight tabular-nums {{ $failedJobsCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    {{ number_format($failedJobsCount) }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Failed job entries</p>
            </div>
        </section>

        {{-- Failed Jobs Section --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Failed Jobs</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Unsuccessful background tasks and their exception messages.
                        </p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        {{ count($failedJobsList) }} job(s)
                    </span>
                </div>
            </div>

            @if (count($failedJobsList) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                                <th class="px-5 py-3">Job Name</th>
                                <th class="px-4 py-3">Queue</th>
                                <th class="px-4 py-3">Failed At</th>
                                <th class="px-4 py-3">Exception Summary</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($failedJobsList as $job)
                                <tr class="transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                                    <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">
                                        {{ $job['name'] }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-600 dark:text-slate-400">
                                        {{ $job['queue'] }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400 tabular-nums">
                                        {{ $job['failed_at'] }}
                                    </td>
                                    <td class="px-4 py-3 max-w-md font-mono text-xs text-rose-600 dark:text-rose-400 truncate">
                                        {{ $job['exception_summary'] }}
                                    </td>
                                    <td class="px-5 py-3 text-right space-x-2">
                                        <button
                                            type="button"
                                            wire:click="retryJob('{{ $job['uuid'] }}')"
                                            class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20">
                                            Retry
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="forgetJob('{{ $job['uuid'] }}')"
                                            class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-medium text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 dark:hover:text-rose-400">
                                            Forget
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6">
                    <div class="flex items-center gap-3 rounded-2xl bg-emerald-50/70 p-4 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
                        <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>No failed queue jobs found. All background tasks have completed successfully.</span>
                    </div>
                </div>
            @endif
        </section>

        {{-- Email Activity Audit Log Section --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Email Activity</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    Recent notifications, test emails, and mail configuration events.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                            <th class="px-5 py-3">Timestamp</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Actor</th>
                            <th class="px-5 py-3">Metadata Preview</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($recentEmailAudits as $audit)
                            <tr class="transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                                <td class="px-5 py-3 font-mono text-xs text-slate-500 dark:text-slate-400 tabular-nums">
                                    {{ $audit->created_at?->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 font-mono text-xs font-semibold text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                        {{ $audit->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
                                    {{ $audit->actor_id ? "User #{$audit->actor_id}" : 'System Worker' }}
                                </td>
                                <td class="px-5 py-3 font-mono text-xs text-slate-500 dark:text-slate-400 truncate max-w-lg">
                                    {{ json_encode($audit->metadata) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400">
                                    No recent email audit events found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($recentEmailAudits->hasPages())
                <div class="border-t border-slate-200 bg-white px-5 py-3 dark:border-slate-800 dark:bg-slate-950">
                    {{ $recentEmailAudits->links() }}
                </div>
            @endif
        </section>

    </div>
</x-filament-panels::page>
