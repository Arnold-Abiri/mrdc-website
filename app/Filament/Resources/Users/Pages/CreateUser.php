<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domain\Identity\UserManager;
use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(UserManager::class)->create(auth()->user(), $data);
        } catch (ValidationException $exception) {
            Notification::make()
                ->title('User not created')
                ->body(collect($exception->errors())->flatten()->first() ?? 'Check the form and try again.')
                ->danger()
                ->persistent()
                ->send();

            throw (new Halt)->rollBackDatabaseTransaction();
        } catch (Throwable $exception) {
            report($exception);

            $message = match (true) {
                $exception instanceof TransportExceptionInterface && str_contains($exception->getMessage(), 'email limit is reached') => 'The email service has reached its sending limit. Ask an administrator to restore mail delivery, then try again.',
                $exception instanceof TransportExceptionInterface => 'The password setup email could not be sent. Ask an administrator to check the mail service, then try again.',
                default => 'The account could not be created. Contact an administrator with the time of this attempt.',
            };

            Notification::make()
                ->title('User not created')
                ->body($message)
                ->danger()
                ->persistent()
                ->send();

            throw (new Halt)->rollBackDatabaseTransaction();
        }
    }
}
