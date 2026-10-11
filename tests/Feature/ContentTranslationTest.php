<?php

namespace Tests\Feature;

use App\Domain\Cms\ServiceManager;
use App\Domain\Content\TranslationManager;
use App\Models\Media;
use App\Models\Page;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContentTranslationTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $role = Role::findByName('System Administrator');
        $user->assignRole($role);
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);

        return $user;
    }

    private function publishedService(User $admin): Service
    {
        $manager = app(ServiceManager::class);
        $service = $manager->create($admin, [
            'slug' => 'qa-lists',
            'name' => 'QA lists',
            'summary' => 'Testing only',
            'requirements' => ['National ID', 'Proof of residence'],
            'steps' => ['Visit office', 'Pay fee'],
            'display_order' => 0,
        ]);
        $manager->setVerification($admin, $service, 'publishable');
        $manager->setStatus($admin, $service, 'published');

        return $service;
    }

    public function test_list_fields_translate_line_by_line_with_english_fallback(): void
    {
        $admin = $this->administrator();
        $service = $this->publishedService($admin);

        app(TranslationManager::class)->saveTranslations($admin, $service, ['sn' => ['requirements' => "Chitupa\nUmbowo"]]);

        $fresh = $service->fresh();
        $this->assertSame(['Chitupa', 'Umbowo'], $fresh->translated('requirements', 'sn'));
        $this->assertSame(['National ID', 'Proof of residence'], $fresh->translated('requirements', 'nd'));
        $this->assertSame(['Visit office', 'Pay fee'], $fresh->translated('steps', 'sn'));

        $this->get('/sn/services/qa-lists')->assertOk()->assertInertia(fn (Assert $page) => $page->where('service.requirements.0', 'Chitupa'));
    }

    public function test_mismatched_list_length_falls_back_to_english(): void
    {
        $admin = $this->administrator();
        $service = $this->publishedService($admin);

        app(TranslationManager::class)->saveTranslations($admin, $service, ['sn' => ['requirements' => 'Chitupa']]);

        $this->assertSame(['National ID', 'Proof of residence'], $service->fresh()->translated('requirements', 'sn'));
    }

    public function test_page_blocks_merge_positionally(): void
    {
        $admin = $this->administrator();

        $page = Page::unguarded(fn () => Page::query()->create([
            'slug' => 'qa-page',
            'title' => 'QA page',
            'blocks' => [
                ['type' => 'paragraph', 'text' => 'First paragraph.'],
                ['type' => 'paragraph', 'text' => 'Second paragraph.'],
            ],
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => now(),
        ]));

        app(TranslationManager::class)->saveTranslations($admin, $page, ['sn' => ['blocks' => "Ndima yekutanga.\n\nNdima yechipiri."]]);

        $blocks = $page->fresh()->translated('blocks', 'sn');
        $this->assertSame('Ndima yekutanga.', $blocks[0]['text']);
        $this->assertSame('Ndima yechipiri.', $blocks[1]['text']);
        $this->assertSame('paragraph', $blocks[0]['type']);
    }

    public function test_media_and_contact_office_translate(): void
    {
        $admin = $this->administrator();

        $media = Media::unguarded(fn () => Media::query()->create([
            'title' => 'QA photo', 'alt_text' => 'QA alt', 'caption' => 'QA caption',
            'original_filename' => 'qa.webp', 'storage_path' => 'qa.webp',
            'mime_type' => 'image/webp', 'size' => 10, 'status' => 'active',
        ]));
        app(TranslationManager::class)->saveTranslations($admin, $media, ['sn' => ['caption' => 'Mufananidzo']]);
        $this->assertSame('Mufananidzo', $media->fresh()->translated('caption', 'sn'));
        $this->assertSame('QA alt', $media->fresh()->translated('alt_text', 'sn'));

        $contact = PublicContact::unguarded(fn () => PublicContact::query()->create([
            'office' => 'Revenue Office', 'type' => 'phone', 'value' => '+263100', 'display_order' => 0,
        ]));
        app(TranslationManager::class)->saveTranslations($admin, $contact, ['nd' => ['office' => 'Ihhovisi leMali']]);
        $this->assertSame('Ihhovisi leMali', $contact->fresh()->translated('office', 'nd'));
        $this->assertSame('Revenue Office', $contact->fresh()->translated('office', 'sn'));
    }
}
