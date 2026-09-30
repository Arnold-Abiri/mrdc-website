<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Domain\Identity\DepartmentManager;
use App\Filament\Resources\Departments\DepartmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(DepartmentManager::class)->create(auth()->user(), $data);
    }
}
