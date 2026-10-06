<?php

namespace App\Filament\Resources\Vacancies\Pages;

use App\Domain\Cms\VacancyManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Vacancies\VacancyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVacancy extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = VacancyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(VacancyManager::class)->create(auth()->user(), $data);
    }
}
