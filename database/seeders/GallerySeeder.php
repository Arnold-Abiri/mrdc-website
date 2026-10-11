<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * A sourced public photo gallery and demonstration albums.
 *
 * The Tere Ruins album uses a documented CC BY-SA 4.0 photograph and is
 * public. Demonstration albums remain draft until council review.
 *
 * Demonstration album images reuse the illustrations imported by
 * NewsIllustrationSeeder.
 * Idempotent: stable slugs, and existing administrator edits are preserved.
 */
class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTereRuinsGallery();
        $this->call(NewsIllustrationSeeder::class);

        $images = Media::query()
            ->where('status', 'active')
            ->where('storage_path', 'like', 'news/illustrations/%')
            ->orderBy('id')
            ->get();

        if ($images->count() < 4) {
            return;
        }

        $this->seedGallery(
            'community-life-mutoko',
            'Community life across Mutoko',
            'Everyday moments from wards across Mutoko district.',
            "A selection of imagery representing community life, agriculture and service delivery across Mutoko district.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            'Mutoko District',
            true,
            $images->take(6)->values(),
        );

        $this->seedGallery(
            'council-service-delivery',
            'Council service delivery in the field',
            'Roads, water and environmental work delivered by council teams.',
            "Field imagery representing routine council work such as roads maintenance, water provision and environmental stewardship.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            'Mutoko District',
            false,
            $images->slice(2, 4)->values(),
        );
    }

    private function seedTereRuinsGallery(): void
    {
        $filename = 'tere-ruin-mutoko.jpg';
        $source = base_path('database/seeders/assets/'.$filename);
        $dimensions = is_file($source) ? getimagesize($source) : false;
        if ($dimensions === false) {
            return;
        }

        $storagePath = 'gallery/'.$filename;
        $disk = Storage::disk(config('cms.media_disk', 'local'));
        if (! $disk->exists($storagePath) && ! $disk->put($storagePath, file_get_contents($source))) {
            return;
        }

        $media = Media::query()->where('storage_path', $storagePath)->first();
        if (! $media) {
            $media = Media::unguarded(fn (): Media => Media::query()->create([
                'title' => 'Tere Ruins, Mutoko',
                'alt_text' => 'Stone walls of Tere Ruins near Mutoko, with granite hills in the background',
                'caption' => 'Photo: Robert Stewart Burrett / Wikimedia Commons. CC BY-SA 4.0. Source: https://commons.wikimedia.org/wiki/File:Tere_Ruin_Mutoko.jpg',
                'original_filename' => $filename,
                'storage_path' => $storagePath,
                'mime_type' => 'image/jpeg',
                'size' => $disk->size($storagePath),
                'width' => $dimensions[0],
                'height' => $dimensions[1],
                'status' => 'active',
            ]));
        }

        if (Gallery::query()->where('slug', 'tere-ruins-mutoko')->exists()) {
            return;
        }

        Gallery::unguarded(function () use ($media): void {
            $gallery = Gallery::query()->create([
                'slug' => 'tere-ruins-mutoko',
                'title' => 'Tere Ruins, Mutoko',
                'summary' => 'A historic stone structure just east of Mutoko town.',
                'description' => 'Photographed on 22 November 2020 by Robert Stewart Burrett. Source: Wikimedia Commons (https://commons.wikimedia.org/wiki/File:Tere_Ruin_Mutoko.jpg). Licensed under Creative Commons Attribution-ShareAlike 4.0 (https://creativecommons.org/licenses/by-sa/4.0/). No image edits have been made.',
                'location' => 'East of Mutoko town',
                'album_date' => '2020-11-22',
                'cover_media_id' => $media->id,
                'is_featured' => true,
                'display_order' => 0,
                'status' => 'published',
                'verification_status' => 'publishable',
                'verified_at' => now(),
                'published_at' => now(),
            ]);
            $gallery->media()->attach($media->id, ['caption' => $media->caption, 'display_order' => 0]);
        });
    }

    /**
     * @param  Collection<int, Media>  $images
     */
    private function seedGallery(string $slug, string $title, string $summary, string $description, string $location, bool $featured, Collection $images): void
    {
        if ($images->isEmpty() || Gallery::query()->where('slug', $slug)->exists()) {
            return;
        }

        Gallery::unguarded(function () use ($slug, $title, $summary, $description, $location, $featured, $images): void {
            $gallery = Gallery::query()->create([
                'slug' => $slug,
                'title' => $title,
                'summary' => $summary,
                'description' => $description,
                'location' => $location,
                'cover_media_id' => $images->first()->id,
                'is_featured' => $featured,
                'display_order' => 0,
                'status' => 'draft',
                'verification_status' => 'demo',
            ]);

            $sync = [];
            foreach ($images as $order => $media) {
                $sync[$media->id] = ['caption' => $media->alt_text, 'display_order' => $order];
            }
            $gallery->media()->sync($sync);
        });
    }
}
