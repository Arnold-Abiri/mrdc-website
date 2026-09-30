<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Domain\Identity\RoleManager;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! $this->record instanceof Role) {
            throw new \LogicException('Invalid role record.');
        } $data['permission_names'] = $this->record->permissions()->pluck('name')->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Role) {
            throw new \LogicException('Invalid role record.');
        } app(RoleManager::class)->syncPermissions(auth()->user(), $record, $data['permission_names'] ?? []);

        return $record;
    }
}
