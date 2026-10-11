<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Domain\Cms\FaqManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Faqs\FaqResource;
use App\Models\Faq;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditFaq extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = FaqResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Faq, 404);

        return app(FaqManager::class)->update(auth()->user(), $record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->using(fn (Faq $record) => app(FaqManager::class)->delete(auth()->user(), $record)),
        ];
    }
}
