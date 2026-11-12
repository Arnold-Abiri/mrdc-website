<x-filament-panels::page>
    <div class="space-y-6">
        <header class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.4)] dark:border-slate-800 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
            <div class="flex flex-col gap-4 border-b border-slate-200/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-[0_12px_24px_-12px_rgba(15,23,42,0.8)] dark:bg-slate-100 dark:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Monthly report</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-[-0.04em] text-slate-900 dark:text-white">Performance summary &mdash; {{ $month }}</h2>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]" aria-hidden="true"></span>
                    Generated {{ now()->format('Y-m-d H:i') }}
                </div>
            </div>

            <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between print:hidden">
                <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">
                    Public engagement, system uptime, and editorial activity across the website.
                </p>

                <form method="get" class="flex flex-wrap items-center gap-2">
                    <input type="month" name="month" value="{{ $month }}" max="{{ now()->format('Y-m') }}" class="h-9 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" />

                    <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-500">
                        <span>Show</span>
                    </button>

                    <button type="button" onclick="window.print()" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span>Print</span>
                    </button>

                    <a href="{{ route('admin.monthly-export', ['month' => $month]) }}" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span>Export CSV</span>
                    </a>
                </form>
            </div>
        </header>

        {{-- Metric Summary Grid --}}
        @php
            $kpiConfigs = [
                'visitor_days' => ['label' => 'Visitor-Days (approx)', 'desc' => 'Monthly unique devices', 'tone' => 'indigo'],
                'page_views' => ['label' => 'Total Page Views', 'desc' => 'Citizen & public visits', 'tone' => 'sky'],
                'content_views' => ['label' => 'Content Views', 'desc' => 'Direct article accesses', 'tone' => 'emerald'],
                'downloads' => ['label' => 'Document Downloads', 'desc' => 'Official downloads', 'tone' => 'purple'],
                'tender_views' => ['label' => 'Tender Views', 'desc' => 'Procurement tracking', 'tone' => 'amber'],
                'vacancy_views' => ['label' => 'Vacancy Views', 'desc' => 'Job opportunities', 'tone' => 'rose'],
                'news_views' => ['label' => 'News / Notices', 'desc' => 'Community announcements', 'tone' => 'teal'],
                'content_updates' => ['label' => 'Content Updates', 'desc' => 'Staff publications', 'tone' => 'slate'],
            ];
        @endphp

        <section class="grid grid-cols-2 gap-4 sm:grid-cols-4" aria-label="Monthly Metrics">
            @foreach ($kpiConfigs as $key => $config)
                <div class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400 truncate">{{ $config['label'] }}</p>
                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white tabular-nums">
                        {{ number_format((int) ($kpis[$key] ?? 0)) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate">{{ $config['desc'] }}</p>
                </div>
            @endforeach
        </section>

        {{-- Referrals & Locales --}}
        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Referral Breakdown</h3>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Channels</span>
                </div>
                <div class="mt-4 space-y-2.5">
                    @forelse ($referrals as $row)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 dark:border-slate-800 dark:bg-slate-900/40">
                            <span class="text-xs font-semibold capitalize text-slate-800 dark:text-slate-200">{{ $row['category'] }}</span>
                            <span class="rounded-full bg-white px-2 py-0.5 text-xs font-bold text-slate-700 shadow-sm dark:bg-slate-800 dark:text-slate-300 tabular-nums">
                                {{ number_format($row['events']) }} visits
                            </span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No referral data recorded for this month.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Website Language Distribution</h3>
                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Localization</span>
                </div>
                <div class="mt-4 space-y-2.5">
                    @forelse ($locales as $row)
                        @php
                            $localeLabel = match(strtolower($row['locale'])) {
                                'en' => 'English (Official / Source)',
                                'sn' => 'ChiShona',
                                'nd' => 'isiNdebele',
                                default => strtoupper($row['locale']),
                            };
                        @endphp
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 dark:border-slate-800 dark:bg-slate-900/40">
                            <span class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $localeLabel }}</span>
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 tabular-nums">
                                {{ number_format($row['views']) }} views
                            </span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">No language data recorded for this month.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Availability & Incidents Section --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Availability & System Incidents</h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Documented portal downtime, degradations, and maintenance windows</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full {{ count($incidents) === 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' }} px-3 py-1 text-xs font-semibold">
                        <span class="h-2 w-2 rounded-full {{ count($incidents) === 0 ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ count($incidents) === 0 ? '100% Uptime' : count($incidents).' recorded incident(s)' }}
                    </span>
                </div>
            </div>

            <div class="p-5">
                @if (count($incidents) === 0)
                    <div class="flex items-center gap-3 rounded-2xl bg-emerald-50/70 p-4 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
                        <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>No service interruptions or outages recorded for {{ $month }}. The portal operated cleanly throughout the entire billing cycle.</span>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($incidents as $incident)
                            <div class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800 dark:bg-slate-900/60">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300 uppercase">
                                            {{ $incident->status }}
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $incident->started_at }} &mdash; {{ $incident->recovered_at ?? 'Ongoing' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $incident->summary }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Application Errors Summary --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Notable Application Errors</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Aggregated server exceptions and error clusters</p>
            </div>

            <div class="p-5">
                @if (count($errors) === 0)
                    <div class="rounded-2xl bg-emerald-50/70 p-4 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
                        No critical application exceptions recorded for this month.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($errors as $row)
                            <div class="flex items-center justify-between py-3">
                                <span class="font-mono text-xs text-rose-600 dark:text-rose-400 truncate max-w-lg">
                                    {{ $row->exception_class }}
                                </span>
                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300 tabular-nums">
                                    {{ number_format($row->events) }} occurrences
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Content Editorial Activity --}}
        <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_24px_55px_-28px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-950">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Council Content Activity Record</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Chronological staff changes made during this month</p>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($activity as $row)
                    <div class="flex items-center justify-between px-5 py-3 transition hover:bg-slate-50/70 dark:hover:bg-slate-900/40">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                <span class="font-semibold capitalize text-emerald-700 dark:text-emerald-400">{{ $row['action'] }}</span>: {{ $row['subject'] }}
                            </p>
                            <p class="text-xs text-slate-400 tabular-nums">{{ $row['date'] }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            Verified
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">
                        No content activity recorded for this month.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
