<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Domain\Cms\DocumentManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDocument extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = DocumentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'document_file_upload',
            targetKey: 'media_id',
            fallbackTitle: $data['title'] ?? null,
            titleSuffix: ' document',
        );

        return app(DocumentManager::class)->create(auth()->user(), $data);
    }
}
