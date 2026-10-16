<?php

namespace App\Filament\Clusters\Services;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class ServicesCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Services & Development';

    protected static ?int $navigationSort = 4;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
