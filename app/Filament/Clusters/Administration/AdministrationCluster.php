<?php

namespace App\Filament\Clusters\Administration;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class AdministrationCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Administration';

    protected static ?int $navigationSort = 6;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
