<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Slides\HomepageSlideResource;
use App\Models\HomepageSlide;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditHomepageSlide extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = HomepageSlideResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof HomepageSlide, 404);

        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'slide_image_upload',
            targetKey: 'media_id',
            altKey: 'slide_image_alt',
            fallbackTitle: $data['headline'] ?? $record->headline,
            titleSuffix: ' slide image',
        );

        $updatedRecord = app(HomepageSlideManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            ActionGroup::make([
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->slide()->status !== 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(HomepageSlideManager::class)->setStatus(auth()->user(), $this->slide(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->slide()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(HomepageSlideManager::class)->setStatus(auth()->user(), $this->slide(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            DeleteAction::make(),
        ];
    }

    private function slide(): HomepageSlide
    {
        abort_unless($this->record instanceof HomepageSlide, 404);

        return $this->record;
    }
}
