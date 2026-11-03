<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Domain\Cms\EditorialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Models\EditorialItem;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditEditorialItem extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = EditorialItemResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof EditorialItem, 404);

        $data = $this->attachInlineFeaturedImage($data, $data['title'] ?? $record->title);

        return app(EditorialManager::class)->update(auth()->user(), $record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Mark publishable')
                ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                ->visible(fn (): bool => $this->editorialItem()->verification_status !== 'publishable')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(EditorialManager::class)->setVerification(auth()->user(), $this->editorialItem(), 'publishable');
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('publish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->editorialItem()->status !== 'published')
                ->disabled(fn (): bool => $this->editorialItem()->verification_status !== 'publishable')
                ->tooltip(fn (): ?string => $this->editorialItem()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                ->requiresConfirmation()
                ->action(function (): void {
                    app(EditorialManager::class)->setStatus(auth()->user(), $this->editorialItem(), 'published');
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('unpublish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->editorialItem()->status === 'published')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(EditorialManager::class)->setStatus(auth()->user(), $this->editorialItem(), 'unpublished');
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            PreviewHelper::makePreviewAction(),
            DeleteAction::make(),
        ];
    }

    private function editorialItem(): EditorialItem
    {
        abort_unless($this->record instanceof EditorialItem, 404);

        return $this->record;
    }
}
