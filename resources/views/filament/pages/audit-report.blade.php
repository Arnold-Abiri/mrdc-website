<x-filament-panels::page>
    <div class="space-y-6">
        <header class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.4)] dark:border-slate-800 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
            <div class="flex flex-col gap-4 border-b border-slate-200/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-[0_12px_24px_-12px_rgba(15,23,42,0.8)] dark:bg-slate-100 dark:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-2.18 7.372c-.886.13-1.782.203-2.695.215a48.42 48.42 0 0 1-5.714-.336A2.25 2.25 0 0 1 2.25 19.8V6.9a2.25 2.25 0 0 1 2.011-2.238 48.374 48.374 0 0 1 11.478 0A2.25 2.25 0 0 1 17.75 6.9v3.42M19.5 10.5v3.75m0 3.75h.008v.008H19.5v-.008Zm0-3.75h.008v.008H19.5v-.008Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Audit trail</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-[-0.04em] text-slate-900 dark:text-white">System activity and change log</h2>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]" aria-hidden="true"></span>
                    Redacted at write time
                </div>
            </div>

            <div class="px-5 py-4">
                <form method="get" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6 items-end">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">From</label>
                        <input type="date" name="from" value="{{ $filters['from'] }}" class="w-full h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">To</label>
                        <input type="date" name="to" value="{{ $filters['to'] }}" class="w-full h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Actor</label>
                        <select name="actor" class="w-full h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <option value="">All</option>
                            @foreach ($actors as $actor)
                                <option value="{{ $actor }}" @selected($filters['actor'] == $actor)>{{ $actor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Action</label>
                        <input type="search" name="action" placeholder="Action contains" value="{{ $filters['action'] }}" maxlength="120" class="w-full h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Entity</label>
                        <input type="search" name="subject_type" placeholder="Entity contains" value="{{ $filters['subject_type'] }}" maxlength="120" class="w-full h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 h-9 inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                            <span>Filter</span>
                        </button>

                        @can('audit.export')
                            <a href="{{ route('admin.audit-export', request()->query()) }}" class="h-9 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                <span>Export CSV</span>
                            </a>
                        @endcan
                    </div>
                </form>
            </div>
        </header>

        {{-- Audit Events Table --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Audit Event Logs</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        Showing {{ $events->firstItem() ?? 0 }}&ndash;{{ $events->lastItem() ?? 0 }} of {{ number_format($events->total()) }} recorded events
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    Page {{ $events->currentPage() }} of {{ $events->lastPage() }}
                </span>
            </div>

            <div class="max-h-[620px] overflow-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead class="sticky top-0 z-10">
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                            <th class="px-5 py-3" scope="col">Timestamp</th>
                            <th class="px-4 py-3" scope="col">Actor</th>
                            <th class="px-4 py-3" scope="col">Action</th>
                            <th class="px-4 py-3" scope="col">Target Entity</th>
                            <th class="px-5 py-3 text-right" scope="col">Entity ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($events as $event)
                            <tr class="transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                                <td class="px-5 py-3 font-mono text-xs text-slate-600 dark:text-slate-400 tabular-nums">
                                    {{ $event->created_at }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($event->actor_id)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            User #{{ $event->actor_id }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">System / CLI</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionLower = strtolower($event->action);
                                        $colorClasses = match(true) {
                                            str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy') => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
                                            str_contains($actionLower, 'create') || str_contains($actionLower, 'store') || str_contains($actionLower, 'publish') => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                                            str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') => 'bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
                                            str_contains($actionLower, 'login') || str_contains($actionLower, 'auth') => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300',
                                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colorClasses }}">
                                        {{ $event->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-700 dark:text-slate-300">
                                    {{ class_basename($event->subject_type ?? 'None') }}
                                </td>
                                <td class="px-5 py-3 text-right font-mono text-xs tabular-nums text-slate-600 dark:text-slate-400">
                                    {{ $event->subject_id ?? '&mdash;' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400" colspan="5">
                                    No audit events match the specified filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($events->hasPages())
                <div class="border-t border-slate-200 bg-white px-5 py-3 dark:border-slate-800 dark:bg-slate-950">
                    {{ $events->links() }}
                </div>
            @endif

            <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-3 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-400">
                Paginated event stream. For full data extraction across the entire historical record, use the <strong>Export</strong> feature above.
            </div>
        </section>
    </div>
</x-filament-panels::page>
