<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domain\Identity\UserManager;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof User) {
            throw new \LogicException('Invalid record.');
        }

        return app(UserManager::class)->update(auth()->user(), $record, $data);
    }
}
