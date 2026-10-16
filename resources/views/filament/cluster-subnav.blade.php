@php
    use Filament\Facades\Filament;
    $subnavItems = [];
    $clusterLabel = null;
    try {
        foreach (Filament::getNavigation() as $group) {
            $items = $group->getItems();
            foreach ($items as $item) {
                $children = $item->getChildItems();
                if (empty($children)) {
                    continue;
                }
                $isClusterActive = $item->isActive();
                foreach ($children as $child) {
                    if ($child->isActive()) {
                        $isClusterActive = true;
                        break;
                    }
                }
                if ($isClusterActive) {
                    $clusterLabel = $item->getLabel();
                    foreach ($children as $child) {
                        if ($child->isHidden() || ! $child->isVisible()) {
                            continue;
                        }
                        $subnavItems[] = [
                            'label' => $child->getLabel(),
                            'url' => $child->getUrl(),
                            'active' => $child->isActive(),
                            'badge' => $child->getBadge(),
                            'badgeColor' => $child->getBadgeColor(),
                            'icon' => $child->getIcon(),
                        ];
                    }
                    break 2;
                }
            }
        }
    } catch (\Throwable $e) {
        $subnavItems = [];
    }
@endphp

@if(!empty($subnavItems) && in_array($clusterLabel, [\App\Filament\Clusters\Enquiries\EnquiriesCluster::getNavigationLabel(), \App\Filament\Clusters\Content\ContentCluster::getNavigationLabel(), \App\Filament\Clusters\Council\CouncilCluster::getNavigationLabel(), \App\Filament\Clusters\Services\ServicesCluster::getNavigationLabel(), \App\Filament\Clusters\Reports\ReportsCluster::getNavigationLabel(), \App\Filament\Clusters\Administration\AdministrationCluster::getNavigationLabel()], true))
    <div class="fi-cluster-subnav mb-4 rounded-xl border border-gray-200 bg-white px-2 py-2 shadow-sm dark:border-white/10 dark:bg-zinc-900">
        <div class="flex items-center gap-1 overflow-x-auto">
            <span class="hidden sm:inline-flex shrink-0 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $clusterLabel }}</span>
            @foreach($subnavItems as $link)
                <a href="{{ $link['url'] }}"
                   @class([
                       'flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                       'bg-primary-50 text-primary-700' => $link['active'],
                       'text-gray-500 hover:bg-gray-100 dark:text-zinc-300 dark:hover:bg-white/10' => !$link['active'],
                   ])>
                    @if(!empty($link['icon']))
                        <x-filament::icon :icon="$link['icon']" class="h-5 w-5" />
                    @endif
                    {{ $link['label'] }}
                    @if(!empty($link['badge']))
                        <span @class([
                            'rounded px-1.5 py-0.5 text-[11px] font-semibold',
                            'bg-orange-100 text-amber-800' => $link['active'],
                            'bg-amber-50 text-amber-700' => !$link['active'],
                        ])>{{ $link['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
