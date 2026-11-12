<x-filament-panels::page>
    <div class="space-y-6">
        <header class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.4)] dark:border-slate-800 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
            <div class="flex flex-col gap-4 border-b border-slate-200/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-[0_12px_24px_-12px_rgba(15,23,42,0.8)] dark:bg-slate-100 dark:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">System health</p>
                        <h2 class="mt-1 text-2xl font-semibold tracking-[-0.04em] text-slate-900 dark:text-white">Service and storage availability</h2>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]" aria-hidden="true"></span>
                    Operational status only
                </div>
            </div>

            <p class="px-5 py-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                Connection and queue status across core services. Credentials and hostnames are omitted.
            </p>
        </header>

        <section class="grid gap-4 md:grid-cols-3" aria-label="System checks">
            @foreach ($checks as $name => $check)
                @php
                    $isHealthy = strtolower($check['status']) === 'healthy';
                    $isDegraded = strtolower($check['status']) === 'degraded';

                    $borderColor = $isHealthy ? 'border-slate-200 dark:border-slate-800' : ($isDegraded ? 'border-amber-200 dark:border-amber-500/20' : 'border-rose-200 dark:border-rose-500/20');
                    $badgeClass = $isHealthy ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ($isDegraded ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300');
                @endphp

                <div class="rounded-[24px] border {{ $borderColor }} bg-white p-4 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.45)] dark:bg-slate-950">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ ucfirst($name) }}</p>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $check['status'] }}</span>
                    </div>
                    <p class="mt-4 text-base font-semibold text-slate-900 dark:text-white">{{ $check['detail'] }}</p>
                </div>
            @endforeach
        </section>
    </div>
</x-filament-panels::page>

