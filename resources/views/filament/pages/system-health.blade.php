<x-filament-panels::page>
    <p class="mb-4 text-sm text-gray-500">Operational status only. No hostnames, credentials, or connection details are shown here.</p>
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($checks as $name => $check)
            <div class="rounded-xl bg-white p-4 shadow dark:bg-gray-900">
                <p class="text-sm text-gray-500">{{ ucfirst($name) }}</p>
                <p class="text-2xl font-bold">{{ $check['status'] }}</p>
                <p class="text-sm">{{ $check['detail'] }}</p>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
