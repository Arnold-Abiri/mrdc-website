<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Slides\HomepageSlideResource;
use App\Models\HomepageSlide;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHomepageSlide extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = HomepageSlideResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof HomepageSlide, 404);

        return app(HomepageSlideManager::class)->update(auth()->user(), $record, $data);
    }
}
