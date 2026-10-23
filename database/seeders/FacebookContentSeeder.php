<?php

namespace Database\Seeders;

use App\Models\DistrictStatistic;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FacebookContentSeeder extends Seeder
{
    /** @var list<string> */
    private array $createdFiles = [];

    private string $assetRoot;

    /** @return array<string, array{create: int, skip: int}> */
    public function import(bool $apply = false, bool $includeStatistics = false, ?string $manifestPath = null, ?string $assetRoot = null): array
    {
        $this->assetRoot = realpath($assetRoot ?? base_path('database/seeders/assets/facebook')) ?: throw new RuntimeException('Media source directory is missing.');
        $manifest = json_decode(file_get_contents($manifestPath ?? $this->assetRoot.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
        $report = [];
        foreach (['public_contacts', 'district_statistics', 'editorial_items', 'galleries'] as $group) {
            $report[$group] = ['create' => 0, 'skip' => 0];
        }
        $this->createdFiles = [];

        try {
            DB::transaction(function () use ($manifest, $apply, $includeStatistics, &$report): void {
                // Contact details require separate council confirmation; preserve the existing directory.
                $report['public_contacts']['skip'] = count($manifest['public_contacts'] ?? []);

                foreach ($manifest['district_statistics'] ?? [] as $statistic) {
                    if (! $includeStatistics) {
                        $report['district_statistics']['skip']++;

                        continue;
                    }
                    if (DistrictStatistic::query()->where('label', $statistic['label'])->exists()) {
                        $report['district_statistics']['skip']++;

                        continue;
                    }
                    $report['district_statistics']['create']++;
                    if ($apply) {
                        DistrictStatistic::query()->create([
                            'label' => $statistic['label'], 'value' => $statistic['value'], 'unit' => $statistic['unit'],
                            'icon' => $statistic['icon'], 'display_order' => $statistic['display_order'],
                            'source_note' => 'Unverified historical claim; source: '.$manifest['directory_source'].'; last verified: unknown',
                            'is_active' => false,
                        ]);
                    }
                }

                foreach ($manifest['editorial_items'] ?? [] as $order => $item) {
                    if (EditorialItem::query()->where('slug', $item['slug'])->exists()) {
                        $report['editorial_items']['skip']++;

                        continue;
                    }
                    $report['editorial_items']['create']++;
                    if ($apply) {
                        $mediaId = isset($item['image']) ? $this->importMedia($item['image'], $item['title'], $item['alt_text'] ?? $item['title'], $item['caption'] ?? '') : null;
                        EditorialItem::query()->create([
                            'slug' => $item['slug'], 'type' => $item['type'] ?? 'news', 'title' => $item['title'],
                            'summary' => $item['summary'], 'body' => $item['body'], 'category' => $item['category'] ?? 'general',
                            'featured_media_id' => $mediaId, 'display_order' => $order,
                        ]);
                    }
                }

                foreach ($manifest['galleries'] ?? [] as $order => $item) {
                    if (Gallery::query()->where('slug', $item['slug'])->exists()) {
                        $report['galleries']['skip']++;

                        continue;
                    }
                    $report['galleries']['create']++;
                    if (! $apply) {
                        continue;
                    }
                    $images = [];
                    foreach ($item['images'] ?? [] as $imageOrder => $image) {
                        $id = $this->importMedia($image['filename'], $image['title'], $image['alt_text'] ?? $image['title'], $image['caption'] ?? '');
                        if (isset($images[$id])) {
                            throw new RuntimeException('Duplicate gallery image: '.$image['filename']);
                        }
                        $images[$id] = ['caption' => $image['caption'] ?? '', 'display_order' => $imageOrder];
                    }
                    if ($images === []) {
                        throw new RuntimeException('Gallery has no valid images: '.$item['slug']);
                    }
                    $gallery = Gallery::query()->create([
                        'slug' => $item['slug'], 'title' => $item['title'], 'summary' => $item['summary'],
                        'description' => $item['description'], 'location' => $item['location'] ?? null,
                        'cover_media_id' => array_key_first($images), 'display_order' => $order, 'is_featured' => false,
                    ]);
                    $gallery->media()->attach($images);
                }
            });
        } catch (\Throwable $exception) {
            $disk = Storage::disk(config('cms.media_disk', 'local'));
            foreach ($this->createdFiles as $path) {
                $disk->delete($path);
            }
            throw $exception;
        }

        return $report;
    }

    public function run(): void
    {
        throw new RuntimeException('Use mrdc:import-facebook --dry-run or --apply; this seeder cannot run implicitly.');
    }

    private function importMedia(string $relativePath, string $title, string $altText, string $caption): int
    {
        if (! preg_match('~\\A[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_.-]+)+\\z~D', $relativePath)
            || str_contains($relativePath, '..')) {
            throw new RuntimeException('Unsafe media path: '.$relativePath);
        }
        $root = $this->assetRoot;
        $source = realpath($root.'/'.$relativePath);
        if ($source === false || ! str_starts_with($source, $root.DIRECTORY_SEPARATOR) || ! is_file($source)) {
            throw new RuntimeException('Missing media: '.$relativePath);
        }
        $image = @getimagesize($source);
        $mime = $image['mime'] ?? null;
        $expected = match (strtolower(pathinfo($source, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', default => null,
        };
        if ($expected === null || $mime !== $expected) {
            throw new RuntimeException('Invalid image: '.$relativePath);
        }
        $path = 'facebook/'.$relativePath;
        $existing = Media::query()->where('storage_path', $path)->first();
        if ($existing) {
            return $existing->id;
        }
        $disk = Storage::disk(config('cms.media_disk', 'local'));
        if ($disk->exists($path)) {
            throw new RuntimeException('Unregistered media file already exists: '.$path);
        }
        $contents = file_get_contents($source);
        if ($contents === false) {
            throw new RuntimeException('Failed to read media: '.$relativePath);
        }
        try {
            if (! $disk->put($path, $contents)) {
                throw new RuntimeException('Failed to store media: '.$path);
            }
        } catch (\Throwable $exception) {
            $disk->delete($path);
            throw $exception;
        }
        $this->createdFiles[] = $path;

        return Media::unguarded(fn () => Media::query()->create([
            'title' => $title, 'alt_text' => $altText, 'caption' => $caption,
            'original_filename' => basename($relativePath), 'storage_path' => $path,
            'mime_type' => $mime, 'size' => strlen($contents), 'width' => $image[0], 'height' => $image[1],
            'status' => 'active',
        ])->id);
    }
}
