<?php

namespace App\Filament\Resources\Officials\Pages;

use App\Domain\Cms\OfficialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Officials\OfficialResource;
use App\Models\Official;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditOfficial extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = OfficialResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Official, 404);

        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'photo_upload',
            targetKey: 'photo_media_id',
            altKey: 'photo_alt',
            fallbackTitle: $data['name'] ?? $record->name,
            titleSuffix: ' portrait',
        );

        $updatedOfficial = app(OfficialManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedOfficial->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            PreviewHelper::makePreviewAction(),
            ActionGroup::make([
                Action::make('verify')
                    ->label('Mark publishable')
                    ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                    ->visible(fn (): bool => $this->official()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        $this->refreshOfficial();
                        app(OfficialManager::class)->setVerification(auth()->user(), $this->official(), 'publishable');
                        $this->refreshOfficial();
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->official()->status !== 'published')
                    ->disabled(fn (): bool => $this->official()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->official()->verification_status !== 'publishable' ? 'Mark this official publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(OfficialManager::class)->setStatus(auth()->user(), $this->official(), 'published');
                        $this->refreshOfficial();
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->official()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(OfficialManager::class)->setStatus(auth()->user(), $this->official(), 'unpublished');
                        $this->refreshOfficial();
                    }),
            ])->label('Publishing')->button()->outlined(),
            DeleteAction::make(),
        ];
    }

    private function official(): Official
    {
        abort_unless($this->record instanceof Official, 404);

        return $this->record;
    }

    private function refreshOfficial(): void
    {
        $this->record = $this->official()->refresh();
        $this->refreshFormData(['status', 'verification_status', 'published_at']);
    }
}
