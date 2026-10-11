<?php

namespace App\Filament\Resources\SiteSettings\Pages;

use App\Filament\Resources\SiteSettings\SiteSettingResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    /** @param  array<string, mixed>  $data */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Textarea variant stores into value_textarea; fold it back into value.
        if (array_key_exists('value_textarea', $data) && ($data['value_textarea'] ?? null) !== null) {
            $data['value'] = $data['value_textarea'];
        }
        unset($data['value_textarea']);
        $record->update(['value' => $data['value'] ?? null]);

        return $record;
    }
}
