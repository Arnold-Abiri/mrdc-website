<x-filament-panels::page>
    <div class="space-y-6">
        <header class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.4)] dark:border-slate-800 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
            <div class="flex flex-col gap-4 border-b border-slate-200/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-[0_12px_24px_-12px_rgba(15,23,42,0.8)] dark:bg-slate-100 dark:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Monitoring & evaluation</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-[-0.04em] text-slate-900 dark:text-white">Public engagement and reach</h2>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]" aria-hidden="true"></span>
                    {{ $from }} to {{ $to }}
                </div>
            </div>

            <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">
                    Visitor-days count one browser once per day. Sessions equal visitor-days by the same definition.
                </p>

                <form method="get" class="flex flex-wrap items-center gap-2">
                    <select name="period" class="h-9 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        @foreach (['today' => 'Today', 'last_7' => 'Last 7 days', 'last_30' => 'Last 30 days', 'month' => 'Current month', 'previous_month' => 'Previous month', 'year' => 'Current year', 'custom' => 'Custom range'] as $value => $label)
                            <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="from" value="{{ $from }}" class="h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />
                    <span class="text-xs text-slate-400">to</span>
                    <input type="date" name="to" value="{{ $to }}" class="h-9 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />

                    <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                        <span>Apply</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- KPI Grid --}}
        @php
            $kpiConfigs = [
                'visitor_days' => ['label' => 'Visitor-Days (approx)', 'desc' => 'Unique daily devices', 'tone' => 'indigo', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />'],
                'page_views' => ['label' => 'Total Page Views', 'desc' => 'All portal pages', 'tone' => 'sky', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />'],
                'content_views' => ['label' => 'Content Views', 'desc' => 'Items & service details', 'tone' => 'emerald', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />'],
                'downloads' => ['label' => 'Document Downloads', 'desc' => 'PDFs, bylaws, forms', 'tone' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />'],
                'tender_views' => ['label' => 'Tender Views', 'desc' => 'Procurement notices', 'tone' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />'],
                'vacancy_views' => ['label' => 'Vacancy Views', 'desc' => 'Careers & jobs', 'tone' => 'rose', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />'],
                'news_views' => ['label' => 'News / Notices', 'desc' => 'Articles & updates', 'tone' => 'teal', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />'],
                'content_updates' => ['label' => 'Content Updates', 'desc' => 'Staff editorial edits', 'tone' => 'slate', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />'],
            ];
        @endphp

        <section class="grid grid-cols-2 gap-4 sm:grid-cols-4" aria-label="Key Performance Indicators">
            @foreach ($kpiConfigs as $key => $config)
                <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.35)] transition-all hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400 truncate">{{ $config['label'] }}</span>
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                {!! $config['icon'] !!}
                            </svg>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white tabular-nums">
                        {{ number_format((int) ($kpis[$key] ?? 0)) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate">{{ $config['desc'] }}</p>
                </div>
            @endforeach
        </section>

        {{-- Daily Trend Section --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Daily Traffic & Engagement Trend</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Day-by-day public engagement and visitor volume</p>
                </div>
                <div class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    {{ count($trend) }} recorded days
                </div>
            </div>

            <div class="max-h-[380px] overflow-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="sticky top-0 z-10">
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                            <th class="px-5 py-3" scope="col">Date</th>
                            <th class="px-4 py-3 text-right" scope="col">Page Views</th>
                            <th class="px-4 py-3 text-right" scope="col">Content Views</th>
                            <th class="px-5 py-3 text-right" scope="col">Visitor-Days (approx)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($trend as $day)
                            <tr class="transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                                <td class="px-5 py-3 font-medium text-slate-900 dark:text-white">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                        <span class="tabular-nums">{{ $day['date'] }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-slate-700 dark:text-slate-300 font-semibold">{{ number_format($day['page_views']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-slate-600 dark:text-slate-400">{{ number_format($day['content_views']) }}</td>
                                <td class="px-5 py-3 text-right tabular-nums text-indigo-600 dark:text-indigo-400 font-bold">{{ number_format($day['visitor_days']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400" colspan="4">
                                    No public engagement recorded for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Content Performance: Popular & Documents --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Most Viewed Content --}}
            <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Most Viewed Content</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">High-interest public pages and notices</p>
                    </div>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </span>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($popular as $index => $row)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3 transition hover:border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-slate-600 shadow-sm dark:bg-slate-800 dark:text-slate-300">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm font-medium text-slate-800 truncate dark:text-slate-200">{{ $row['title'] }}</span>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 tabular-nums">
                                {{ number_format($row['views']) }} views
                            </span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">No content views recorded in this period.</p>
                    @endforelse
                </div>
            </section>

            {{-- Most Downloaded Documents --}}
            <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Most Downloaded Documents</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Official bylaws, forms, budgets and reports</p>
                    </div>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-300">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </span>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($documents as $index => $row)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3 transition hover:border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-slate-600 shadow-sm dark:bg-slate-800 dark:text-slate-300">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm font-medium text-slate-800 truncate dark:text-slate-200">{{ $row['title'] }}</span>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-bold text-purple-700 dark:bg-purple-500/10 dark:text-purple-300 tabular-nums">
                                {{ number_format($row['downloads']) }} d/l
                            </span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">No downloads recorded in this period.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Domain Specific Views: Tenders, Vacancies, News --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Tenders --}}
            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Tenders Views</h4>
                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">Procurement</span>
                </div>
                <div class="mt-3 space-y-2">
                    @forelse ($tenders as $row)
                        <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50/60 p-2.5 dark:bg-slate-900/40">
                            <span class="text-xs font-medium text-slate-800 truncate dark:text-slate-200">{{ $row['title'] }}</span>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 shrink-0 tabular-nums">{{ number_format($row['views']) }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No tender views recorded.</p>
                    @endforelse
                </div>
            </section>

            {{-- Vacancies --}}
            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Vacancy Views</h4>
                    <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">Recruitment</span>
                </div>
                <div class="mt-3 space-y-2">
                    @forelse ($vacancies as $row)
                        <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50/60 p-2.5 dark:bg-slate-900/40">
                            <span class="text-xs font-medium text-slate-800 truncate dark:text-slate-200">{{ $row['title'] }}</span>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 shrink-0 tabular-nums">{{ number_format($row['views']) }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No vacancy views recorded.</p>
                    @endforelse
                </div>
            </section>

            {{-- News & Notices --}}
            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">News Engagement</h4>
                    <span class="rounded-full bg-teal-50 px-2 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-500/10 dark:text-teal-300">Public Info</span>
                </div>
                <div class="mt-3 space-y-2">
                    @forelse ($news as $row)
                        <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50/60 p-2.5 dark:bg-slate-900/40">
                            <span class="text-xs font-medium text-slate-800 truncate dark:text-slate-200">{{ $row['title'] }}</span>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 shrink-0 tabular-nums">{{ number_format($row['views']) }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No article views recorded.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Referrals & Language Localization --}}
        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Referral Channels</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Sources driving public traffic to portal</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Origin</span>
                </div>
                <div class="mt-4 space-y-2.5">
                    @forelse ($referrals as $row)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 dark:border-slate-800 dark:bg-slate-900/40">
                            <span class="text-xs font-semibold capitalize text-slate-800 dark:text-slate-200">{{ $row['category'] }}</span>
                            <span class="rounded-full bg-white px-2 py-0.5 text-xs font-bold text-slate-700 shadow-sm dark:bg-slate-800 dark:text-slate-300 tabular-nums">
                                {{ number_format($row['events']) }}
                            </span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No referral data recorded.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Languages Viewed</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Citizen language preference distribution</p>
                    </div>
                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">Locales</span>
                </div>
                <div class="mt-4 space-y-2.5">
                    @forelse ($locales as $row)
                        @php
                            $localeLabel = match(strtolower($row['locale'])) {
                                'en' => 'English (Official/Source)',
                                'sn' => 'ChiShona',
                                'nd' => 'isiNdebele',
                                default => strtoupper($row['locale']),
                            };
                        @endphp
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 dark:border-slate-800 dark:bg-slate-900/40">
                            <span class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $localeLabel }}</span>
                            <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 tabular-nums">
                                {{ number_format($row['views']) }} views
                            </span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No language data recorded.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Content Editorial Activity Stream --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Content Activity</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Updates, creations, and publications across council modules</p>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($activity as $row)
                    <div class="flex items-center justify-between gap-4 px-5 py-3 transition hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                    <span class="capitalize">{{ $row['action'] }}</span>: {{ $row['subject'] }}
                                </p>
                                <p class="text-xs text-slate-400 tabular-nums">{{ $row['date'] }}</p>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            Logged
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">
                        No content activity recorded in this period.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
