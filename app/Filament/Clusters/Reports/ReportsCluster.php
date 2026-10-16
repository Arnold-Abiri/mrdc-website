<?php

namespace App\Filament\Clusters\Reports;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class ReportsCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Reports & Monitoring';

    protected static ?int $navigationSort = 5;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
