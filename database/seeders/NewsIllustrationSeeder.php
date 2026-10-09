<?php

namespace Database\Seeders;

use App\Models\EditorialItem;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class NewsIllustrationSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'demo-understanding-mrdc-role' => ['council-role.webp', 'Illustration of a rural council service centre and surrounding community'],
            'demo-accessing-council-services' => ['accessing-services.webp', 'Illustration of a resident receiving help at a public service desk'],
            'demo-community-participation' => ['community-participation.webp', 'Illustration of residents discussing local priorities under a tree'],
            'demo-rural-roads-maintenance' => ['rural-roads.webp', 'Illustration of road grading and culvert maintenance in a rural area'],
            'demo-environmental-stewardship' => ['environmental-stewardship.webp', 'Illustration of tree planting and a stream in a granite landscape'],
            'demo-agriculture-economic-development' => ['agriculture-development.webp', 'Illustration of horticulture and a local produce market'],
        ];

        foreach ($images as $slug => [$filename, $altText]) {
            $source = base_path('database/seeders/assets/news/'.$filename);
            if (! is_file($source)) {
                continue;
            }

            $contents = file_get_contents($source);
            $dimensions = getimagesize($source);
            if ($contents === false || $dimensions === false) {
                continue;
            }

            $storagePath = 'news/illustrations/'.$filename;
            $disk = Storage::disk(config('cms.media_disk', 'local'));
            if (! $disk->exists($storagePath) && ! $disk->put($storagePath, $contents)) {
                continue;
            }

            $media = Media::query()->where('storage_path', $storagePath)->first();
            if (! $media) {
                [$width, $height] = $dimensions;
                $media = Media::unguarded(fn (): Media => Media::query()->create([
                    'title' => 'News illustration: '.$slug,
                    'alt_text' => $altText,
                    'caption' => 'AI-generated editorial illustration; not a photograph of a Council event.',
                    'original_filename' => $filename,
                    'storage_path' => $storagePath,
                    'mime_type' => 'image/webp',
                    'size' => $disk->size($storagePath),
                    'width' => $width,
                    'height' => $height,
                    'status' => 'active',
                ]));
            }

            EditorialItem::query()->where('slug', $slug)->where('type', 'news')->whereNull('featured_media_id')->update(['featured_media_id' => $media->id]);
        }
    }
}
