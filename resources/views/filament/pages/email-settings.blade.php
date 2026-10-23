<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Status and Active Configuration Banner --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Active Mail Transport Status</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Outbound mail delivery pipeline for Mutoko Rural District Council.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if ($activeConfig)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            ● Active Database SMTP ({{ $activeConfig->name }})
                        </span>
                        @if (! $activeConfig->mail_enabled)
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                ⚠ Outbound Sending Disabled
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                            ● Fallback .env SMTP ({{ $envFallback['host'] }}:{{ $envFallback['port'] }})
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4 text-sm">
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                    <span class="text-gray-500">Provider</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($activeConfig->provider ?? 'Environment Fallback') }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                    <span class="text-gray-500">Host & Port</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $activeConfig->host ?? $envFallback['host'] }}:{{ $activeConfig->port ?? $envFallback['port'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                    <span class="text-gray-500">From Address</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $activeConfig->from_address ?? $envFallback['from_address'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                    <span class="text-gray-500">Last Verified</span>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        @if ($activeConfig?->last_tested_at)
                            {{ $activeConfig->last_tested_at->diffForHumans() }} ({{ $activeConfig->last_tested_status }})
                        @else
                            Not verified recently
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Preset Shortcuts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quick Provider Presets</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Selecting a preset fills in standard hosts, ports, and transport security settings. Presets are editable defaults, not guarantees of provider compatibility.
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <button
                    type="button"
                    wire:click="applyPreset('gmail')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Gmail / Google Workspace (Port 587 TLS)
                </button>
                <button
                    type="button"
                    wire:click="applyPreset('cpanel')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    cPanel SMTP (Port 465 SSL)
                </button>
                <button
                    type="button"
                    wire:click="applyPreset('microsoft365')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Microsoft 365 (Port 587 TLS)
                </button>
                <button
                    type="button"
                    wire:click="applyPreset('custom')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Custom Provider
                </button>
            </div>
        </div>

        {{-- Configuration Editor using Native Filament Form Components --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">SMTP Configuration Details</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Credentials are encrypted at rest with AES-256-CBC. Stored passwords are never revealed back to the browser.
            </p>

            <form wire:submit="saveDraft" class="space-y-6">
                {{ $this->form }}

                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        Save Configuration Draft
                    </button>

                    <button
                        type="button"
                        wire:click="testConnection"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <span wire:loading.remove wire:target="testConnection">Test Connection & TLS</span>
                        <span wire:loading wire:target="testConnection">Testing Connection...</span>
                    </button>

                    @if ($formData['id'])
                        <button
                            type="button"
                            wire:click="activateConfiguration"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Activate for Production
                        </button>
                    @endif

                    @if ($activeConfig)
                        <button
                            type="button"
                            wire:click="revertToEnv"
                            wire:confirm="Are you sure you want to deactivate database SMTP settings and fall back to .env values?"
                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-300">
                            Revert to .env Fallback
                        </button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Diagnostic Report Output --}}
        @if ($diagnosticResult)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Diagnostic Results ({{ $diagnosticResult['tested_at'] }})</h3>
                    <span class="text-xs text-gray-500">{{ $diagnosticResult['host'] }}:{{ $diagnosticResult['port'] }} ({{ strtoupper($diagnosticResult['encryption']) }})</span>
                </div>

                <div class="mt-4 space-y-3 text-sm">
                    {{-- Socket & TLS Check --}}
                    <div class="rounded-lg p-3 {{ $diagnosticResult['socket']['success'] ? 'bg-emerald-50 text-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200' : 'bg-red-50 text-red-900 dark:bg-red-950/40 dark:text-red-200' }}">
                        <div class="flex items-center justify-between">
                            <strong>1. Socket & TLS Verification:</strong>
                            <span>{{ $diagnosticResult['socket']['success'] ? 'PASSED' : 'FAILED' }}</span>
                        </div>
                        @if ($diagnosticResult['socket']['banner'])
                            <p class="mt-1 font-mono text-xs">Banner: {{ $diagnosticResult['socket']['banner'] }}</p>
                        @endif
                        @if ($diagnosticResult['socket']['error'])
                            <p class="mt-1 font-semibold text-xs">{{ $diagnosticResult['socket']['error'] }}</p>
                        @endif
                        @if ($diagnosticResult['socket']['peer_certificate'])
                            <div class="mt-2 text-xs opacity-90 border-t border-emerald-200/50 pt-2 dark:border-emerald-800/50">
                                <div><strong>Certificate Subject CN:</strong> {{ $diagnosticResult['socket']['peer_certificate']['subject_cn'] ?? 'N/A' }}</div>
                                <div><strong>Issuer:</strong> {{ $diagnosticResult['socket']['peer_certificate']['issuer'] ?? 'N/A' }}</div>
                                <div><strong>Valid Until:</strong> {{ $diagnosticResult['socket']['peer_certificate']['valid_to'] ?? 'N/A' }}</div>
                                @if (!empty($diagnosticResult['socket']['peer_certificate']['san']))
                                    <div class="truncate"><strong>Subject Alt Names (SAN):</strong> {{ $diagnosticResult['socket']['peer_certificate']['san'] }}</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Authentication Check --}}
                    <div class="rounded-lg p-3 {{ $diagnosticResult['auth']['success'] ? 'bg-emerald-50 text-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200' : 'bg-red-50 text-red-900 dark:bg-red-950/40 dark:text-red-200' }}">
                        <div class="flex items-center justify-between">
                            <strong>2. SMTP Handshake & Authentication:</strong>
                            <span>{{ $diagnosticResult['auth']['success'] ? 'PASSED' : 'FAILED' }}</span>
                        </div>
                        @if ($diagnosticResult['auth']['error'])
                            <p class="mt-1 font-semibold text-xs">{{ $diagnosticResult['auth']['error'] }}</p>
                        @endif
                        @if (!empty($diagnosticResult['auth']['note']))
                            <p class="mt-1 text-xs opacity-80">{{ $diagnosticResult['auth']['note'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Send Test Email Action Box --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Send Real Test Email</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Dispatches a test message via the configured SMTP transport to confirm actual inbox reception.
            </p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                <input
                    type="email"
                    wire:model="testRecipient"
                    placeholder="recipient@example.com"
                    class="block w-full max-w-md rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm"
                    required>

                <button
                    type="button"
                    wire:click="sendTestEmailAction"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-emerald-700 focus:outline-none">
                    <span wire:loading.remove wire:target="sendTestEmailAction">Send Test Email</span>
                    <span wire:loading wire:target="sendTestEmailAction">Sending...</span>
                </button>
            </div>
            @error('testRecipient') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

    </div>
</x-filament-panels::page>
