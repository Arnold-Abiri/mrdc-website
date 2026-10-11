<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Domain\Cms\FaqManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFaq extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = FaqResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(FaqManager::class)->create(auth()->user(), $data);
    }
}
