<x-filament-panels::page>
    <div class="space-y-6">
        <header class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.4)] dark:border-slate-800 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
            <div class="flex flex-col gap-4 border-b border-slate-200/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-[0_12px_24px_-12px_rgba(15,23,42,0.8)] dark:bg-slate-100 dark:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-11.25 0h8.001m-8.001 0c-.034-.018-.065-.038-.097-.059a18.5 18.5 0 0 1-3.633-3.248A18.5 18.5 0 0 1 3.146 12m12.537 9c.069-.018.13-.038.193-.059a18.5 18.5 0 0 0 3.486-3.248A18.5 18.5 0 0 0 20.855 12M3.146 12a18.5 18.5 0 0 1 2.375-5.693A18.5 18.5 0 0 1 9.154 3.06M20.855 12a18.5 18.5 0 0 0-2.375-5.693A18.5 18.5 0 0 0 14.846 3.06M3 12h18" /></svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Translation quality</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-[-0.04em] text-slate-900 dark:text-white">English remains the source of truth</h2>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]" aria-hidden="true"></span>
                    Council-approved wording only
                </div>
            </div>

            <p class="px-5 py-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                Shona and Ndebele fall back to English wherever a translation is missing. Select a content type below to open its records and fill the gaps.
            </p>
        </header>

        @if ($totals['records'] > 0 && $totals['sn_pct'] === 100 && $totals['nd_pct'] === 100)
            <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200" role="status">
                All {{ number_format($totals['records']) }} records are fully translated into Shona and Ndebele. New or edited English content will reappear here as a gap.
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-3" aria-label="Translation totals">
            <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Records</p>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ count($rows) }} types</span>
                </div>
                <p class="mt-4 text-4xl font-semibold tracking-[-0.06em] text-slate-900 dark:text-white">{{ number_format($totals['records']) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Translatable records in scope</p>
            </div>

            <div class="rounded-[24px] border border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-white p-4 shadow-[0_18px_45px_-28px_rgba(16,185,129,0.35)] dark:border-emerald-500/20 dark:from-emerald-500/5 dark:via-slate-950 dark:to-slate-950">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Shona</p>
                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $totals['sn_pct'] }}%</span>
                </div>
                <p class="mt-4 text-4xl font-semibold tracking-[-0.06em] text-slate-900 dark:text-white">{{ number_format($totals['sn_complete']) }}</p>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-emerald-100 dark:bg-slate-800" role="progressbar" aria-label="Shona fully translated" aria-valuenow="{{ $totals['sn_pct'] }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400" style="width: {{ $totals['sn_pct'] }}%"></div>
                </div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Fully translated</p>
            </div>

            <div class="rounded-[24px] border border-sky-100 bg-gradient-to-br from-sky-50 via-white to-white p-4 shadow-[0_18px_45px_-28px_rgba(14,165,233,0.35)] dark:border-sky-500/20 dark:from-sky-500/5 dark:via-slate-950 dark:to-slate-950">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Ndebele</p>
                    <span class="rounded-full bg-sky-100 px-2 py-1 text-[10px] font-bold text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">{{ $totals['nd_pct'] }}%</span>
                </div>
                <p class="mt-4 text-4xl font-semibold tracking-[-0.06em] text-slate-900 dark:text-white">{{ number_format($totals['nd_complete']) }}</p>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-sky-100 dark:bg-slate-800" role="progressbar" aria-label="Ndebele fully translated" aria-valuenow="{{ $totals['nd_pct'] }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-sky-400" style="width: {{ $totals['nd_pct'] }}%"></div>
                </div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Fully translated</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(260px,0.7fr)]">
            <div class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.38)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Coverage by content type</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Complete, partial, and missing translations</p>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-slate-500 dark:text-slate-400" aria-hidden="true">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Complete</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span> Partial</span>
                    </div>
                </div>

                <div class="max-h-[560px] overflow-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="sticky top-0 z-10">
                            <tr class="border-b border-slate-200 bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                                <th class="px-4 py-3" scope="col">Content</th>
                                <th class="px-3 py-3 text-right" scope="col">Records</th>
                                <th class="px-3 py-3" scope="col">Shona</th>
                                <th class="px-3 py-3" scope="col">Ndebele</th>
                                <th class="px-4 py-3 text-right" scope="col"><span class="sr-only">Action</span><span aria-hidden="true">Health</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($rows as $row)
                                <tr class="transition-colors duration-200 hover:bg-slate-50 dark:hover:bg-slate-900/40">
                                    <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                                        @if ($row['url'])
                                            <a href="{{ $row['url'] }}" class="underline-offset-4 hover:underline">{{ $row['label'] }}</a>
                                        @else
                                            {{ $row['label'] }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right tabular-nums text-slate-600 dark:text-slate-300">{{ number_format($row['total']) }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-2.5 w-28 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800" role="progressbar" aria-label="{{ $row['label'] }} Shona coverage" aria-valuenow="{{ $row['sn_complete_pct'] }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400" style="width: {{ $row['sn_complete_pct'] }}%"></div>
                                                <div class="h-full bg-amber-400" style="width: {{ $row['sn_partial_pct'] }}%"></div>
                                            </div>
                                            <span class="text-[11px] tabular-nums text-slate-500 dark:text-slate-400">{{ $row['sn_complete'] }}/{{ $row['total'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-2.5 w-28 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800" role="progressbar" aria-label="{{ $row['label'] }} Ndebele coverage" aria-valuenow="{{ $row['nd_complete_pct'] }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="h-full bg-gradient-to-r from-sky-500 to-sky-400" style="width: {{ $row['nd_complete_pct'] }}%"></div>
                                                <div class="h-full bg-amber-400" style="width: {{ $row['nd_partial_pct'] }}%"></div>
                                            </div>
                                            <span class="text-[11px] tabular-nums text-slate-500 dark:text-slate-400">{{ $row['nd_complete'] }}/{{ $row['total'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($row['status'] === 'empty')
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">Empty</span>
                                        @elseif ($row['status'] === 'complete')
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Complete</span>
                                        @elseif ($row['status'] === 'untouched')
                                            @if ($row['url'])
                                                <a href="{{ $row['url'] }}" class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700 underline-offset-4 hover:underline dark:bg-rose-500/10 dark:text-rose-300">Not started · review</a>
                                            @else
                                                <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">Not started</span>
                                            @endif
                                        @else
                                            @if ($row['url'])
                                                <a href="{{ $row['url'] }}" class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 underline-offset-4 hover:underline dark:bg-amber-500/10 dark:text-amber-300">In progress · review</a>
                                            @else
                                                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">In progress</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.38)] dark:border-slate-800 dark:bg-slate-950">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Priority review</p>
                    @if ($priorityRows === [])
                        <p class="mt-3 rounded-2xl bg-emerald-50 p-3 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Nothing pending — every record is translated.</p>
                    @else
                        <div class="mt-3 space-y-3">
                            @foreach ($priorityRows as $row)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900">
                                    <div class="flex items-center justify-between gap-2">
                                        @if ($row['url'])
                                            <a href="{{ $row['url'] }}" class="text-sm font-medium text-slate-800 underline-offset-4 hover:underline dark:text-slate-100">{{ $row['label'] }}</a>
                                        @else
                                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $row['label'] }}</p>
                                        @endif
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">{{ $row['score'] }}%</span>
                                    </div>
                                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800" role="progressbar" aria-label="{{ $row['label'] }} overall coverage" aria-valuenow="{{ $row['score'] }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-amber-400" style="width: {{ $row['score'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-[26px] border border-slate-200 bg-gradient-to-br from-slate-100 via-white to-slate-200 p-4 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.38)] dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Operational note</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Coverage refreshes every 5 minutes. Approve the English source copy first — partially translated records fall back to English for the missing fields until reviewed.
                    </p>
                </div>
            </aside>
        </section>
    </div>
</x-filament-panels::page>
