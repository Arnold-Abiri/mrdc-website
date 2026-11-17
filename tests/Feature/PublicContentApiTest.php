<?php

use App\Models\BankAccount;
use App\Models\Department;
use App\Models\EditorialItem;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Media;
use App\Support\PublicApiCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('all public collections and missing details return JSON', function (string $resource) {
    $this->getJson('/api/v1/'.$resource)->assertOk()->assertJsonStructure(['data', 'links', 'meta'])->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/'.$resource.'/999999')->assertNotFound();
})->with(array_keys(PublicApiCatalog::all()));

test('documentation describes all public operations', function () {
    $this->get('/api/documentation')->assertOk()->assertSee('SwaggerUIBundle')->assertSee('api/openapi.json');
    $spec = $this->getJson('/api/openapi.json')->assertOk()->assertJsonPath('openapi', '3.0.3')->json();
    expect($spec['paths'])->toHaveCount(28);
    foreach (PublicApiCatalog::all() as $resource => $definition) {
        expect($spec['paths']['/'.$resource]['get']['tags'])->toBe([$definition['tag']]);
        expect($spec['paths']['/'.$resource.'/{identifier}']['get']['responses'])->toHaveKey(404);
    }
});

test('news and notices enforce publication and type on lists and details', function () {
    foreach ([
        ['public-news', 'news', 'published', 'publishable', now()->subDay(), null],
        ['public-notice', 'notice', 'published', 'publishable', now()->subDay(), null],
        ['draft', 'news', 'draft', 'publishable', now()->subDay(), null],
        ['unverified', 'news', 'published', 'demo', now()->subDay(), null],
        ['scheduled', 'news', 'published', 'publishable', now()->addDay(), null],
        ['expired', 'news', 'published', 'publishable', now()->subDay(), today()->subDay()],
    ] as [$slug, $type, $status, $verification, $published, $expires]) {
        (new EditorialItem)->forceFill(['slug' => $slug, 'type' => $type, 'title' => $slug, 'body' => 'Public content', 'status' => $status, 'verification_status' => $verification, 'published_at' => $published, 'expires_at' => $expires])->save();
    }
    $this->getJson('/api/v1/news')->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'public-news')->assertJsonMissingPath('data.0.verification_status');
    $this->getJson('/api/v1/notices')->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'public-notice');
    $this->getJson('/api/v1/news/public-news')->assertOk();
    foreach (['draft', 'unverified', 'scheduled', 'expired', 'public-notice'] as $slug) {
        $this->getJson('/api/v1/news/'.$slug)->assertNotFound();
    }
});

test('faqs are translated with English fallback and paginated', function () {
    $faq = Faq::create(['question' => 'How to pay?', 'answer' => 'Visit the council.', 'category' => 'billing', 'is_published' => true]);
    $faq->translations()->create(['locale' => 'sn', 'field' => 'question', 'value' => 'Ndinobhadhara sei?']);
    Faq::create(['question' => 'Second', 'answer' => 'Answer', 'category' => 'general', 'is_published' => true]);
    $draft = Faq::create(['question' => 'Private', 'answer' => 'Private', 'category' => 'general', 'is_published' => false]);
    $this->getJson('/api/v1/faqs?locale=sn&per_page=1')->assertJsonCount(1, 'data')->assertJsonPath('data.0.question', 'Ndinobhadhara sei?')->assertJsonPath('data.0.answer', 'Visit the council.')->assertJsonPath('meta.total', 2);
    $this->getJson('/api/v1/faqs/'.$faq->id.'?locale=nd')->assertJsonPath('data.question', 'How to pay?');
    $this->getJson('/api/v1/faqs/'.$draft->id)->assertNotFound();
});

test('departments expose only verified public profiles', function () {
    $department = Department::factory()->create();
    $this->getJson('/api/v1/departments/'.$department->id)->assertNotFound();
    $department->forceFill(['status' => 'active', 'public_name' => 'Public office', 'public_status' => 'published', 'public_verification_status' => 'publishable', 'public_published_at' => now()->subMinute()])->save();
    $this->getJson('/api/v1/departments/'.$department->id)->assertOk()->assertJsonPath('data.public_name', 'Public office')->assertJsonMissingPath('data.name')->assertJsonMissingPath('data.code');
    $department->forceFill(['public_published_at' => now()->addDay()])->save();
    $this->getJson('/api/v1/departments')->assertJsonCount(0, 'data');
});

test('bank accounts must be active and published', function () {
    $account = BankAccount::create(['bank_name' => 'Bank', 'account_name' => 'Council', 'account_number' => '00123', 'branch_name' => 'Mutoko', 'currency' => 'USD', 'category' => 'general', 'is_active' => true, 'status' => 'published']);
    $this->getJson('/api/v1/bank-accounts/'.$account->id)->assertOk()->assertJsonPath('data.account_number', '00123');
    $account->update(['is_active' => false]);
    $this->getJson('/api/v1/bank-accounts/'.$account->id)->assertNotFound();
});

test('invalid query parameters are rejected', function (string $query) {
    $this->getJson('/api/v1/faqs?'.$query)->assertUnprocessable()->assertJsonStructure(['message', 'errors']);
})->with(['per_page=101', 'per_page=0', 'per_page=abc', 'page=0', 'locale=fr', 'locale[]=sn']);

test('unknown resources and writes are unavailable', function () {
    $this->getJson('/api/v1/users')->assertNotFound();
    $this->postJson('/api/v1/news', ['title' => 'Unauthorized'])->assertStatus(405);
});

test('gallery images include public URLs and exclude inactive media', function () {
    $gallery = (new Gallery)->forceFill(['slug' => 'album', 'title' => 'Album', 'status' => 'published', 'verification_status' => 'publishable', 'published_at' => now()->subMinute()]);
    $gallery->save();
    foreach (['active', 'inactive'] as $status) {
        $media = (new Media)->forceFill(['title' => 'Image', 'alt_text' => 'Public image', 'original_filename' => 'image.jpg', 'storage_path' => 'gallery/'.$status.'.jpg', 'mime_type' => 'image/jpeg', 'size' => 10, 'status' => $status]);
        $media->save();
        $gallery->media()->attach($media->id);
    }
    $this->getJson('/api/v1/galleries/album?locale=sn')->assertOk()->assertJsonCount(1, 'data.images')->assertJsonPath('data.images.0.alt', 'Public image')->assertJsonPath('data.images.0.url', route('managed-media.show', ['locale' => 'sn', 'media' => $gallery->media()->first()->id]));
});

test('public content requests are rate limited', function () {
    for ($request = 0; $request < 60; $request++) {
        $this->getJson('/api/v1/faqs')->assertOk();
    }
    $this->getJson('/api/v1/faqs')->assertStatus(429);
});
