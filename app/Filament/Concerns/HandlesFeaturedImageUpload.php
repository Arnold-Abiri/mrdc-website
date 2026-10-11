<?php

namespace App\Filament\Concerns;

use App\Domain\Cms\MediaManager;
use Illuminate\Support\Str;

/**
 * Lets a content form upload its featured image inline instead of forcing
 * a separate trip to the Media library. Pops `featured_image_upload`,
 * `featured_image_alt` and `featured_image_caption` out of the form data,
 * stores the file through MediaManager, and sets `featured_media_id`.
 */
trait HandlesFeaturedImageUpload
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function attachInlineFeaturedImage(array $data, ?string $fallbackTitle = null): array
    {
        $file = $data['featured_image_upload'] ?? null;
        $alt = $data['featured_image_alt'] ?? null;
        $caption = $data['featured_image_caption'] ?? null;
        unset($data['featured_image_upload'], $data['featured_image_alt'], $data['featured_image_caption']);

        if ($file === null || $file === false) {
            return $data;
        }

        $media = app(MediaManager::class)->upload(auth()->user(), $file, [
            'title' => trim((string) ($fallbackTitle ?? 'Inline upload')).' image',
            'alt_text' => $alt,
            'caption' => $caption,
            'department_id' => $data['department_id'] ?? null,
        ]);
        $data['featured_media_id'] = $media->id;

        return $data;
    }

    public static function slugFromTitle(?string $title): string
    {
        $slug = Str::slug((string) $title);

        return $slug === '' ? '' : substr($slug, 0, 160);
    }
}
