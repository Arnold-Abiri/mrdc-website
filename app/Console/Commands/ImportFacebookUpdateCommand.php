<?php

namespace App\Console\Commands;

use App\Models\EditorialItem;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportFacebookUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrdc:import-fb-update
                            {--title= : Title of the news or notice}
                            {--type=news : Content type (news or notice)}
                            {--category=general : Category (e.g., agriculture, environment, finance, development)}
                            {--summary= : Brief summary}
                            {--body= : Full body text}
                            {--image= : Path to local image file}
                            {--alt= : Alt text for image}
                            {--caption= : Image caption}
                            {--date= : Publication date (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quickly import an authentic Facebook update or notice directly into the database and seed manifest';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $title = $this->option('title') ?: $this->ask('Enter post title');
        if (empty($title)) {
            $this->error('Title is required.');

            return self::FAILURE;
        }

        $type = in_array($this->option('type'), ['news', 'notice'], true) ? $this->option('type') : 'news';
        $category = $this->option('category') ?: 'general';
        $summary = $this->option('summary') ?: $this->ask('Enter brief summary');
        $body = $this->option('body') ?: $this->ask('Enter full body content');
        $imagePath = $this->option('image') ?: $this->ask('Local path to image file (optional)');
        $altText = $this->option('alt') ?: $title;
        $caption = $this->option('caption') ?: $title;
        $dateStr = $this->option('date') ?: now()->toDateTimeString();

        $slug = Str::slug($title);
        $mediaId = null;
        $savedImageRelPath = null;

        if ($imagePath && File::exists($imagePath)) {
            $filename = basename($imagePath);
            $targetDir = base_path('database/seeders/assets/facebook/'.($type === 'notice' ? 'notices' : 'news'));
            File::ensureDirectoryExists($targetDir);
            $targetPath = $targetDir.'/'.$filename;
            File::copy($imagePath, $targetPath);

            $dimensions = getimagesize($targetPath);
            $savedImageRelPath = ($type === 'notice' ? 'notices/' : 'news/').$filename;
            $storagePath = 'facebook/'.$savedImageRelPath;

            $disk = Storage::disk(config('cms.media_disk', 'local'));
            $disk->put($storagePath, File::get($targetPath));

            $media = Media::query()->firstOrNew(['storage_path' => $storagePath]);
            $mimeType = match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };

            Media::unguarded(function () use ($media, $filename, $storagePath, $mimeType, $disk, $dimensions, $title, $altText, $caption): void {
                $media->fill([
                    'title' => $title,
                    'alt_text' => $altText,
                    'caption' => $caption,
                    'original_filename' => $filename,
                    'mime_type' => $mimeType,
                    'size' => $disk->size($storagePath),
                    'width' => $dimensions ? $dimensions[0] : 800,
                    'height' => $dimensions ? $dimensions[1] : 600,
                    'status' => 'active',
                ]);
                $media->save();
            });

            $mediaId = $media->id;
        }

        $editorial = EditorialItem::query()->firstOrNew(['slug' => $slug]);
        $editorial->fill([
            'type' => $type,
            'title' => $title,
            'summary' => $summary,
            'body' => $body,
            'category' => $category,
            'featured_media_id' => $mediaId ?? $editorial->featured_media_id,
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => Carbon::parse($dateStr),
            'verified_at' => Carbon::parse($dateStr),
            'seo_title' => $title.' | Mutoko RDC',
            'meta_description' => $summary,
        ]);
        $editorial->save();

        // Update manifest.json if exists
        $manifestFile = base_path('database/seeders/assets/facebook/manifest.json');
        if (File::exists($manifestFile)) {
            $manifest = json_decode(File::get($manifestFile), true) ?: [];
            $manifest['editorial_items'] = $manifest['editorial_items'] ?? [];

            // Check if item already in manifest
            $found = false;
            foreach ($manifest['editorial_items'] as &$item) {
                if ($item['slug'] === $slug) {
                    $item['title'] = $title;
                    $item['summary'] = $summary;
                    $item['body'] = $body;
                    $item['category'] = $category;
                    if ($savedImageRelPath) {
                        $item['image'] = $savedImageRelPath;
                    }
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $manifest['editorial_items'][] = [
                    'slug' => $slug,
                    'type' => $type,
                    'title' => $title,
                    'category' => $category,
                    'summary' => $summary,
                    'body' => $body,
                    'image' => $savedImageRelPath,
                    'alt_text' => $altText,
                    'caption' => $caption,
                    'published_at' => $dateStr,
                ];
            }
            File::put($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $this->info("Successfully imported and published '{$title}' ({$type}) with slug '{$slug}'.");

        return self::SUCCESS;
    }
}
