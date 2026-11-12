<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    /** @return array<mixed> */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All Settings'),
        ];

        $groups = SiteSetting::query()
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        foreach ($groups as $group) {
            $tabs[$group] = Tab::make(ucwords(str_replace('_', ' ', (string) $group)))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('group', $group));
        }

        return $tabs;
    }
}
