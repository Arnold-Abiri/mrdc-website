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
        return $this->attachInlineMedia(
            $data,
            uploadKey: 'featured_image_upload',
            targetKey: 'featured_media_id',
            altKey: 'featured_image_alt',
            captionKey: 'featured_image_caption',
            fallbackTitle: $fallbackTitle,
            titleSuffix: ' image',
        );
    }

    /**
     * Attach an inline uploaded file to a specified media ID field on the model data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function attachInlineMedia(
        array $data,
        string $uploadKey = 'media_upload',
        string $targetKey = 'media_id',
        ?string $altKey = null,
        ?string $captionKey = null,
        ?string $fallbackTitle = null,
        string $titleSuffix = ' file',
    ): array {
        $file = $data[$uploadKey] ?? null;
        $alt = $altKey ? ($data[$altKey] ?? null) : null;
        $caption = $captionKey ? ($data[$captionKey] ?? null) : null;

        unset($data[$uploadKey]);
        if ($altKey) {
            unset($data[$altKey]);
        }
        if ($captionKey) {
            unset($data[$captionKey]);
        }

        if ($file === null || $file === false) {
            return $data;
        }

        $media = app(MediaManager::class)->upload(auth()->user(), $file, [
            'title' => trim((string) ($fallbackTitle ?? 'Inline upload')).$titleSuffix,
            'alt_text' => $alt,
            'caption' => $caption,
            'department_id' => $data['department_id'] ?? null,
        ]);
        $data[$targetKey] = $media->id;

        return $data;
    }

    public static function slugFromTitle(?string $title): string
    {
        $slug = Str::slug((string) $title);

        return $slug === '' ? '' : substr($slug, 0, 160);
    }
}
