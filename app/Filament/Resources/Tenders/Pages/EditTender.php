<?php

namespace App\Filament\Resources\Tenders\Pages;

use App\Domain\Cms\TenderManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Tenders\TenderResource;
use App\Models\Tender;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditTender extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = TenderResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Tender, 404);

        $updatedRecord = app(TenderManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            ActionGroup::make([
                Action::make('verify')
                    ->label('Mark publishable')
                    ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                    ->visible(fn (): bool => $this->tender()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(TenderManager::class)->setVerification(auth()->user(), $this->tender(), 'publishable');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->tender()->status !== 'published')
                    ->disabled(fn (): bool => $this->tender()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->tender()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(TenderManager::class)->setStatus(auth()->user(), $this->tender(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->tender()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(TenderManager::class)->setStatus(auth()->user(), $this->tender(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            PreviewHelper::makePreviewAction(),
            DeleteAction::make(),
        ];
    }

    private function tender(): Tender
    {
        abort_unless($this->record instanceof Tender, 404);

        return $this->record;
    }
}
