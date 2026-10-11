<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Domain\Cms\MediaManager;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    /** @param  array<string, mixed>  $data */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($record->key === 'logo_media_id') {
            $file = $data['logo_file'] ?? null;

            if (! $file instanceof UploadedFile) {
                return $record;
            }

            $media = app(MediaManager::class)->upload(auth()->user(), $file, [
                'title' => 'Council logo',
                'alt_text' => 'Mutoko Rural District Council logo',
            ]);

            $record->update(['value' => (string) $media->id]);

            return $record;
        }

        // Textarea variant stores into value_textarea; fold it back into value.
        if (array_key_exists('value_textarea', $data) && ($data['value_textarea'] ?? null) !== null) {
            $data['value'] = $data['value_textarea'];
        }
        unset($data['value_textarea']);
        $record->update(['value' => $data['value'] ?? null]);

        return $record;
    }
}
