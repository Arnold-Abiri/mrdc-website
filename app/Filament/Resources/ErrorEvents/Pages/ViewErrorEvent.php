<?php

namespace App\Filament\Resources\ErrorEvents\Pages;

use App\Filament\Resources\ErrorEvents\ErrorEventResource;
use Filament\Resources\Pages\ViewRecord;

class ViewErrorEvent extends ViewRecord
{
    protected static string $resource = ErrorEventResource::class;
}
