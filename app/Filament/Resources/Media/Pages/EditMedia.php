<?php

namespace App\Filament\Resources\Media\Pages;

use App\Domain\Cms\MediaManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    use HandlesTranslations;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Media, 404);

        $updatedRecord = app(MediaManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            Action::make('preview')
                ->label('Preview Media')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->url(fn (Media $record): string => route('admin.media.preview', $record->id))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
