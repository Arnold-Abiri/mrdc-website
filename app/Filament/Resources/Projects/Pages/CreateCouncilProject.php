<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Domain\Cms\CouncilProjectManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Projects\CouncilProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCouncilProject extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = CouncilProjectResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineFeaturedImage($data, $data['title'] ?? null);

        return app(CouncilProjectManager::class)->create(auth()->user(), $data);
    }
}
