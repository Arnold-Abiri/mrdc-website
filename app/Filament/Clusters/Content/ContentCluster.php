<?php

namespace App\Filament\Clusters\Content;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class ContentCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
