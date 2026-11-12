<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Domain\Cms\DocumentManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditDocument extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = DocumentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Document, 404);

        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'document_file_upload',
            targetKey: 'media_id',
            fallbackTitle: $data['title'] ?? $record->title,
            titleSuffix: ' document',
        );

        $updatedRecord = app(DocumentManager::class)->update(auth()->user(), $record, $data);
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
                    ->visible(fn (): bool => $this->document()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(DocumentManager::class)->setVerification(auth()->user(), $this->document(), 'publishable');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->document()->status !== 'published')
                    ->disabled(fn (): bool => $this->document()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->document()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(DocumentManager::class)->setStatus(auth()->user(), $this->document(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->document()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(DocumentManager::class)->setStatus(auth()->user(), $this->document(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            PreviewHelper::makePreviewAction(),
            DeleteAction::make(),
        ];
    }

    private function document(): Document
    {
        abort_unless($this->record instanceof Document, 404);

        return $this->record;
    }
}
