<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Domain\Cms\EditorialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Editorial\EditorialItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEditorialItem extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = EditorialItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineFeaturedImage($data, $data['title'] ?? null);

        return app(EditorialManager::class)->create(auth()->user(), $data);
    }
}
