<?php

namespace App\Filament\Resources\Wards\Pages;

use App\Domain\Cms\WardManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Wards\WardResource;
use App\Models\Ward;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditWard extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = WardResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Ward, 404);

        $updatedRecord = app(WardManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Mark publishable')
                ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                ->visible(fn (): bool => $this->ward()->verification_status !== 'publishable')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(WardManager::class)->setVerification(auth()->user(), $this->ward(), 'publishable');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('publish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->ward()->status !== 'published')
                ->disabled(fn (): bool => $this->ward()->verification_status !== 'publishable')
                ->tooltip(fn (): ?string => $this->ward()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                ->requiresConfirmation()
                ->action(function (): void {
                    app(WardManager::class)->setStatus(auth()->user(), $this->ward(), 'published');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('unpublish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->ward()->status === 'published')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(WardManager::class)->setStatus(auth()->user(), $this->ward(), 'unpublished');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            DeleteAction::make(),
        ];
    }

    private function ward(): Ward
    {
        abort_unless($this->record instanceof Ward, 404);

        return $this->record;
    }
}
