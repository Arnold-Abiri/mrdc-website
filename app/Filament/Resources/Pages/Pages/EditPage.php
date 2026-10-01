<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Domain\Cms\PageManager;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Page) {
            throw new \LogicException('Invalid record.');
        }

        return app(PageManager::class)->update(auth()->user(), $record, $data);
    }
}
