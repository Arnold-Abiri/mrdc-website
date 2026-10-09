<x-filament-panels::page>
    @php
        $editorial = \App\Filament\Resources\Editorial\EditorialItemResource::class;
        $meetingsResource = \App\Filament\Resources\Meetings\CouncilMeetingResource::class;
        $tendersResource = \App\Filament\Resources\Tenders\TenderResource::class;
        $vacanciesResource = \App\Filament\Resources\Vacancies\VacancyResource::class;
        $documentsResource = \App\Filament\Resources\Documents\DocumentResource::class;
        $projectsResource = \App\Filament\Resources\Projects\CouncilProjectResource::class;
        $enquiriesResource = \App\Filament\Resources\Enquiries\EnquiryResource::class;
        $totalContent = collect($contentTypes)->sum('value');
        $maxMonth = max(1, $months->max(fn ($month) => max($month['news'], $month['documents'], $month['projects'], $month['meetings'])));
        $chartSeries = [
            ['key' => 'news', 'label' => 'News', 'color' => '#1875ed'],
            ['key' => 'meetings', 'label' => 'Meetings', 'color' => '#08b978'],
            ['key' => 'documents', 'label' => 'Documents', 'color' => '#f7ae21'],
            ['key' => 'projects', 'label' => 'Projects', 'color' => '#7959ed'],
        ];
        $chartPoints = fn ($key) => $months->values()->map(fn ($month, $index) => (35 + $index * 49) . ',' . (150 - ($month[$key] / $maxMonth) * 120))->implode(' ');
        $donutStops = [];
        $donutPosition = 0;
        foreach ($contentTypes as $type) {
            $nextPosition = $donutPosition + ($totalContent ? $type['value'] / $totalContent * 100 : 0);
            $color = ['blue' => '#1875ed', 'green' => '#08b978', 'orange' => '#f7ae21', 'red' => '#f24651', 'purple' => '#7959ed', 'teal' => '#13b8b0'][$type['tone']];
            $donutStops[] = "$color {$donutPosition}% {$nextPosition}%";
            $donutPosition = $nextPosition;
        }
        $donutGradient = $totalContent ? 'conic-gradient(' . implode(', ', $donutStops) . ')' : 'conic-gradient(#e7edf5 0% 100%)';
        $quickActions = [
            ['label' => 'Create news', 'resource' => $editorial, 'tone' => 'blue', 'icon' => '✎'],
            ['label' => 'Add meeting', 'resource' => $meetingsResource, 'tone' => 'green', 'icon' => '▦'],
            ['label' => 'Create tender', 'resource' => $tendersResource, 'tone' => 'orange', 'icon' => '▤'],
            ['label' => 'Add vacancy', 'resource' => $vacanciesResource, 'tone' => 'red', 'icon' => '▣'],
            ['label' => 'Upload document', 'resource' => $documentsResource, 'tone' => 'purple', 'icon' => '↑'],
            ['label' => 'Add project', 'resource' => $projectsResource, 'tone' => 'teal', 'icon' => '▥'],
        ];
    @endphp

    <div class="mrdc-dashboard">
        <div class="mrdc-dashboard-main">
            <section class="mrdc-dash-hero">
                <div>
                    <p>Welcome back,</p>
                    <h1>{{ auth()->user()?->name }}</h1>
                    <span>Manage council content, services, projects and enquiries from one place.</span>
                </div>
                <div class="mrdc-dash-date"><span>{{ now()->format('l') }}</span><strong>{{ now()->format('d F Y') }}</strong></div>
            </section>

            <div class="mrdc-dash-metrics">
                @foreach ($metrics as $metric)
                    <div class="mrdc-dash-metric mrdc-tone-{{ $metric['tone'] }}">
                        <span class="mrdc-dash-metric-icon">{{ ['news' => '▤', 'calendar' => '▦', 'file' => '▥', 'briefcase' => '▣', 'chart' => '▂'][ $metric['icon'] ] }}</span>
                        <div><strong>{{ number_format($metric['value']) }}</strong><span>{{ $metric['label'] }}</span></div>
                    </div>
                @endforeach
            </div>

            <div class="mrdc-dash-analytics">
                <section class="mrdc-dash-panel mrdc-dash-trend">
                    <div class="mrdc-dash-panel-heading"><div><h3>Content overview</h3><p>Published items over the last 12 months</p></div><div class="mrdc-dash-legend">@foreach ($chartSeries as $series)<span style="--tone: {{ $series['color'] }}">{{ $series['label'] }}</span>@endforeach</div></div>
                    <div class="mrdc-dash-line-chart" role="img" aria-label="Monthly published news, meetings, documents and projects">
                        <div class="mrdc-dash-y-labels"><span>{{ $maxMonth }}</span><span>{{ (int) round($maxMonth / 2) }}</span><span>0</span></div>
                        <svg viewBox="0 0 600 180" preserveAspectRatio="none" aria-hidden="true">
                            <path d="M35 30H574 M35 90H574 M35 150H574" class="mrdc-dash-gridlines" />
                            @foreach ($chartSeries as $series)
                                <polyline points="{{ $chartPoints($series['key']) }}" fill="none" stroke="{{ $series['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                                @foreach ($months->values() as $index => $month)<circle cx="{{ 35 + $index * 49 }}" cy="{{ 150 - ($month[$series['key']] / $maxMonth) * 120 }}" r="3" fill="{{ $series['color'] }}" />@endforeach
                            @endforeach
                        </svg>
                        <div class="mrdc-dash-x-labels">@foreach ($months as $month)<span>{{ $month['label'] }}</span>@endforeach</div>
                    </div>
                </section>
                <section class="mrdc-dash-panel mrdc-dash-types">
                    <div class="mrdc-dash-panel-heading"><div><h3>Content by type</h3><p>Published and active items</p></div></div>
                    <div class="mrdc-dash-types-body"><div class="mrdc-dash-total" style="background: {{ $donutGradient }}"><div><strong>{{ number_format($totalContent) }}</strong><span>Total</span></div></div><ul>@foreach ($contentTypes as $type)<li><span class="mrdc-type-dot mrdc-bg-{{ $type['tone'] }}"></span><span>{{ $type['label'] }}</span><strong>{{ $type['value'] }}</strong></li>@endforeach</ul></div>
                </section>
            </div>

            <div class="mrdc-dash-three">
                <section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Latest news</h3>@if ($editorial::canViewAny())<a href="{{ $editorial::getUrl() }}">View all →</a>@endif</div><div class="mrdc-dash-list mrdc-dash-news-list">@forelse ($news as $item)<a href="{{ $editorial::getUrl('edit', ['record' => $item]) }}">@if ($item->featuredMedia?->status === 'active')<img class="mrdc-dash-news-image" src="{{ public_route('managed-media.show', $item->featured_media_id) }}" alt="">@else<span class="mrdc-dash-news-image mrdc-dash-image-fallback"></span>@endif<span><strong>{{ $item->title }}</strong><small>{{ $item->created_at?->format('d M Y') }}</small></span><em class="mrdc-dash-published">{{ ucfirst($item->status) }}</em></a>@empty<p class="mrdc-dash-empty">No news articles yet.</p>@endforelse</div></section>
                <section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Upcoming meetings</h3>@if ($meetingsResource::canViewAny())<a href="{{ $meetingsResource::getUrl() }}">View all →</a>@endif</div><div class="mrdc-dash-list">@forelse ($meetings as $item)<a href="{{ $meetingsResource::getUrl('edit', ['record' => $item]) }}"><span class="mrdc-dash-meeting-date"><strong>{{ $item->scheduled_date?->format('d') }}</strong>{{ $item->scheduled_date?->format('M') }}</span><span><strong>{{ $item->title }}</strong><small>{{ $item->scheduled_time }} @if ($item->venue) · {{ $item->venue }} @endif</small></span></a>@empty<p class="mrdc-dash-empty">No upcoming meetings.</p>@endforelse</div></section>
                <section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Recent documents</h3>@if ($documentsResource::canViewAny())<a href="{{ $documentsResource::getUrl() }}">View all →</a>@endif</div><div class="mrdc-dash-list">@forelse ($documents as $item)<a href="{{ $documentsResource::getUrl('edit', ['record' => $item]) }}"><span class="mrdc-dash-list-icon mrdc-tone-purple">▥</span><span><strong>{{ $item->title }}</strong><small>{{ str_replace('_', ' ', ucfirst($item->category ?? 'Document')) }}</small></span><em class="mrdc-dash-published">{{ ucfirst($item->status) }}</em></a>@empty<p class="mrdc-dash-empty">No documents yet.</p>@endforelse</div></section>
            </div>

            <div class="mrdc-dash-bottom">
                <section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Development projects</h3>@if ($projectsResource::canViewAny())<a href="{{ $projectsResource::getUrl() }}">View all →</a>@endif</div><div class="mrdc-dash-projects">@forelse ($projects as $item)<a href="{{ $projectsResource::getUrl('edit', ['record' => $item]) }}"><span class="mrdc-dash-project-image" @if ($item->featuredMedia?->status === 'active')style="background-image: url('{{ public_route('managed-media.show', $item->featured_media_id) }}')"@endif><em>{{ str_replace('_', ' ', ucfirst($item->project_status)) }}</em></span><strong>{{ $item->title }}</strong><small>{{ $item->location ?: 'Mutoko district' }}</small><span class="mrdc-dash-progress"><i style="width: {{ min(100, max(0, (int) $item->progress_percent)) }}%"></i></span><span class="mrdc-dash-project-status">@if ($item->progress_percent !== null){{ $item->progress_percent }}% complete @else Progress unconfirmed @endif</span></a>@empty<p class="mrdc-dash-empty">No projects yet.</p>@endforelse</div></section>
                <section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Recent enquiries</h3>@if ($enquiriesResource::canViewAny())<a href="{{ $enquiriesResource::getUrl() }}">View all →</a>@endif</div><div class="mrdc-dash-table-wrap"><table><thead><tr><th>Reference</th><th>Subject</th><th>Date</th><th>Status</th></tr></thead><tbody>@forelse ($enquiries as $item)<tr><td><a href="{{ $enquiriesResource::getUrl('view', ['record' => $item]) }}">{{ $item->public_id }}</a></td><td>{{ $item->subject }}</td><td>{{ $item->submitted_at?->format('d M Y') }}</td><td><span class="mrdc-dash-status">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span></td></tr>@empty<tr><td colspan="4" class="mrdc-dash-empty">No enquiries yet.</td></tr>@endforelse</tbody></table></div></section>
            </div>
        </div>

        <aside class="mrdc-dashboard-aside">
            <section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Quick actions</h3></div><div class="mrdc-dash-actions">@foreach ($quickActions as $action)@if ($action['resource']::canCreate())<a href="{{ $action['resource']::getUrl('create') }}" class="mrdc-tone-{{ $action['tone'] }}"><span>{{ $action['icon'] }}</span>{{ $action['label'] }}</a>@endif @endforeach</div></section>
            @if ($editorial::canViewAny())<section class="mrdc-dash-panel"><div class="mrdc-dash-panel-heading"><h3>Pending approvals</h3><span class="mrdc-dash-count">{{ $approvals->count() }}</span></div><div class="mrdc-dash-list">@forelse ($approvals as $item)<a href="{{ $editorial::getUrl('edit', ['record' => $item]) }}"><span><strong>{{ $item->title }}</strong><small>{{ ucfirst($item->type) }} · {{ $item->updated_at?->format('d M Y') }}</small></span><span class="mrdc-dash-pending">Review</span></a>@empty<p class="mrdc-dash-empty">No items awaiting review.</p>@endforelse</div></section>@endif
        </aside>
    </div>
</x-filament-panels::page>
