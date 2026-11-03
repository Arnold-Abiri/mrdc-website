<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Domain\Cms\GalleryManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\Gallery;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditGallery extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = GalleryResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Gallery, 404);

        $updatedRecord = app(GalleryManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Mark publishable')
                ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                ->visible(fn (): bool => $this->gallery()->verification_status !== 'publishable')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(GalleryManager::class)->setVerification(auth()->user(), $this->gallery(), 'publishable');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('publish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->gallery()->status !== 'published')
                ->disabled(fn (): bool => $this->gallery()->verification_status !== 'publishable')
                ->tooltip(fn (): ?string => $this->gallery()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                ->requiresConfirmation()
                ->action(function (): void {
                    app(GalleryManager::class)->setStatus(auth()->user(), $this->gallery(), 'published');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('unpublish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->gallery()->status === 'published')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(GalleryManager::class)->setStatus(auth()->user(), $this->gallery(), 'unpublished');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            DeleteAction::make(),
        ];
    }

    private function gallery(): Gallery
    {
        abort_unless($this->record instanceof Gallery, 404);

        return $this->record;
    }
}
