<x-filament-panels::page>
    <form method="get" class="mb-4 flex flex-wrap items-end gap-3">
        <label>Period
            <select name="period">
                @foreach (['today' => 'Today', 'last_7' => 'Last 7 days', 'last_30' => 'Last 30 days', 'month' => 'Current month', 'previous_month' => 'Previous month', 'year' => 'Current year', 'custom' => 'Custom range'] as $value => $label)
                    <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>From <input type="date" name="from" value="{{ $from }}" /></label>
        <label>To <input type="date" name="to" value="{{ $to }}" /></label>
        <button type="submit">Apply</button>
    </form>

    <p class="mb-4 text-sm text-gray-500">Showing {{ $from }} to {{ $to }}. Visitor-days are approximate (one browser counted once per day); sessions equal visitor-days by the same definition.</p>

    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach (['visitor_days' => 'Visitor-days (approx)', 'page_views' => 'Page views', 'content_views' => 'Content views', 'downloads' => 'Document downloads', 'tender_views' => 'Tender views', 'vacancy_views' => 'Vacancy views', 'news_views' => 'News/notice views', 'content_updates' => 'Content updates'] as $key => $label)
            <div class="rounded-xl bg-white p-4 shadow dark:bg-gray-900">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-bold">{{ $kpis[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="mb-2 text-lg font-bold">Daily trend</h2>
    <div class="mb-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left"><th class="p-2">Date</th><th class="p-2">Page views</th><th class="p-2">Content views</th><th class="p-2">Visitor-days</th></tr></thead>
            <tbody>
                @forelse ($trend as $day)
                    <tr class="border-t"><td class="p-2">{{ $day['date'] }}</td><td class="p-2">{{ $day['page_views'] }}</td><td class="p-2">{{ $day['content_views'] }}</td><td class="p-2">{{ $day['visitor_days'] }}</td></tr>
                @empty
                    <tr><td class="p-2" colspan="4">No public engagement recorded in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mb-6 grid gap-6 md:grid-cols-2">
        <div>
            <h2 class="mb-2 text-lg font-bold">Most viewed content</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($popular as $row)
                    <li>{{ $row['title'] }} — {{ $row['views'] }} views</li>
                @empty
                    <li>No content views recorded in this period.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="mb-2 text-lg font-bold">Most downloaded documents</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($documents as $row)
                    <li>{{ $row['title'] }} — {{ $row['downloads'] }} downloads</li>
                @empty
                    <li>No downloads recorded in this period.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mb-6 grid gap-6 md:grid-cols-3">
        <div>
            <h2 class="mb-2 text-lg font-bold">Tender views</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($tenders as $row)
                    <li>{{ $row['title'] }} — {{ $row['views'] }}</li>
                @empty
                    <li>No tender views recorded.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="mb-2 text-lg font-bold">Vacancy views</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($vacancies as $row)
                    <li>{{ $row['title'] }} — {{ $row['views'] }}</li>
                @empty
                    <li>No vacancy views recorded.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="mb-2 text-lg font-bold">News engagement (article views)</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($news as $row)
                    <li>{{ $row['title'] }} — {{ $row['views'] }}</li>
                @empty
                    <li>No article views recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mb-6 grid gap-6 md:grid-cols-2">
        <div>
            <h2 class="mb-2 text-lg font-bold">Referrals</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($referrals as $row)
                    <li>{{ ucfirst($row['category']) }} — {{ $row['events'] }}</li>
                @empty
                    <li>No referral data recorded.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="mb-2 text-lg font-bold">Pages viewed by selected website language</h2>
            <ul class="list-disc pl-5 text-sm">
                @forelse ($locales as $row)
                    <li>{{ $row['locale'] }} — {{ $row['views'] }}</li>
                @empty
                    <li>No language data recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <h2 class="mb-2 text-lg font-bold">Recent content activity</h2>
    <ul class="list-disc pl-5 text-sm">
        @forelse ($activity as $row)
            <li>{{ $row['date'] }} — {{ $row['action'] }} ({{ $row['subject'] }})</li>
        @empty
            <li>No content activity recorded in this period.</li>
        @endforelse
    </ul>
</x-filament-panels::page>
