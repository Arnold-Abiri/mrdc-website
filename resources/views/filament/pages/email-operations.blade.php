<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Overview Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Mail Transport Status</span>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        @if ($activeConfig)
                            {{ $activeConfig->mail_enabled ? 'Active (DB)' : 'Disabled' }}
                        @else
                            Active (.env)
                        @endif
                    </p>
                    <span class="inline-flex h-3 w-3 rounded-full {{ ($activeConfig?->mail_enabled ?? true) ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    {{ $activeConfig ? $activeConfig->host.':'.$activeConfig->port : config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port') }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Database Queue Pending Jobs</span>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($pendingJobsCount) }}</p>
                    <span class="text-xs text-gray-400">table: jobs</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Queued tasks awaiting processing by queue worker</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed Queue Jobs</span>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-2xl font-bold {{ $failedJobsCount > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ number_format($failedJobsCount) }}
                    </p>
                    @if ($failedJobsCount > 0)
                        <button
                            type="button"
                            wire:click="retryAllFailed"
                            wire:confirm="Are you sure you want to retry all failed jobs?"
                            class="inline-flex items-center rounded bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 hover:bg-red-200 dark:bg-red-950 dark:text-red-300">
                            Retry All
                        </button>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500">table: failed_jobs</p>
            </div>
        </div>

        {{-- Failed Jobs Table --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Recent Failed Jobs</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Failed jobs recorded with sensitive notification payloads and tokens stripped out.
            </p>

            @if (count($failedJobsList) > 0)
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs font-medium uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3">Job Name</th>
                                <th class="px-4 py-3">Queue</th>
                                <th class="px-4 py-3">Failed At</th>
                                <th class="px-4 py-3">Exception Summary</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($failedJobsList as $job)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $job['name'] }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $job['queue'] }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $job['failed_at'] }}</td>
                                    <td class="px-4 py-3 text-xs text-red-600 dark:text-red-400 font-mono truncate max-w-md">
                                        {{ $job['exception_summary'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <button
                                            type="button"
                                            wire:click="retryJob('{{ $job['uuid'] }}')"
                                            class="inline-flex items-center text-xs font-medium text-emerald-600 hover:underline">
                                            Retry
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="forgetJob('{{ $job['uuid'] }}')"
                                            class="inline-flex items-center text-xs font-medium text-gray-400 hover:text-red-600">
                                            Forget
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-4 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                    ✓ No failed queue jobs detected. The mail and notification background queues are running cleanly.
                </div>
            @endif
        </div>

        {{-- Email Activity Audit Log --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Recent Email Delivery Attempts & Audit Trail</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Audited operations for citizen notifications, test dispatches, and configuration updates. Note that successful SMTP submission does not guarantee citizen inbox delivery.
            </p>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="border-b border-gray-200 bg-gray-50 text-xs font-medium uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Actor</th>
                            <th class="px-4 py-3">Metadata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($recentEmailAudits as $audit)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $audit->created_at?->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 font-semibold text-xs text-gray-900 dark:text-white">
                                    <span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                        {{ $audit->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    User #{{ $audit->actor_id ?? 'System' }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono">
                                    {{ json_encode($audit->metadata) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                    No recent email audit events found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>
