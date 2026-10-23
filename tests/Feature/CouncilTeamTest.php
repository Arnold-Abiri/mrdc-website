<?php

use App\Models\Media;
use App\Models\Official;
use Database\Seeders\CouncilTeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('reference team portraits are published as editable officials without duplicates', function () {
    Storage::fake('local');

    $this->seed(CouncilTeamSeeder::class);
    $this->seed(CouncilTeamSeeder::class);

    expect(Official::query()->public()->count())->toBe(10);
    expect(Media::query()->count())->toBe(10);

    $chairperson = Official::query()->where('slug', 'alderman-trust-kachidza')->firstOrFail();
    expect($chairperson->name)->toBe('Alderman Trust Kachidza');
    expect($chairperson->title)->toBe('Council Chairperson');

    $official = Official::query()->where('slug', 'b-tasarira')->firstOrFail();
    expect($official->name)->toBe('B. Tasarira');
    expect($official->photo_media_id)->not->toBeNull();

    $this->get('/en/officials')->assertOk()->assertSee('B. Tasarira')->assertSee('D. T. Mutangadura');
    $this->get('/en/officials/b-tasarira')->assertOk()->assertSee('Chief Executive Officer');
    $this->get('/en/managed-media/'.$official->photo_media_id)->assertOk()->assertHeader('content-type', 'image/png');
});
