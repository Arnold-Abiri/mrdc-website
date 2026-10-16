<?php

namespace App\Filament\Clusters\Council;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class CouncilCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel = 'Council';

    protected static ?int $navigationSort = 3;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
