<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Domain\Cms\PageManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use App\Models\PageRevision;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPage extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restoreRevision')
                ->label('Restore revision')
                ->authorize(fn (): bool => auth()->user()?->can('update', $this->record) ?? false)
                ->form([
                    Select::make('revision_id')
                        ->label('Previous revision')
                        ->options(fn (): array => $this->pageRecord()->revisions()->orderByDesc('number')->pluck('number', 'id')->mapWithKeys(fn ($number, $id): array => [$id => 'Revision '.$number])->all())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $revision = PageRevision::query()->findOrFail($data['revision_id']);
                    app(PageManager::class)->restore(auth()->user(), $this->pageRecord(), $revision);
                    $this->pageRecord()->refresh();
                    $this->fillForm();
                }),
        ];
    }

    private function pageRecord(): Page
    {
        if (! $this->record instanceof Page) {
            throw new \LogicException('Invalid record.');
        }

        return $this->record;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Page) {
            throw new \LogicException('Invalid record.');
        }

        return app(PageManager::class)->update(auth()->user(), $record, $data);
    }
}
