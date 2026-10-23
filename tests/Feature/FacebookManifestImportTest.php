<?php

use App\Models\DistrictStatistic;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\PublicContact;
use Database\Seeders\FacebookContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake(config('cms.media_disk', 'local'));
});

it('previews without writing and applies unverified drafts once', function (): void {
    $this->artisan('mrdc:import-facebook --dry-run')->assertSuccessful();
    expect(EditorialItem::count())->toBe(0);

    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();
    expect(EditorialItem::count())->toBe(4)
        ->and(Gallery::count())->toBe(1)
        ->and(Media::count())->toBe(8)
        ->and(EditorialItem::public()->count())->toBe(0)
        ->and(Gallery::public()->count())->toBe(0)
        ->and(PublicContact::public()->count())->toBe(0)
        ->and(DistrictStatistic::public()->count())->toBe(0);
    expect(EditorialItem::first()->status)->toBe('draft')
        ->and(EditorialItem::first()->verification_status)->toBe('demo')
        ->and(EditorialItem::first()->published_at)->toBeNull()
        ->and(Gallery::first()->status)->toBe('draft')
        ->and(Gallery::first()->verified_at)->toBeNull();
    expect(Gallery::first()->media()->count())->toBe(4);
    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();
    expect(EditorialItem::count())->toBe(4)->and(Gallery::count())->toBe(1)->and(Media::count())->toBe(8);
});

it('preserves existing content media contacts and statistics', function (): void {
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $item = $manifest['editorial_items'][0];
    EditorialItem::query()->create(['slug' => $item['slug'], 'type' => 'news', 'title' => 'Approved title', 'body' => 'Approved body']);
    PublicContact::query()->create(['type' => 'phone', 'office' => 'Existing', 'value' => 'Existing number']);
    DistrictStatistic::query()->create(['label' => 'Facebook Community Reach', 'value' => 'unknown', 'unit' => 'followers', 'is_active' => false]);
    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();
    expect(EditorialItem::where('slug', $item['slug'])->first()->title)->toBe('Approved title')
        ->and(PublicContact::first()->value)->toBe('Existing number')
        ->and(DistrictStatistic::first()->value)->toBe('unknown');
});

it('keeps existing gallery associations and media metadata intact', function (): void {
    $media = Media::unguarded(fn () => Media::query()->create([
        'title' => 'Approved photograph', 'storage_path' => 'facebook/notices/mutoko-budget-deliberations.jpg',
        'original_filename' => 'approved.jpg', 'mime_type' => 'image/jpeg', 'size' => 42, 'status' => 'active',
    ]));
    $gallery = Gallery::query()->create([
        'slug' => 'council-deliberations-and-service-delivery', 'title' => 'Approved gallery',
    ]);
    $gallery->media()->attach($media->id, ['caption' => 'Approved caption', 'display_order' => 7]);

    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();

    expect($media->fresh()->title)->toBe('Approved photograph')
        ->and($gallery->fresh()->title)->toBe('Approved gallery')
        ->and($gallery->media()->first()->pivot->caption)->toBe('Approved caption')
        ->and($gallery->media()->first()->pivot->display_order)->toBe(7);
});

function facebookFixture(array $changes = []): string
{
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $manifest = array_replace($manifest, $changes);
    $path = tempnam(sys_get_temp_dir(), 'facebook-manifest-');
    file_put_contents($path, json_encode($manifest, JSON_THROW_ON_ERROR));

    return $path;
}

it('leaves the storage disk and all content tables untouched on dry run', function (): void {
    $this->artisan('mrdc:import-facebook --dry-run')->assertSuccessful();
    expect(EditorialItem::count())->toBe(0)
        ->and(Gallery::count())->toBe(0)
        ->and(Media::count())->toBe(0)
        ->and(PublicContact::count())->toBe(0)
        ->and(DistrictStatistic::count())->toBe(0)
        ->and(Storage::disk(config('cms.media_disk', 'local'))->allFiles())->toBe([]);
});

it('keeps statistics opt in and inactive even when explicitly included', function (): void {
    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();
    expect(DistrictStatistic::count())->toBe(0);

    $this->artisan('mrdc:import-facebook --apply --include-statistics')->assertSuccessful();
    $statistic = DistrictStatistic::firstOrFail();
    expect($statistic->is_active)->toBeFalse()
        ->and($statistic->source_note)->toContain('Unverified historical claim')
        ->and($statistic->source_note)->toContain('last verified: unknown');
});

it('never changes published or draft editorial records or their translations', function (): void {
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    foreach (['published', 'draft'] as $index => $status) {
        $item = EditorialItem::query()->create([
            'slug' => $manifest['editorial_items'][$index]['slug'], 'type' => 'news',
            'title' => "Council title {$status}", 'body' => 'Council body',
        ]);
        $item->status = $status;
        $item->verification_status = $status === 'published' ? 'publishable' : 'demo';
        $item->published_at = $status === 'published' ? now()->subDay() : null;
        $item->save();
        $item->translations()->create(['locale' => 'sn', 'field' => 'title', 'value' => 'Musoro']);
    }
    $before = EditorialItem::query()->whereIn('slug', array_column(array_slice($manifest['editorial_items'], 0, 2), 'slug'))->get()->map->getAttributes()->all();

    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();

    $after = EditorialItem::query()->whereIn('slug', array_column(array_slice($manifest['editorial_items'], 0, 2), 'slug'))->get()->map->getAttributes()->all();
    expect($after)->toBe($before)
        ->and(EditorialItem::query()->where('status', 'published')->first()->translated('title', 'sn'))->toBe('Musoro')
        ->and(EditorialItem::query()->where('status', 'draft')->first()->translated('title', 'sn'))->toBe('Musoro');
});

it('retains imported gallery captions and display order', function (): void {
    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $images = Gallery::firstOrFail()->media()->get();
    foreach ($images as $index => $image) {
        expect($image->pivot->caption)->toBe($manifest['galleries'][0]['images'][$index]['caption'])
            ->and($image->pivot->display_order)->toBe($index);
    }
    expect(Gallery::firstOrFail()->cover_media_id)->toBe($images->first()->id);
});

it('rolls back content and deletes only new files after a missing image', function (): void {
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $manifest['editorial_items'][1]['image'] = 'news/missing-image.jpg';
    $path = facebookFixture(['editorial_items' => array_slice($manifest['editorial_items'], 0, 2), 'galleries' => []]);
    $disk = Storage::disk(config('cms.media_disk', 'local'));
    $disk->put('facebook/existing.jpg', 'original bytes');
    try {
        expect(fn () => app(FacebookContentSeeder::class)->import(true, false, $path))->toThrow(RuntimeException::class, 'Missing media');
        expect(EditorialItem::count())->toBe(0)
            ->and(Media::count())->toBe(0)
            ->and($disk->exists('facebook/notices/mutoko-budget-adoption-meeting-1.jpg'))->toBeFalse()
            ->and($disk->get('facebook/existing.jpg'))->toBe('original bytes');
    } finally {
        unlink($path);
    }
});

it('rejects invalid image bytes and traversal without changing data or files', function (string $image): void {
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $manifest['editorial_items'][0]['image'] = $image;
    $path = facebookFixture(['editorial_items' => [$manifest['editorial_items'][0]], 'galleries' => []]);
    try {
        expect(fn () => app(FacebookContentSeeder::class)->import(true, false, $path))->toThrow(RuntimeException::class);
        expect(EditorialItem::count())->toBe(0)
            ->and(Media::count())->toBe(0)
            ->and(Storage::disk(config('cms.media_disk', 'local'))->allFiles())->toBe([]);
    } finally {
        unlink($path);
    }
})->with(['news/../manifest.json', 'news/not-an-image.jpg']);

it('does not create approval or verification audit events', function (): void {
    $this->artisan('mrdc:import-facebook --apply')->assertSuccessful();
    expect(DB::table('audit_events')->count())->toBe(0)
        ->and(EditorialItem::query()->whereNotNull('verified_at')->count())->toBe(0)
        ->and(Gallery::query()->whereNotNull('verified_at')->count())->toBe(0)
        ->and(EditorialItem::public()->count())->toBe(0)
        ->and(Gallery::public()->count())->toBe(0);
});

it('requires confirmation before applying in production', function (): void {
    app()->detectEnvironment(fn () => 'production');
    $this->artisan('mrdc:import-facebook --apply')->expectsQuestion('Apply unverified draft content to production?', false)->assertFailed();
    expect(EditorialItem::count())->toBe(0);
});

it('rejects an existing file whose bytes do not match its image extension', function (): void {
    $root = sys_get_temp_dir().'/facebook-assets-'.uniqid();
    mkdir($root.'/news', 0777, true);
    file_put_contents($root.'/news/invalid.jpg', 'plain text, not an image');
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $manifest['editorial_items'][0]['image'] = 'news/invalid.jpg';
    $path = facebookFixture(['editorial_items' => [$manifest['editorial_items'][0]], 'galleries' => []]);
    try {
        expect(fn () => app(FacebookContentSeeder::class)->import(true, false, $path, $root))->toThrow(RuntimeException::class, 'Invalid image');
        expect(EditorialItem::count())->toBe(0)
            ->and(Media::count())->toBe(0)
            ->and(Storage::disk(config('cms.media_disk', 'local'))->allFiles())->toBe([]);
    } finally {
        unlink($path);
        unlink($root.'/news/invalid.jpg');
        rmdir($root.'/news');
        rmdir($root);
    }
});

it('preserves an existing registered file while rolling back later new files', function (): void {
    $manifest = json_decode(file_get_contents(base_path('database/seeders/assets/facebook/manifest.json')), true);
    $manifest['editorial_items'][2]['image'] = 'news/missing-image.jpg';
    $path = facebookFixture(['editorial_items' => array_slice($manifest['editorial_items'], 0, 3), 'galleries' => []]);
    $disk = Storage::disk(config('cms.media_disk', 'local'));
    $existingPath = 'facebook/notices/mutoko-budget-adoption-meeting-1.jpg';
    $disk->put($existingPath, 'approved original bytes');
    Media::unguarded(fn () => Media::query()->create([
        'title' => 'Council approved', 'storage_path' => $existingPath, 'original_filename' => 'approved.jpg',
        'mime_type' => 'image/jpeg', 'size' => 23, 'status' => 'active',
    ]));
    try {
        expect(fn () => app(FacebookContentSeeder::class)->import(true, false, $path))->toThrow(RuntimeException::class, 'Missing media');
        expect(EditorialItem::count())->toBe(0)
            ->and(Media::count())->toBe(1)
            ->and(Media::first()->title)->toBe('Council approved')
            ->and($disk->get($existingPath))->toBe('approved original bytes')
            ->and($disk->exists('facebook/news/mutoko-refuse-truck-waste-campaign.jpg'))->toBeFalse();
    } finally {
        unlink($path);
    }
});
