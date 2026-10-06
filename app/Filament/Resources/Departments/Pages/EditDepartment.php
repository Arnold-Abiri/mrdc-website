<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Domain\Identity\DepartmentManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Departments\DepartmentResource;
use App\Models\Department;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDepartment extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = DepartmentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Department) {
            throw new \LogicException('Invalid record.');
        }

        return app(DepartmentManager::class)->update(auth()->user(), $record, $data);
    }
}
