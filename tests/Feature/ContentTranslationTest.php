<?php

namespace Tests\Feature;

use App\Domain\Cms\DocumentManager;
use App\Domain\Cms\EditorialManager;
use App\Domain\Cms\HomepageSlideManager;
use App\Domain\Cms\MediaManager;
use App\Domain\Cms\ServiceManager;
use App\Domain\Content\TranslationManager;
use App\Filament\Resources\BankAccounts\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccount;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Editorial\Pages\CreateEditorialItem;
use App\Filament\Resources\Editorial\Pages\EditEditorialItem;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\ProjectUpdates\Pages\CreateProjectUpdate;
use App\Filament\Resources\ProjectUpdates\Pages\EditProjectUpdate;
use App\Filament\Resources\Slides\Pages\CreateHomepageSlide;
use App\Filament\Resources\Slides\Pages\EditHomepageSlide;
use App\Models\BankAccount;
use App\Models\CouncilProject;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomepageSlide;
use App\Models\Media;
use App\Models\Page;
use App\Models\ProjectUpdate;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\User;
use App\Services\Ai\GeminiStaffService;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
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

    public function test_slide_edit_action_generates_and_saves_translations(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin);
        $slide = app(HomepageSlideManager::class)->create($admin, [
            'headline' => 'Welcome',
            'supporting_text' => 'Council services',
            'cta_label' => 'Read more',
            'display_order' => 0,
            'is_active' => true,
        ]);

        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('HomepageSlide', [
                    'headline' => 'Welcome',
                    'supporting_text' => 'Council services',
                    'cta_label' => 'Read more',
                ], 'sn')
                ->andReturn(['success' => true, 'translations' => [
                    'headline' => 'Mauya',
                    'supporting_text' => 'Mabasa ekanzuru',
                    'cta_label' => 'Verenga zvimwe',
                ]]);
        });

        Livewire::test(EditHomepageSlide::class, ['record' => $slide->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors();

        $this->assertSame('Mauya', $slide->fresh()->translated('headline', 'sn'));
        $this->assertSame('Mabasa ekanzuru', $slide->fresh()->translated('supporting_text', 'sn'));
        $this->assertSame('Verenga zvimwe', $slide->fresh()->translated('cta_label', 'sn'));
    }

    public function test_slide_create_translation_preserves_form_and_saves_on_create(): void
    {
        $this->actingAs($this->administrator());
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('HomepageSlide', ['headline' => 'Welcome'], 'sn')
                ->andReturn(['success' => true, 'translations' => ['headline' => 'Mauya']]);
        });

        $page = Livewire::test(CreateHomepageSlide::class)
            ->fillForm([
                'headline' => 'Welcome',
                'cta_url' => '/services',
                'display_order' => 2,
                'is_active' => true,
                'content_translations_nd_headline' => 'Siyakwamukela',
            ])
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors()
            ->assertSet('data.content_translations_sn_headline', 'Mauya')
            ->assertSet('data.content_translations_nd_headline', 'Siyakwamukela')
            ->assertSet('data.cta_url', '/services');

        $this->assertDatabaseCount('homepage_slides', 0);
        $page->call('create')->assertHasNoFormErrors();
        $slide = HomepageSlide::query()->sole();
        $this->assertSame('Mauya', $slide->translated('headline', 'sn'));
        $this->assertSame('Siyakwamukela', $slide->translated('headline', 'nd'));
    }

    public function test_slide_forms_show_field_errors_for_invalid_button_links(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin);
        $input = ['headline' => 'Welcome', 'display_order' => 0, 'is_active' => true];
        $slide = app(HomepageSlideManager::class)->create($admin, $input);

        foreach (['services', 'https://example.invalid/services', '//example.invalid/services', '/bad path', '/\\example.invalid'] as $url) {
            Livewire::test(CreateHomepageSlide::class)
                ->fillForm([...$input, 'cta_url' => $url])
                ->call('create')
                ->assertHasFormErrors(['cta_url']);

            Livewire::test(EditHomepageSlide::class, ['record' => $slide->getRouteKey()])
                ->fillForm(['cta_url' => $url])
                ->call('save')
                ->assertHasFormErrors(['cta_url']);
        }

        $this->assertDatabaseCount('homepage_slides', 1);
        $this->assertNull($slide->fresh()->cta_url);
    }

    public function test_slide_manager_reports_invalid_button_link_as_validation_error(): void
    {
        $admin = $this->administrator();
        $this->expectException(ValidationException::class);

        app(HomepageSlideManager::class)->create($admin, [
            'headline' => 'Welcome', 'display_order' => 0, 'is_active' => true,
            'cta_url' => 'services',
        ]);
    }

    public function test_editorial_create_translation_is_reviewed_then_saved(): void
    {
        $this->actingAs($this->administrator());
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateEditorial')->once()
                ->with('Council update', 'Latest news', 'Council news body', null, null, 'sn')
                ->andReturn(['success' => true, 'title' => 'Nhau dzekanzuru', 'summary' => 'Nhau itsva', 'body' => 'Mashoko ekanzuru']);
        });

        $page = Livewire::test(CreateEditorialItem::class)
            ->fillForm([
                'title' => 'Council update', 'summary' => 'Latest news', 'body' => 'Council news body',
                'type' => 'news', 'slug' => 'council-update', 'display_order' => 0, 'is_urgent' => 0,
                'content_translations_nd_title' => 'Indaba',
            ])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors()
            ->assertSet('data.title', 'Council update')
            ->assertSet('data.content_translations_sn_title', 'Nhau dzekanzuru')
            ->assertSet('data.content_translations_nd_title', 'Indaba');

        $this->assertDatabaseCount('editorial_items', 0);
        $page->call('create')->assertHasNoFormErrors();
        $item = EditorialItem::query()->sole();
        $this->assertSame('Nhau dzekanzuru', $item->translated('title', 'sn'));
        $this->assertSame('Mashoko ekanzuru', $item->translated('body', 'sn'));
        $this->assertSame('Indaba', $item->translated('title', 'nd'));
    }

    public function test_editorial_edit_translation_action_saves_translation(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin);
        $item = app(EditorialManager::class)->create($admin, [
            'title' => 'Council update', 'summary' => 'Latest news', 'body' => 'Council news body',
            'type' => 'news', 'slug' => 'council-update', 'display_order' => 0, 'is_urgent' => 0,
        ]);
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateEditorial')->once()
                ->with('Council update', 'Latest news', 'Council news body', null, null, 'nd')
                ->andReturn(['success' => true, 'title' => 'Indaba', 'body' => 'Izindaba zekhansili']);
        });

        Livewire::test(EditEditorialItem::class, ['record' => $item->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])
            ->assertHasNoActionErrors();

        $this->assertSame('Indaba', $item->fresh()->translated('title', 'nd'));
        $this->assertSame('Izindaba zekhansili', $item->fresh()->translated('body', 'nd'));
    }

    public function test_media_create_translation_preserves_upload_and_saves_on_create(): void
    {
        Storage::fake(config('cms.media_disk'));
        $this->actingAs($this->administrator());
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('Media', ['title' => 'Council guide', 'alt_text' => 'Guide cover', 'caption' => 'Public guide'], 'sn')
                ->andReturn(['success' => true, 'translations' => [
                    'title' => 'Gwaro rekanzuru', 'alt_text' => 'Butiro regwaro', 'caption' => 'Gwaro reruzhinji',
                ]]);
        });

        $page = Livewire::test(CreateMedia::class)
            ->fillForm([
                'file' => UploadedFile::fake()->createWithContent('guide.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"),
                'title' => 'Council guide', 'alt_text' => 'Guide cover', 'caption' => 'Public guide',
                'content_translations_nd_title' => 'Umhlahlandlela',
            ])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors()
            ->assertSet('data.title', 'Council guide')
            ->assertSet('data.content_translations_sn_title', 'Gwaro rekanzuru')
            ->assertSet('data.content_translations_nd_title', 'Umhlahlandlela');

        $this->assertDatabaseCount('media', 0);
        $page->call('create')->assertHasNoFormErrors();
        $media = Media::query()->sole();
        $this->assertSame('guide.pdf', $media->original_filename);
        $this->assertSame('Gwaro rekanzuru', $media->translated('title', 'sn'));
        $this->assertSame('Butiro regwaro', $media->translated('alt_text', 'sn'));
        $this->assertSame('Gwaro reruzhinji', $media->translated('caption', 'sn'));
        $this->assertSame('Umhlahlandlela', $media->translated('title', 'nd'));
    }

    public function test_media_edit_translation_action_saves_translations(): void
    {
        Storage::fake(config('cms.media_disk'));
        $admin = $this->administrator();
        $this->actingAs($admin);
        $media = app(MediaManager::class)->upload($admin,
            UploadedFile::fake()->createWithContent('guide.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"),
            ['title' => 'Council guide']);
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('Media', ['title' => 'Council guide'], 'nd')
                ->andReturn(['success' => true, 'translations' => ['title' => 'Umhlahlandlela']]);
        });

        Livewire::test(EditMedia::class, ['record' => $media->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])
            ->assertHasNoActionErrors();

        $this->assertSame('Umhlahlandlela', $media->fresh()->translated('title', 'nd'));
    }

    public function test_document_create_translation_preserves_pdf_and_saves_on_create(): void
    {
        Storage::fake(config('cms.media_disk'));
        $this->actingAs($this->administrator());
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('Document', ['title' => 'Council guide', 'description' => 'Public guide'], 'sn')
                ->andReturn(['success' => true, 'translations' => [
                    'title' => 'Gwaro rekanzuru', 'description' => 'Gwaro reruzhinji',
                ]]);
        });

        $page = Livewire::test(CreateDocument::class)
            ->fillForm([
                'document_file_upload' => UploadedFile::fake()->createWithContent('guide.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"),
                'title' => 'Council guide', 'description' => 'Public guide',
                'slug' => 'council-guide', 'category' => 'publication', 'visibility' => 'public',
                'content_translations_nd_title' => 'Umhlahlandlela',
            ])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors()
            ->assertSet('data.title', 'Council guide')
            ->assertSet('data.content_translations_sn_title', 'Gwaro rekanzuru')
            ->assertSet('data.content_translations_nd_title', 'Umhlahlandlela');

        $this->assertDatabaseCount('documents', 0);
        $page->call('create')->assertHasNoFormErrors();
        $document = Document::query()->sole();
        $this->assertSame('guide.pdf', $document->media->original_filename);
        $this->assertSame('Gwaro rekanzuru', $document->translated('title', 'sn'));
        $this->assertSame('Gwaro reruzhinji', $document->translated('description', 'sn'));
        $this->assertSame('Umhlahlandlela', $document->translated('title', 'nd'));
    }

    public function test_document_edit_translation_action_saves_translations(): void
    {
        Storage::fake(config('cms.media_disk'));
        $admin = $this->administrator();
        $this->actingAs($admin);
        $media = app(MediaManager::class)->upload($admin,
            UploadedFile::fake()->createWithContent('guide.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"),
            ['title' => 'Council guide']);
        $document = app(DocumentManager::class)->create($admin, [
            'title' => 'Council guide', 'description' => 'Public guide', 'slug' => 'council-guide',
            'category' => 'publication', 'visibility' => 'public', 'media_id' => $media->id,
        ]);
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('Document', ['title' => 'Council guide', 'description' => 'Public guide'], 'nd')
                ->andReturn(['success' => true, 'translations' => [
                    'title' => 'Umhlahlandlela', 'description' => 'Izindaba zekhansili',
                ]]);
        });

        Livewire::test(EditDocument::class, ['record' => $document->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])
            ->assertHasNoActionErrors();

        $this->assertSame('Umhlahlandlela', $document->fresh()->translated('title', 'nd'));
        $this->assertSame('Izindaba zekhansili', $document->fresh()->translated('description', 'nd'));
    }

    public function test_gallery_translation_actions_preserve_images_and_save_translations(): void
    {
        $this->actingAs($this->administrator());
        $media = Media::unguarded(fn () => Media::query()->create([
            'title' => 'Council photo', 'original_filename' => 'photo.webp', 'storage_path' => 'photo.webp',
            'mime_type' => 'image/webp', 'size' => 10, 'status' => 'active',
        ]));
        $fields = ['title' => 'Council visit', 'summary' => 'Visit summary', 'description' => 'Visit details', 'location' => 'Council office'];
        $translations = ['title' => 'Kushanya', 'summary' => 'Pfupiso', 'description' => 'Mashoko', 'location' => 'Hofisi'];
        $this->mock(GeminiStaffService::class, function ($mock) use ($fields, $translations): void {
            $mock->shouldReceive('translateFields')->once()->with('Gallery', $fields, 'sn')
                ->andReturn(['success' => true, 'translations' => $translations]);
            $mock->shouldReceive('translateFields')->once()->with('Gallery', $fields, 'nd')
                ->andReturn(['success' => true, 'translations' => ['title' => 'Ukuvakasha']]);
        });

        $page = Livewire::test(CreateGallery::class)
            ->fillForm([...$fields, 'slug' => 'council-visit', 'is_featured' => 0, 'display_order' => 0,
                'media' => [['media_id' => $media->id, 'caption' => 'Original caption']]])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors()
            ->assertSet('data.title', 'Council visit')
            ->assertSet('data.content_translations_sn_title', 'Kushanya');
        $this->assertDatabaseCount('galleries', 0);
        $page->call('create')->assertHasNoFormErrors();
        $gallery = Gallery::query()->sole();
        foreach ($translations as $field => $value) {
            $this->assertSame($value, $gallery->translated($field, 'sn'));
        }
        $this->assertSame($media->id, $gallery->media->sole()->id);
        $this->assertSame('Original caption', $gallery->media->sole()->pivot->caption);

        Livewire::test(EditGallery::class, ['record' => $gallery->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])->assertHasNoActionErrors();
        $this->assertSame('Ukuvakasha', $gallery->fresh()->translated('title', 'nd'));
    }

    public function test_faq_create_and_edit_translation_actions_save_question_and_answer(): void
    {
        $this->actingAs($this->administrator());
        $fields = ['question' => 'Where is the office?', 'answer' => 'Visit the council office.'];
        $this->mock(GeminiStaffService::class, function ($mock) use ($fields): void {
            $mock->shouldReceive('translateFields')->once()->with('Faq', $fields, 'sn')
                ->andReturn(['success' => true, 'translations' => ['question' => 'Hofisi iri kupi?', 'answer' => 'Shanyirai hofisi.']]);
            $mock->shouldReceive('translateFields')->once()->with('Faq', $fields, 'nd')
                ->andReturn(['success' => true, 'translations' => ['question' => 'Iphi ihofisi?', 'answer' => 'Vakashela ihofisi.']]);
        });
        $page = Livewire::test(CreateFaq::class)
            ->fillForm([...$fields, 'category' => 'general', 'display_order' => 0, 'is_published' => false, 'is_featured' => false])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])->assertHasNoActionErrors()
            ->assertSet('data.question', $fields['question'])
            ->assertSet('data.content_translations_sn_question', 'Hofisi iri kupi?');
        $this->assertDatabaseCount('faqs', 0);
        $page->call('create')->assertHasNoFormErrors();
        $faq = Faq::query()->sole();
        $this->assertSame('Hofisi iri kupi?', $faq->translated('question', 'sn'));
        $this->assertSame('Shanyirai hofisi.', $faq->translated('answer', 'sn'));
        $this->assertFalse($faq->is_published);

        Livewire::test(EditFaq::class, ['record' => $faq->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])->assertHasNoActionErrors();
        $this->assertSame('Iphi ihofisi?', $faq->fresh()->translated('question', 'nd'));
        $this->assertSame('Vakashela ihofisi.', $faq->fresh()->translated('answer', 'nd'));
    }

    public function test_council_resource_create_and_edit_translation_actions(): void
    {
        $this->actingAs($this->administrator());
        $cases = [
            ['Officials', 'Official', ['name' => 'Council Officer', 'title' => 'Director', 'biography' => 'Council service', 'slug' => 'qa-director', 'category' => 'executive', 'is_department_head' => 0, 'display_order' => 0],
                ['title' => 'Director', 'biography' => 'Council service']],
            ['Departments', 'Department', ['name' => 'QA Planning', 'code' => 'QA-PLAN', 'public_name' => 'Planning', 'public_summary' => 'Planning services', 'public_description' => 'Council planning', 'responsibilities' => ['Plan roads', 'Review permits'], 'public_display_order' => 0],
                ['public_name' => 'Planning', 'public_summary' => 'Planning services', 'public_description' => 'Council planning', 'responsibilities' => "Plan roads\nReview permits"]],
            ['Wards', 'Ward', ['name' => 'Ward 30', 'description' => 'Ward services', 'boundaries_description' => 'Northern boundary', 'slug' => 'qa-ward-30', 'display_order' => 0],
                ['name' => 'Ward 30', 'description' => 'Ward services', 'boundaries_description' => 'Northern boundary']],
            ['Meetings', 'CouncilMeeting', ['title' => 'Council meeting', 'summary' => 'Council agenda', 'venue' => 'Council office', 'meeting_type' => 'full_council', 'scheduled_date' => '2026-12-01', 'meeting_status' => 'scheduled', 'display_order' => 0],
                ['title' => 'Council meeting', 'summary' => 'Council agenda', 'venue' => 'Council office']],
            ['Statistics', 'DistrictStatistic', ['label' => 'Electoral wards', 'unit' => 'wards', 'value' => '29', 'is_active' => 1, 'display_order' => 0],
                ['label' => 'Electoral wards', 'unit' => 'wards']],
        ];
        foreach ($cases as [$folder, $model, $input, $fields]) {
            $modelClass = 'App\\Models\\'.$model;
            $createPage = 'App\\Filament\\Resources\\'.$folder.'\\Pages\\Create'.$model;
            $editPage = 'App\\Filament\\Resources\\'.$folder.'\\Pages\\Edit'.$model;
            $translations = array_map(fn (string $value): string => 'SN '.$value, $fields);
            if ($model === 'Department') {
                $translations['responsibilities'] = "Ronga migwagwa\nOngorora mvumo";
            }
            $this->mock(GeminiStaffService::class, function ($mock) use ($model, $fields, $translations): void {
                $mock->shouldReceive('translateFields')->once()->with($model, $fields, 'sn')
                    ->andReturn(['success' => true, 'translations' => $translations]);
                $mock->shouldReceive('translateFields')->once()->with($model, $fields, 'nd')
                    ->andReturn(['success' => true, 'translations' => array_map(fn (string $value): string => 'ND '.$value, $fields)]);
            });
            $count = $modelClass::query()->count();
            $page = Livewire::test($createPage)->fillForm($input);
            if ($model === 'Department') {
                $page->set('data.responsibilities', ['first' => ['value' => 'Plan roads'], 'second' => ['value' => 'Review permits']]);
            }
            $page->assertActionVisible('ai_translate')
                ->callAction('ai_translate', data: ['locale' => 'sn'])->assertHasNoActionErrors();
            $this->assertSame($count, $modelClass::query()->count());
            foreach ($translations as $field => $value) {
                $page->assertSet('data.content_translations_sn_'.$field, $value);
            }
            $page->call('create')->assertHasNoFormErrors();
            $record = $modelClass::query()->latest('id')->firstOrFail();
            foreach ($translations as $field => $value) {
                $expected = $field === 'responsibilities' ? explode("\n", $value) : $value;
                $this->assertSame($expected, $record->translated($field, 'sn'));
            }
            Livewire::test($editPage, ['record' => $record->getRouteKey()])
                ->assertActionVisible('ai_translate')
                ->callAction('ai_translate', data: ['locale' => 'nd'])->assertHasNoActionErrors();
            $firstField = array_key_first($fields);
            $this->assertSame('ND '.$fields[$firstField], $record->fresh()->translated($firstField, 'nd'));
            if ($model === 'DistrictStatistic') {
                $this->assertSame('29', $record->fresh()->value);
            }
            if ($model === 'Department') {
                $this->assertSame(['Plan roads', 'Review permits'], $record->fresh()->responsibilities);
            }
        }
    }

    public function test_development_resources_translate_on_create_and_edit(): void
    {
        $this->actingAs($this->administrator());
        $cases = [
            ['Services', 'Service', ['name' => 'Council service', 'slug' => 'qa-service', 'display_order' => 0, 'requirements' => [], 'steps' => []], ['name' => 'Council service']],
            ['Projects', 'CouncilProject', ['title' => 'Council project', 'description' => 'Project details', 'slug' => 'qa-project', 'project_type' => 'project', 'project_status' => 'planned', 'display_order' => 0], ['title' => 'Council project', 'description' => 'Project details']],
            ['Investment', 'InvestmentOpportunity', ['title' => 'Council opportunity', 'description' => 'Opportunity details', 'slug' => 'qa-investment', 'opportunity_status' => 'open', 'display_order' => 0], ['title' => 'Council opportunity', 'description' => 'Opportunity details']],
            ['Tenders', 'Tender', ['title' => 'Council tender', 'description' => 'Tender details', 'slug' => 'qa-tender', 'reference' => 'QA-01', 'lifecycle_status' => 'open', 'display_order' => 0], ['title' => 'Council tender', 'description' => 'Tender details']],
            ['Vacancies', 'Vacancy', ['title' => 'Council vacancy', 'description' => 'Vacancy details', 'slug' => 'qa-vacancy', 'display_order' => 0], ['title' => 'Council vacancy', 'description' => 'Vacancy details']],
        ];
        foreach ($cases as [$folder, $model, $input, $fields]) {
            $modelClass = 'App\\Models\\'.$model;
            $createPage = 'App\\Filament\\Resources\\'.$folder.'\\Pages\\Create'.$model;
            $editPage = 'App\\Filament\\Resources\\'.$folder.'\\Pages\\Edit'.$model;
            $translations = array_map(fn (string $value): string => 'SN '.$value, $fields);
            $this->mock(GeminiStaffService::class, function ($mock) use ($model, $fields, $translations): void {
                $mock->shouldReceive('translateFields')->once()->with($model, $fields, 'sn')
                    ->andReturn(['success' => true, 'translations' => $translations]);
                $mock->shouldReceive('translateFields')->once()->with($model, $fields, 'nd')
                    ->andReturn(['success' => true, 'translations' => array_map(fn (string $value): string => 'ND '.$value, $fields)]);
            });
            $count = $modelClass::query()->count();
            $page = Livewire::test($createPage)->fillForm($input)
                ->assertActionVisible('ai_translate')
                ->callAction('ai_translate', data: ['locale' => 'sn'])->assertHasNoActionErrors();
            $this->assertSame($count, $modelClass::query()->count());
            foreach ($translations as $field => $value) {
                $page->assertSet('data.content_translations_sn_'.$field, $value);
            }
            $page->call('create')->assertHasNoFormErrors();
            $record = $modelClass::query()->latest('id')->firstOrFail();
            foreach ($translations as $field => $value) {
                $this->assertSame($value, $record->translated($field, 'sn'));
            }
            Livewire::test($editPage, ['record' => $record->getRouteKey()])
                ->assertActionVisible('ai_translate')
                ->callAction('ai_translate', data: ['locale' => 'nd'])->assertHasNoActionErrors();
            $field = array_key_first($fields);
            $this->assertSame('ND '.$fields[$field], $record->fresh()->translated($field, 'nd'));
        }
    }

    public function test_payment_account_purpose_translation_preserves_account_details_and_renders_publicly(): void
    {
        $this->actingAs($this->administrator());
        $details = ['bank_name' => 'Example Bank', 'account_name' => 'Council', 'account_number' => '00000001',
            'branch_name' => 'Main', 'branch_code' => '001', 'currency' => 'ZiG', 'category' => 'rates',
            'purpose' => 'Pay council rates', 'display_order' => 0, 'is_active' => true, 'status' => 'published'];
        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()->with('BankAccount', ['purpose' => 'Pay council rates'], 'sn')
                ->andReturn(['success' => true, 'translations' => ['purpose' => 'Bhadharai mari yekanzuru']]);
            $mock->shouldReceive('translateFields')->once()->with('BankAccount', ['purpose' => 'Pay council rates'], 'nd')
                ->andReturn(['success' => true, 'translations' => ['purpose' => 'Bhadala imali yekhansili']]);
        });
        $page = Livewire::test(CreateBankAccount::class)
            ->fillForm($details)->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])->assertHasNoActionErrors();
        $this->assertDatabaseCount('bank_accounts', 0);
        $page->call('create')->assertHasNoFormErrors();
        $account = BankAccount::query()->sole();
        $this->assertSame('Bhadharai mari yekanzuru', $account->translated('purpose', 'sn'));
        Livewire::test(EditBankAccount::class, ['record' => $account->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])->assertHasNoActionErrors();
        $this->assertSame('Bhadala imali yekhansili', $account->fresh()->translated('purpose', 'nd'));
        foreach ($details as $field => $value) {
            $this->assertSame($value, $account->fresh()->getAttribute($field));
        }
        $this->get('/sn/rates')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('bankAccounts.0.purpose', 'Bhadharai mari yekanzuru')
            ->where('bankAccounts.0.account_number', '00000001'));
        $this->get('/en/rates')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('bankAccounts.0.purpose', 'Pay council rates'));
    }

    public function test_project_update_ai_translation_generates_translations(): void
    {
        $this->actingAs($this->administrator());

        $project = CouncilProject::query()->create([
            'title' => 'Community Borehole Drilling',
            'slug' => 'community-borehole-drilling',
            'project_type' => 'project',
            'project_status' => 'ongoing',
            'location' => 'Ward 5',
            'summary' => 'Drilling boreholes for clean water.',
            'description' => 'Comprehensive borehole drilling project for clean accessible water.',
            'display_order' => 1,
            'status' => 'draft',
        ]);

        $details = [
            'council_project_id' => $project->id,
            'update_date' => '2026-05-10',
            'title' => 'Drilling Phase 1 Complete',
            'summary' => 'Initial surveying and drilling of three boreholes has finished.',
            'progress_percent' => 50,
            'display_order' => 1,
        ];

        $this->mock(GeminiStaffService::class, function ($mock): void {
            $mock->shouldReceive('translateFields')->once()
                ->with('ProjectUpdate', [
                    'title' => 'Drilling Phase 1 Complete',
                    'summary' => 'Initial surveying and drilling of three boreholes has finished.',
                ], 'sn')
                ->andReturn([
                    'success' => true,
                    'translations' => [
                        'title' => 'Chikamu Chekutanga Chekuchera Chapera',
                        'summary' => 'Kuongorora nekuchera zvibhorani zvitatu kwapera.',
                    ],
                ]);

            $mock->shouldReceive('translateFields')->once()
                ->with('ProjectUpdate', [
                    'title' => 'Drilling Phase 1 Complete',
                    'summary' => 'Initial surveying and drilling of three boreholes has finished.',
                ], 'nd')
                ->andReturn([
                    'success' => true,
                    'translations' => [
                        'title' => 'Isigaba Sokuqala Sokubhola Sesiphelile',
                        'summary' => 'Ukuhlola lokubhobhoza izibholani ezintathu sekuphelile.',
                    ],
                ]);
        });

        $page = Livewire::test(CreateProjectUpdate::class)
            ->fillForm($details)
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'sn'])
            ->assertHasNoActionErrors();

        $this->assertDatabaseCount('project_updates', 0);
        $page->call('create')->assertHasNoFormErrors();

        $update = ProjectUpdate::query()->sole();
        $this->assertSame('Chikamu Chekutanga Chekuchera Chapera', $update->translated('title', 'sn'));
        $this->assertSame('Kuongorora nekuchera zvibhorani zvitatu kwapera.', $update->translated('summary', 'sn'));

        Livewire::test(EditProjectUpdate::class, ['record' => $update->getRouteKey()])
            ->assertActionVisible('ai_translate')
            ->callAction('ai_translate', data: ['locale' => 'nd'])
            ->assertHasNoActionErrors();

        $this->assertSame('Isigaba Sokuqala Sokubhola Sesiphelile', $update->fresh()->translated('title', 'nd'));
        $this->assertSame('Ukuhlola lokubhobhoza izibholani ezintathu sekuphelile.', $update->fresh()->translated('summary', 'nd'));
    }
}
