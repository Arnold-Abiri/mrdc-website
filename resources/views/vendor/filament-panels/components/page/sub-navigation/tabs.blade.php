@props([
    'navigation',
])

@php
    use Illuminate\Contracts\Support\Htmlable;
@endphp

<div
    {{ \Filament\Support\prepare_inherited_attributes($attributes)->class(['fi-cluster-tabs mb-4 rounded-xl border border-gray-200 bg-white px-2 py-2 shadow-sm dark:border-white/10 dark:bg-zinc-900']) }}
>
    <div class="flex items-center gap-1 overflow-x-auto">
        @foreach ($navigation as $navigationGroup)
            @foreach ($navigationGroup->getItems() as $navigationItem)
                @php
                    $isNavigationItemActive = $navigationItem->isActive();
                    $navigationItemBadge = $navigationItem->getBadge();
                    $navigationItemIcon = $navigationItem->isActive() ? ($navigationItem->getActiveIcon() ?? $navigationItem->getIcon()) : $navigationItem->getIcon();
                    $navigationItemUrl = $navigationItem->getUrl();
                    $shouldNavigationItemOpenUrlInNewTab = $navigationItem->shouldOpenUrlInNewTab();
                    $navigationItemExtraAttributes = $navigationItem->getExtraAttributeBag();
                @endphp

                <a
                    href="{{ $navigationItemUrl }}"
                    @if ($shouldNavigationItemOpenUrlInNewTab) target="_blank" @endif
                    @class([
                        'flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-300' => $isNavigationItemActive,
                        'text-gray-500 hover:bg-gray-100 dark:text-zinc-300 dark:hover:bg-white/10' => ! $isNavigationItemActive,
                    ])
                    {{ \Filament\Support\prepare_inherited_attributes($navigationItemExtraAttributes) }}
                >
                    @if ($navigationItemIcon)
                        @if ($navigationItemIcon instanceof Htmlable)
                            {{ $navigationItemIcon }}
                        @else
                            <x-filament::icon :icon="$navigationItemIcon" class="h-5 w-5" />
                        @endif
                    @endif

                    {{ $navigationItem->getLabel() }}

                    @if (filled($navigationItemBadge))
                        <span @class([
                            'rounded px-1.5 py-0.5 text-[11px] font-semibold',
                            'bg-orange-100 text-amber-800' => $isNavigationItemActive,
                            'bg-amber-50 text-amber-700' => ! $isNavigationItemActive,
                        ])>{{ $navigationItemBadge }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </div>
</div>
