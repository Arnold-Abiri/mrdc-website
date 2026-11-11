<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Domain\Cms\PageManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use App\Models\PageRevision;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditPage extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewHelper::makePreviewAction(),
            ActionGroup::make([
                Action::make('markPublishable')
                    ->label('Mark publishable')
                    ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                    ->visible(fn (): bool => $this->pageRecord()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(PageManager::class)->setVerification(auth()->user(), $this->pageRecord(), 'publishable');
                        $this->record = $this->pageRecord()->refresh();
                        $this->fillForm();
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->pageRecord()->status !== 'published')
                    ->disabled(fn (): bool => app()->environment('production') && $this->pageRecord()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(PageManager::class)->setStatus(auth()->user(), $this->pageRecord(), 'published');
                        $this->record = $this->pageRecord()->refresh();
                        $this->fillForm();
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->pageRecord()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(PageManager::class)->setStatus(auth()->user(), $this->pageRecord(), 'unpublished');
                        $this->record = $this->pageRecord()->refresh();
                        $this->fillForm();
                    }),
            ])->label('Publishing')->button()->outlined(),
            DeleteAction::make(),
            self::makeAiTranslateAction(),
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
                    $this->record = $this->pageRecord()->refresh();
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

        $updatedRecord = app(PageManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }
}
