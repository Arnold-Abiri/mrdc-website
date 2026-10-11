<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Demonstration photo galleries.
 *
 * Classification: PROVISIONAL DEMO. Galleries are seeded as
 * status=draft + verification_status=demo, which scopePublic() can never
 * return. Council reviews each album in Filament and publishes it
 * explicitly; nothing here reaches the public site.
 *
 * Album images reuse the illustration media imported by
 * NewsIllustrationSeeder, so no new image files are introduced.
 * Idempotent: stable slugs, and existing administrator edits are preserved.
 */
class GallerySeeder extends Seeder
{
    public function run(): void
    {
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
