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
            ['key' => 'news', 'label' => 'News Articles', 'color' => '#1875ed'],
            ['key' => 'meetings', 'label' => 'Meetings & Events', 'color' => '#08b978'],
            ['key' => 'documents', 'label' => 'Public Tenders', 'color' => '#f7ae21'],
            ['key' => 'projects', 'label' => 'Vacancies', 'color' => '#eb3546'],
        ];

        // Generate smooth SVG line coordinates
        $chartPoints = fn ($key) => $months->values()->map(fn ($month, $index) => (35 + $index * 49) . ',' . (140 - ($month[$key] / $maxMonth) * 110))->implode(' ');

        $donutStops = [];
        $donutPosition = 0;
        foreach ($contentTypes as $type) {
            $nextPosition = $donutPosition + ($totalContent ? $type['value'] / $totalContent * 100 : 0);
            $color = [
                'blue' => '#1875ed',
                'green' => '#08b978',
                'orange' => '#f7ae21',
                'red' => '#f24651',
                'purple' => '#7959ed',
                'teal' => '#13b8b0'
            ][$type['tone']] ?? '#1875ed';
            $donutStops[] = "$color {$donutPosition}% {$nextPosition}%";
            $donutPosition = $nextPosition;
        }
        $donutGradient = $totalContent ? 'conic-gradient(' . implode(', ', $donutStops) . ')' : 'conic-gradient(#e7edf5 0% 100%)';

        $quickActions = [
            [
                'label' => 'Create News',
                'resource' => $editorial,
                'tone' => 'blue',
                'icon_svg' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>'
            ],
            [
                'label' => 'Add Event',
                'resource' => $meetingsResource,
                'tone' => 'green',
                'icon_svg' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" /></svg>'
            ],
            [
                'label' => 'New Notice',
                'resource' => $editorial,
                'tone' => 'orange',
                'icon_svg' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" /></svg>'
            ],
            [
                'label' => 'Create Tender',
                'resource' => $tendersResource,
                'tone' => 'orange',
                'icon_svg' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>'
            ],
            [
                'label' => 'Add Vacancy',
                'resource' => $vacanciesResource,
                'tone' => 'red',
                'icon_svg' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>'
            ],
            [
                'label' => 'Upload Doc',
                'resource' => $documentsResource,
                'tone' => 'purple',
                'icon_svg' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
        ];
    @endphp

    <div class="mrdc-dashboard">
        <div class="mrdc-dashboard-main">
            {{-- Top Hero Section --}}
            <section class="mrdc-dash-hero">
                <div class="mrdc-dash-hero-content">
                    <p class="mrdc-dash-hero-welcome">Welcome back,</p>
                    <h1 class="mrdc-dash-hero-name">{{ auth()->user()?->name ?? 'Council Member' }}</h1>
                    <span class="mrdc-dash-hero-sub">Manage content, services, development projects and council operations for a better Mutoko.</span>
                </div>
                <div class="mrdc-dash-weather-card">
                    <div class="mrdc-dash-date-row">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span>{{ now()->format('l, d F Y') }}</span>
                    </div>
                    <div class="mrdc-dash-weather-row">
                        <div class="mrdc-weather-icon-wrap">
                            <svg class="w-5 h-5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                        </div>
                        <div class="mrdc-dash-weather-info">
                            <div class="mrdc-weather-temp">22°C <small class="text-xs text-slate-300 font-normal">· Mutoko</small></div>
                            <div class="mrdc-weather-desc">Mashonaland East · Mostly sunny</div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 5 Metric Cards --}}
            <div class="mrdc-dash-metrics">
                @foreach ($metrics as $metric)
                    <div class="mrdc-dash-metric mrdc-tone-{{ $metric['tone'] }}">
                        <div class="mrdc-dash-metric-top">
                            <div class="mrdc-dash-metric-icon">
                                @if ($metric['icon'] === 'news')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" /></svg>
                                @elseif ($metric['icon'] === 'calendar')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                @elseif ($metric['icon'] === 'tender')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                @elseif ($metric['icon'] === 'briefcase')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.25 2.25L18 6.75" /></svg>
                                @endif
                            </div>
                            <span class="mrdc-dash-delta mrdc-delta-{{ $metric['change_tone'] }}">
                                @if ($metric['change_tone'] === 'up')
                                    <svg class="w-3 h-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
                                @else
                                    <svg class="w-3 h-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>
                                @endif
                                {{ $metric['change'] }}
                            </span>
                        </div>
                        <div class="mrdc-dash-metric-bottom">
                            <span class="mrdc-dash-metric-value">{{ number_format($metric['value']) }}</span>
                            <span class="mrdc-dash-metric-title">{{ $metric['label'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Analytics Row: Line Chart + Donut Chart --}}
            <div class="mrdc-dash-analytics">
                {{-- Content Overview Line Chart --}}
                <section class="mrdc-dash-panel mrdc-dash-trend">
                    <div class="mrdc-dash-panel-heading">
                        <div>
                            <h3>Content Overview</h3>
                            <p>Published items and activity over the last 12 months</p>
                        </div>
                        <div class="mrdc-dash-legend">
                            @foreach ($chartSeries as $series)
                                <span style="--tone: {{ $series['color'] }}">{{ $series['label'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mrdc-dash-line-chart" role="img" aria-label="Monthly published news, meetings, documents and projects">
                        <div class="mrdc-dash-y-labels">
                            <span>{{ $maxMonth }}</span>
                            <span>{{ (int) round($maxMonth * 0.66) }}</span>
                            <span>{{ (int) round($maxMonth * 0.33) }}</span>
                            <span>0</span>
                        </div>
                        <svg viewBox="0 0 600 180" preserveAspectRatio="none" aria-hidden="true">
                            <path d="M35 30H574 M35 70H574 M35 110H574 M35 150H574" class="mrdc-dash-gridlines" />
                            @foreach ($chartSeries as $series)
                                <polyline points="{{ $chartPoints($series['key']) }}" fill="none" stroke="{{ $series['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                                @foreach ($months->values() as $index => $month)
                                    <circle cx="{{ 35 + $index * 49 }}" cy="{{ 140 - ($month[$series['key']] / $maxMonth) * 110 }}" r="3.5" fill="#ffffff" stroke="{{ $series['color'] }}" stroke-width="2" />
                                @endforeach
                            @endforeach
                        </svg>
                        <div class="mrdc-dash-x-labels">
                            @foreach ($months as $month)
                                <span>{{ $month['label'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Content by Type Donut Chart --}}
                <section class="mrdc-dash-panel mrdc-dash-types">
                    <div class="mrdc-dash-panel-heading">
                        <div>
                            <h3>Content by Type</h3>
                            <p>Distribution of published and active assets</p>
                        </div>
                    </div>
                    <div class="mrdc-dash-types-body">
                        <div class="mrdc-dash-total" style="background: {{ $donutGradient }}">
                            <div class="mrdc-dash-total-inner">
                                <strong>{{ number_format($totalContent) }}</strong>
                                <span>Total Items</span>
                            </div>
                        </div>
                        <ul class="mrdc-dash-type-list">
                            @foreach ($contentTypes as $type)
                                <li>
                                    <span class="mrdc-type-dot mrdc-bg-{{ $type['tone'] }}"></span>
                                    <span class="mrdc-type-name">{{ $type['label'] }}</span>
                                    <span class="mrdc-type-val">{{ number_format($type['value']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            </div>

            {{-- Three-Column Section: Latest News, Upcoming Events, Recent Documents --}}
            <div class="mrdc-dash-three">
                {{-- Latest News --}}
                <section class="mrdc-dash-panel">
                    <div class="mrdc-dash-panel-heading">
                        <h3>Latest News</h3>
                        @if ($editorial::canViewAny())
                            <a href="{{ $editorial::getUrl() }}" class="mrdc-view-all">View all &rarr;</a>
                        @endif
                    </div>
                    <div class="mrdc-dash-list mrdc-dash-news-list">
                        @forelse ($news as $item)
                            <a href="{{ $editorial::getUrl('edit', ['record' => $item]) }}" class="mrdc-dash-card-item">
                                @if ($item->featuredMedia?->status === 'active')
                                    <img class="mrdc-dash-news-image" src="{{ public_route('managed-media.show', $item->featured_media_id) }}" alt="{{ $item->title }}">
                                @else
                                    <div class="mrdc-dash-news-image mrdc-dash-image-fallback"></div>
                                @endif
                                <div class="mrdc-dash-item-meta">
                                    <strong>{{ $item->title }}</strong>
                                    <small>{{ $item->created_at?->format('d M Y') }}</small>
                                </div>
                                <span class="mrdc-dash-status-pill mrdc-pill-green">{{ ucfirst($item->status) }}</span>
                            </a>
                        @empty
                            <p class="mrdc-dash-empty">No news articles published yet.</p>
                        @endforelse
                    </div>
                </section>

                {{-- Upcoming Meetings / Events --}}
                <section class="mrdc-dash-panel">
                    <div class="mrdc-dash-panel-heading">
                        <h3>Upcoming Events</h3>
                        @if ($meetingsResource::canViewAny())
                            <a href="{{ $meetingsResource::getUrl() }}" class="mrdc-view-all">View all &rarr;</a>
                        @endif
                    </div>
                    <div class="mrdc-dash-list">
                        @forelse ($meetings as $item)
                            <a href="{{ $meetingsResource::getUrl('edit', ['record' => $item]) }}" class="mrdc-dash-card-item">
                                <div class="mrdc-dash-meeting-date">
                                    <strong>{{ $item->scheduled_date?->format('d') }}</strong>
                                    <span>{{ $item->scheduled_date?->format('M') }}</span>
                                </div>
                                <div class="mrdc-dash-item-meta">
                                    <strong>{{ $item->title }}</strong>
                                    <small>{{ $item->scheduled_time ?? '09:00 AM' }} @if ($item->venue) · {{ $item->venue }} @endif</small>
                                </div>
                            </a>
                        @empty
                            <p class="mrdc-dash-empty">No upcoming council meetings scheduled.</p>
                        @endforelse
                    </div>
                </section>

                {{-- Recent Documents --}}
                <section class="mrdc-dash-panel">
                    <div class="mrdc-dash-panel-heading">
                        <h3>Recent Documents</h3>
                        @if ($documentsResource::canViewAny())
                            <a href="{{ $documentsResource::getUrl() }}" class="mrdc-view-all">View all &rarr;</a>
                        @endif
                    </div>
                    <div class="mrdc-dash-list">
                        @forelse ($documents as $item)
                            <a href="{{ $documentsResource::getUrl('edit', ['record' => $item]) }}" class="mrdc-dash-card-item">
                                <div class="mrdc-dash-doc-badge">
                                    <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="mrdc-dash-item-meta">
                                    <strong>{{ $item->title }}</strong>
                                    <small>PDF · {{ str_replace('_', ' ', ucfirst($item->category ?? 'Document')) }}</small>
                                </div>
                                <span class="mrdc-dash-status-pill mrdc-pill-green">{{ ucfirst($item->status) }}</span>
                            </a>
                        @empty
                            <p class="mrdc-dash-empty">No council documents uploaded yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Bottom Section: Development Projects & Enquiries Table --}}
            <div class="mrdc-dash-bottom">
                {{-- Development Projects --}}
                <section class="mrdc-dash-panel">
                    <div class="mrdc-dash-panel-heading">
                        <div>
                            <h3>Development Projects</h3>
                            <p>Key infrastructure and community initiatives</p>
                        </div>
                        @if ($projectsResource::canViewAny())
                            <a href="{{ $projectsResource::getUrl() }}" class="mrdc-view-all">View all &rarr;</a>
                        @endif
                    </div>
                    <div class="mrdc-dash-projects">
                        @forelse ($projects as $item)
                            <a href="{{ $projectsResource::getUrl('edit', ['record' => $item]) }}" class="mrdc-dash-project-card">
                                <div class="mrdc-dash-project-image" @if ($item->featuredMedia?->status === 'active') style="background-image: url('{{ public_route('managed-media.show', $item->featured_media_id) }}')" @endif>
                                    <span class="mrdc-dash-project-badge mrdc-badge-{{ $item->project_status ?? 'progress' }}">
                                        {{ str_replace('_', ' ', ucfirst($item->project_status ?? 'In progress')) }}
                                    </span>
                                </div>
                                <div class="mrdc-dash-project-body">
                                    <strong class="mrdc-dash-project-title">{{ $item->title }}</strong>
                                    <small class="mrdc-dash-project-loc">
                                        <svg class="w-3.5 h-3.5 inline mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                        {{ $item->location ?: 'Mutoko Rural District' }}
                                    </small>
                                    <div class="mrdc-dash-progress-wrap">
                                        <div class="mrdc-dash-progress-bar">
                                            <div class="mrdc-dash-progress-fill" style="width: {{ min(100, max(0, (int) ($item->progress_percent ?? 50))) }}%"></div>
                                        </div>
                                        <span class="mrdc-dash-progress-text">
                                            {{ $item->progress_percent !== null ? "{$item->progress_percent}% complete" : '50% complete' }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="mrdc-dash-empty">No active development projects found.</p>
                        @endforelse
                    </div>
                </section>

                {{-- Enquiries & Service Requests Table --}}
                <section class="mrdc-dash-panel">
                    <div class="mrdc-dash-panel-heading">
                        <div>
                            <h3>Enquiries & Service Requests</h3>
                            <p>Recent citizen feedback and support inquiries</p>
                        </div>
                        @if ($enquiriesResource::canViewAny())
                            <a href="{{ $enquiriesResource::getUrl() }}" class="mrdc-view-all">View all &rarr;</a>
                        @endif
                    </div>
                    <div class="mrdc-dash-table-wrap">
                        <table class="mrdc-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($enquiries as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ $enquiriesResource::getUrl('view', ['record' => $item]) }}" class="mrdc-table-ref">
                                                {{ $item->public_id }}
                                            </a>
                                        </td>
                                        <td class="mrdc-table-subject">
                                            <span class="truncate block max-w-xs" title="{{ $item->subject }}">{{ $item->subject }}</span>
                                        </td>
                                        <td class="mrdc-table-date">{{ $item->submitted_at?->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $statusClass = match ($item->status) {
                                                    'resolved', 'closed' => 'mrdc-pill-green',
                                                    'in_progress' => 'mrdc-pill-blue',
                                                    default => 'mrdc-pill-orange',
                                                };
                                            @endphp
                                            <span class="mrdc-dash-status-pill {{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="mrdc-dash-empty">No citizen enquiries submitted yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        {{-- Aside Right Column: Quick Actions & Pending Approvals --}}
        <aside class="mrdc-dashboard-aside">
            {{-- Quick Actions --}}
            <section class="mrdc-dash-panel">
                <div class="mrdc-dash-panel-heading">
                    <h3>Quick Actions</h3>
                </div>
                <div class="mrdc-dash-actions">
                    @foreach ($quickActions as $action)
                        @if ($action['resource']::canCreate())
                            <a href="{{ $action['resource']::getUrl('create') }}" class="mrdc-action-btn mrdc-tone-{{ $action['tone'] }}">
                                <span class="mrdc-action-icon">{!! $action['icon_svg'] !!}</span>
                                <span class="mrdc-action-label">{{ $action['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>

            {{-- Pending Approvals --}}
            <section class="mrdc-dash-panel">
                <div class="mrdc-dash-panel-heading">
                    <h3>Pending Approvals</h3>
                    <span class="mrdc-dash-count-badge">{{ count($approvals) }}</span>
                </div>
                <div class="mrdc-dash-list mrdc-dash-approvals-list">
                    @forelse ($approvals as $item)
                        <a href="{{ $item['url'] }}" class="mrdc-dash-approval-item">
                            <div class="mrdc-approval-meta">
                                <span class="mrdc-approval-tag mrdc-bg-{{ $item['tone'] }}">{{ $item['type'] }}</span>
                                <strong class="mrdc-approval-title">{{ $item['title'] }}</strong>
                                <small class="mrdc-approval-date">{{ $item['date'] }}</small>
                            </div>
                            <span class="mrdc-dash-status-pill mrdc-pill-orange">
                                {{ $item['status'] }}
                            </span>
                        </a>
                    @empty
                        <p class="mrdc-dash-empty">No items awaiting review.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</x-filament-panels::page>
