<x-filament-panels::page>
    <form method="get" class="mb-4 flex flex-wrap items-end gap-3 print:hidden">
        <label>Month <input type="month" name="month" value="{{ $month }}" max="{{ now()->format('Y-m') }}" /></label>
        <button type="submit">Show</button>
        <button type="button" onclick="window.print()">Print</button>
        <a href="{{ route('admin.monthly-export', ['month' => $month]) }}">Export metrics CSV</a>
    </form>

    <h2 class="mb-2 text-lg font-bold">Mutoko RDC website performance — {{ $month }}</h2>
    <p class="mb-4 text-sm text-gray-500">Generated {{ now()->toDateTimeString() }} from recorded system data.</p>

    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach (['visitor_days' => 'Visitor-days (approx)', 'page_views' => 'Page views', 'content_views' => 'Content views', 'downloads' => 'Document downloads', 'tender_views' => 'Tender views', 'vacancy_views' => 'Vacancy views', 'news_views' => 'News/notice views', 'content_updates' => 'Content updates'] as $key => $label)
            <div class="rounded-xl bg-white p-4 shadow dark:bg-gray-900">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-bold">{{ $kpis[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <div class="mb-6 grid gap-6 md:grid-cols-2">
        <div>
            <h3 class="mb-2 font-bold">Referral breakdown</h3>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($referrals as $row)
                    <li>{{ ucfirst($row['category']) }} — {{ $row['events'] }}</li>
                @empty
                    <li>No referral data recorded.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h3 class="mb-2 font-bold">Pages viewed by selected website language</h3>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($locales as $row)
                    <li>{{ $row['locale'] }} — {{ $row['views'] }}</li>
                @empty
                    <li>No language data recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <h3 class="mb-2 font-bold">Availability and incidents</h3>
    <ul class="mb-6 list-disc pl-5 text-sm">
        @forelse ($incidents as $incident)
            <li>{{ $incident->started_at }} — {{ $incident->recovered_at ?? 'ongoing' }} ({{ $incident->status }}): {{ $incident->summary }}</li>
        @empty
            <li>No incidents recorded for this month.</li>
        @endforelse
    </ul>

    <h3 class="mb-2 font-bold">Notable application errors</h3>
    <ul class="mb-6 list-disc pl-5 text-sm">
        @forelse ($errors as $row)
            <li>{{ $row->exception_class }} — {{ $row->events }} occurrence(s)</li>
        @empty
            <li>No application errors recorded for this month.</li>
        @endforelse
    </ul>

    <h3 class="mb-2 font-bold">Content activity</h3>
    <ul class="list-disc pl-5 text-sm">
        @forelse ($activity as $row)
            <li>{{ $row['date'] }} — {{ $row['action'] }} ({{ $row['subject'] }})</li>
        @empty
            <li>No content activity recorded.</li>
        @endforelse
    </ul>
</x-filament-panels::page>
