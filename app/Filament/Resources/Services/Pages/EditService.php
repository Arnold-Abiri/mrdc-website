<?php

namespace App\Filament\Resources\Services\Pages;

use App\Domain\Cms\ServiceManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditService extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = ServiceResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Service, 404);

        $updatedRecord = app(ServiceManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('verify')
                    ->label('Mark publishable')
                    ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                    ->visible(fn (): bool => $this->service()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(ServiceManager::class)->setVerification(auth()->user(), $this->service(), 'publishable');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->service()->status !== 'published')
                    ->disabled(fn (): bool => $this->service()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->service()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(ServiceManager::class)->setStatus(auth()->user(), $this->service(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->service()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(ServiceManager::class)->setStatus(auth()->user(), $this->service(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            PreviewHelper::makePreviewAction(),
            DeleteAction::make(),
        ];
    }

    private function service(): Service
    {
        abort_unless($this->record instanceof Service, 404);

        return $this->record;
    }
}
