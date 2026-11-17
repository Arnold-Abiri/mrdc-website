<?php

namespace App\Http\Resources;

use App\Support\PublicApiCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicContentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $definition = PublicApiCatalog::all()[$request->route('resource')];

        $data = $this->resource->toLocalizedArray($definition['fields'], $request->query('locale', 'en'));
        if (array_key_exists('progress_percent', $data) && $data['progress_percent'] !== null) {
            $data['progress_percent'] = (int) $data['progress_percent'];
        }

        if ($request->route('resource') === 'galleries') {
            $locale = $request->query('locale', 'en');
            $data['images'] = $this->resource->media->map(fn ($media): array => [
                'url' => route('managed-media.show', ['locale' => $locale, 'media' => $media->id]),
                'alt' => $media->translated('alt_text', $locale),
                'caption' => $media->pivot->caption ?: $media->translated('caption', $locale),
            ])->values()->all();
        }

        return $data;
    }
}
