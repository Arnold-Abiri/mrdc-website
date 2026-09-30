<?php

namespace App\Filament\Pages;

use App\Domain\Identity\AuditWriter;
use App\Models\User;
use Filament\Auth\Pages\EditProfile;
use Illuminate\Database\Eloquent\Model;
use SensitiveParameter;

class Profile extends EditProfile
{
    protected function handleRecordUpdate(Model $record, #[SensitiveParameter] array $data): Model
    {
        $previousName = $record->getAttribute('name');
        $previousEmail = $record->getAttribute('email');
        $previousPassword = $record->getAttribute('password');
        $updated = parent::handleRecordUpdate($record, $data);

        if ($updated instanceof User) {
            if ($updated->name !== $previousName || $updated->email !== $previousEmail) {
                app(AuditWriter::class)->record($updated, 'user.profile_updated', $updated);
            }
            if ($updated->password !== $previousPassword) {
                app(AuditWriter::class)->record($updated, 'auth.password_changed', $updated);
            }
        }

        return $updated;
    }
}
