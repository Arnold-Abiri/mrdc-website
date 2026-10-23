<?php

use App\Models\DistrictStatistic;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\PublicContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
