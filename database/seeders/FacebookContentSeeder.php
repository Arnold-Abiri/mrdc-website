<?php

namespace Database\Seeders;

use App\Models\DistrictStatistic;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\PublicContact;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Authentic council content, directory records, and imagery sourced from Mutoko Rural District Council's
 * official social updates (https://www.facebook.com/mutokordczw) and GovServ entry (https://www.govserv.org/ZW/Mutoko/614085979003410/Mutoko-Rural-District-Council).
 *
 * All records are seeded as fully verified and published (no demo or AI-generated markers).
 * Idempotent: checks for existing records before inserting or updating.
 */
class FacebookContentSeeder extends Seeder
{
    public function run(): void
    {
        $manifestPath = base_path('database/seeders/assets/facebook/manifest.json');
        if (! File::exists($manifestPath)) {
            return;
        }

        $data = json_decode(File::get($manifestPath), true);
        if (! is_array($data)) {
            return;
        }

        $disk = Storage::disk(config('cms.media_disk', 'local'));

        // 1. Seed Public Contacts (Civic address, phone hotline, email, web portal)
        if (! empty($data['public_contacts'])) {
            foreach ($data['public_contacts'] as $contactData) {
                $contact = PublicContact::query()->firstOrNew([
                    'type' => $contactData['type'],
                    'value' => $contactData['value'],
                ]);

                $contact->fill([
                    'office' => $contactData['office'],
                    'type' => $contactData['type'],
                    'value' => $contactData['value'],
                    'display_order' => $contactData['display_order'] ?? 0,
                    'is_public' => true,
                ]);

                $contact->status = 'published';
                $contact->verification_status = 'publishable';
                $contact->published_at = now();
                $contact->save();
            }
        }

        // 2. Seed District Statistics (Verified community social reach)
        if (! empty($data['district_statistics'])) {
            foreach ($data['district_statistics'] as $statData) {
                $stat = DistrictStatistic::query()->firstOrNew(['label' => $statData['label']]);
                $stat->fill([
                    'value' => $statData['value'],
                    'unit' => $statData['unit'],
                    'icon' => $statData['icon'],
                    'source_note' => $statData['source_note'],
                    'display_order' => $statData['display_order'],
                    'is_active' => true,
                ]);
                $stat->save();
            }
        }

        // 3. Seed Editorial Items (News & Notices)
        if (! empty($data['editorial_items'])) {
            foreach ($data['editorial_items'] as $index => $item) {
                $mediaId = null;

                if (! empty($item['image'])) {
                    $mediaId = $this->importMedia(
                        $item['image'],
                        $item['title'],
                        $item['alt_text'] ?? $item['title'],
                        $item['caption'] ?? 'Official Mutoko Rural District Council update.'
                    );
                }

                $publishedAt = isset($item['published_at']) ? Carbon::parse($item['published_at']) : now();

                $editorial = EditorialItem::query()->firstOrNew(['slug' => $item['slug']]);
                $editorial->fill([
                    'type' => $item['type'] ?? 'news',
                    'title' => $item['title'],
                    'summary' => $item['summary'],
                    'body' => $item['body'],
                    'category' => $item['category'] ?? 'general',
                    'featured_media_id' => $mediaId ?? $editorial->featured_media_id,
                    'display_order' => $index,
                ]);

                $editorial->status = 'published';
                $editorial->verification_status = 'publishable';
                $editorial->published_at = $publishedAt;
                $editorial->verified_at = $publishedAt;
                $editorial->seo_title = $item['title'].' | Mutoko RDC';
                $editorial->meta_description = $item['summary'];
                $editorial->save();
            }
        }

        // 4. Seed Galleries
        if (! empty($data['galleries'])) {
            foreach ($data['galleries'] as $order => $galleryData) {
                $galleryImages = [];

                if (! empty($galleryData['images'])) {
                    foreach ($galleryData['images'] as $imgOrder => $imgData) {
                        $mediaId = $this->importMedia(
                            $imgData['filename'],
                            $imgData['title'],
                            $imgData['alt_text'] ?? $imgData['title'],
                            $imgData['caption'] ?? ''
                        );

                        if ($mediaId) {
                            $galleryImages[$mediaId] = [
                                'caption' => $imgData['caption'] ?? '',
                                'display_order' => $imgOrder,
                            ];
                        }
                    }
                }

                if (empty($galleryImages)) {
                    continue;
                }

                $firstMediaId = array_key_first($galleryImages);

                $gallery = Gallery::query()->firstOrNew(['slug' => $galleryData['slug']]);
                $gallery->fill([
                    'title' => $galleryData['title'],
                    'summary' => $galleryData['summary'],
                    'description' => $galleryData['description'],
                    'location' => $galleryData['location'] ?? 'Mutoko District',
                    'cover_media_id' => $firstMediaId,
                    'is_featured' => true,
                    'display_order' => $order,
                ]);

                $gallery->status = 'published';
                $gallery->verification_status = 'publishable';
                $gallery->published_at = now();
                $gallery->verified_at = now();
                $gallery->save();

                $gallery->media()->sync($galleryImages);
            }
        }
    }

    /**
     * Import a media file from database/seeders/assets/facebook/ into CMS storage.
     */
    private function importMedia(string $relativeAssetPath, string $title, string $altText, string $caption): ?int
    {
        $source = base_path('database/seeders/assets/facebook/'.$relativeAssetPath);
        if (! is_file($source)) {
            return null;
        }

        $dimensions = getimagesize($source);
        if ($dimensions === false) {
            return null;
        }

        $contents = file_get_contents($source);
        if ($contents === false) {
            return null;
        }

        $filename = basename($relativeAssetPath);
        $storagePath = 'facebook/'.ltrim($relativeAssetPath, '/');
        $disk = Storage::disk(config('cms.media_disk', 'local'));

        if (! $disk->exists($storagePath)) {
            $disk->put($storagePath, $contents);
        }

        $media = Media::query()->firstOrNew(['storage_path' => $storagePath]);
        $media->fill([
            'title' => $title,
            'alt_text' => $altText,
            'caption' => $caption,
        ]);

        $mimeType = match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        Media::unguarded(function () use ($media, $filename, $storagePath, $mimeType, $disk, $dimensions): void {
            $media->original_filename = $filename;
            $media->mime_type = $mimeType;
            $media->size = $disk->size($storagePath);
            $media->width = $dimensions[0];
            $media->height = $dimensions[1];
            $media->status = 'active';
            $media->save();
        });

        return $media->id;
    }
}
