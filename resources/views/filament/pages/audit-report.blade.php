<x-filament-panels::page>
    <form method="get" class="mb-4 flex flex-wrap items-end gap-3">
        <label>From <input type="date" name="from" value="{{ $filters['from'] }}" /></label>
        <label>To <input type="date" name="to" value="{{ $filters['to'] }}" /></label>
        <label>Actor ID
            <select name="actor">
                <option value="">All</option>
                @foreach ($actors as $actor)
                    <option value="{{ $actor }}" @selected($filters['actor'] == $actor)>{{ $actor }}</option>
                @endforeach
            </select>
        </label>
        <label>Action contains <input type="search" name="action" value="{{ $filters['action'] }}" maxlength="120" /></label>
        <label>Entity contains <input type="search" name="subject_type" value="{{ $filters['subject_type'] }}" maxlength="120" /></label>
        <button type="submit">Filter</button>
        @can('audit.export')
            <a href="{{ route('admin.audit-export', request()->query()) }}">Export CSV</a>
        @endcan
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left"><th class="p-2">Timestamp</th><th class="p-2">Actor</th><th class="p-2">Action</th><th class="p-2">Entity</th><th class="p-2">Entity ID</th></tr></thead>
            <tbody>
                @forelse ($events as $event)
                    <tr class="border-t"><td class="p-2">{{ $event->created_at }}</td><td class="p-2">{{ $event->actor_id ?? '—' }}</td><td class="p-2">{{ $event->action }}</td><td class="p-2">{{ $event->subject_type }}</td><td class="p-2">{{ $event->subject_id }}</td></tr>
                @empty
                    <tr><td class="p-2" colspan="5">No audit events match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-sm text-gray-500">Showing up to 500 most recent matching events. Use CSV export for the full set. Sensitive values are redacted at write time.</p>
</x-filament-panels::page>
