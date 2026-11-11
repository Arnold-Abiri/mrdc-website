<?php

namespace App\Filament\Resources\PublicContacts\Pages;

use App\Domain\Cms\PublicContactManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PublicContacts\PublicContactResource;
use App\Models\PublicContact;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditPublicContact extends EditRecord
{
    protected static string $resource = PublicContactResource::class;

    use HandlesTranslations;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof PublicContact, 404);

        $updatedRecord = app(PublicContactManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Mark publishable')
                ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                ->visible(fn (): bool => $this->publicContact()->verification_status !== 'publishable')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(PublicContactManager::class)->setVerification(auth()->user(), $this->publicContact(), 'publishable');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('publish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->publicContact()->status !== 'published')
                ->disabled(fn (): bool => $this->publicContact()->verification_status !== 'publishable')
                ->tooltip(fn (): ?string => $this->publicContact()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                ->requiresConfirmation()
                ->action(function (): void {
                    app(PublicContactManager::class)->setStatus(auth()->user(), $this->publicContact(), 'published');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('unpublish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->publicContact()->status === 'published')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(PublicContactManager::class)->setStatus(auth()->user(), $this->publicContact(), 'unpublished');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            self::makeAiTranslateAction(),
            DeleteAction::make(),
        ];
    }

    private function publicContact(): PublicContact
    {
        abort_unless($this->record instanceof PublicContact, 404);

        return $this->record;
    }
}
