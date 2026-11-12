<?php

namespace App\Filament\Resources\ProjectUpdates\Pages;

use App\Domain\Cms\ProjectUpdateManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\ProjectUpdates\ProjectUpdateResource;
use App\Models\ProjectUpdate;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditProjectUpdate extends EditRecord
{
    protected static string $resource = ProjectUpdateResource::class;

    use HandlesTranslations;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof ProjectUpdate, 404);

        $updatedRecord = app(ProjectUpdateManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            ActionGroup::make([
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('update', $this->record))
                    ->visible(fn (): bool => $this->projectUpdate()->status !== 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(ProjectUpdateManager::class)->setStatus(auth()->user(), $this->projectUpdate(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('update', $this->record))
                    ->visible(fn (): bool => $this->projectUpdate()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(ProjectUpdateManager::class)->setStatus(auth()->user(), $this->projectUpdate(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            DeleteAction::make(),
        ];
    }

    private function projectUpdate(): ProjectUpdate
    {
        abort_unless($this->record instanceof ProjectUpdate, 404);

        return $this->record;
    }
}
