<?php

namespace App\Filament\Resources\Vacancies\Pages;

use App\Domain\Cms\VacancyManager;
use App\Filament\Resources\Vacancies\VacancyResource;
use App\Models\Vacancy;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditVacancy extends EditRecord
{
    protected static string $resource = VacancyResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Vacancy, 404);

        return app(VacancyManager::class)->update(auth()->user(), $record, $data);
    }
}
