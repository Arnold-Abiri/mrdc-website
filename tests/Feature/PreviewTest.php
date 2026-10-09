<?php

namespace Tests\Feature;

use App\Models\CouncilMeeting;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\HomepageSlide;
use App\Models\Media;
use App\Models\Page;
use Database\Seeders\NewsIllustrationSeeder;
use Database\Seeders\Stage4MutokoContentSeeder;
use Database\Seeders\StakeholderDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsigned_preview_home_is_forbidden(): void
    {
        $this->get('/preview/en')->assertForbidden();
    }

    public function test_unsigned_preview_page_is_forbidden(): void
    {
        $this->get('/preview/en/pages/about-mutoko')->assertForbidden();
    }

    public function test_signed_preview_home_shows_draft_content_with_noindex(): void
    {
        $this->seed([Stage4MutokoContentSeeder::class, StakeholderDemoSeeder::class]);

        $url = URL::temporarySignedRoute('preview.home', now()->addHour(), ['locale' => 'en']);

        $this->get($url)
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home', false)
                ->where('preview', true)
                ->has('services', 9)
                ->has('news', 3)
                ->has('notices', 3)
                ->has('meetings', 2)
                ->has('slides', 3));
    }

    public function test_seeded_news_illustrations_are_managed_and_labelled(): void
    {
        Storage::fake(config('cms.media_disk'));
        $this->seed(StakeholderDemoSeeder::class);

        $items = EditorialItem::query()->where('type', 'news')->where('slug', 'like', 'demo-%')->with('featuredMedia')->get();
        $this->assertCount(6, $items);

        foreach ($items as $item) {
            $this->assertNotNull($item->featuredMedia);
            $this->assertStringStartsWith('AI-generated editorial illustration', $item->featuredMedia->caption);
            $this->assertSame('image/webp', $item->featuredMedia->mime_type);
            Storage::disk(config('cms.media_disk'))->assertExists($item->featuredMedia->storage_path);
            $this->get(route('managed-media.show', ['locale' => 'en', 'media' => $item->featured_media_id]))->assertOk()->assertHeader('Content-Type', 'image/webp');
        }

        $this->get('/en/news/demo-understanding-mrdc-role')->assertOk()->assertSee('AI-generated editorial illustration');

        EditorialItem::query()->where('type', 'news')->where('slug', 'like', 'demo-%')->update(['featured_media_id' => null]);
        $this->seed(NewsIllustrationSeeder::class);
        $this->assertSame(6, EditorialItem::query()->where('type', 'news')->where('slug', 'like', 'demo-%')->whereNotNull('featured_media_id')->count());
        $this->assertSame(6, Media::query()->where('storage_path', 'like', 'news/illustrations/%')->count());
    }

    public function test_preview_page_shows_draft_after_home_unlocks_session(): void
    {
        $this->seed([Stage4MutokoContentSeeder::class, StakeholderDemoSeeder::class]);

        $url = URL::temporarySignedRoute('preview.home', now()->addHour(), ['locale' => 'en']);
        $this->get($url)->assertOk();

        $this->get('/preview/en/pages/about-mutoko')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertInertia(fn (Assert $page) => $page
                ->component('CmsPage', false)
                ->where('preview', true));
    }

    public function test_welcome_slide_is_public_and_draft_slide_image_requires_preview(): void
    {
        Storage::fake(config('cms.media_disk'));
        $this->seed(StakeholderDemoSeeder::class);

        $welcome = HomepageSlide::query()->where('headline', 'Mutoko Rural District Council')->firstOrFail();
        $draft = HomepageSlide::query()->where('headline', 'Roads, Water and Growth Points')->firstOrFail();
        $this->assertNotNull($welcome->media_id);
        $this->assertNotNull($draft->media_id);
        $welcomeImageUrl = route('managed-media.show', ['locale' => 'en', 'media' => $welcome->media_id]);
        $draftImageUrl = route('managed-media.show', ['locale' => 'en', 'media' => $draft->media_id]);

        $this->get('/en')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home', false)
            ->has('slides', 1)
            ->where('slides.0.headline', 'Mutoko Rural District Council')
            ->where('slides.0.supporting_text', 'Working with our communities to deliver quality services, promote local development and build a better Mutoko.')
            ->where('slides.0.image_url', $welcomeImageUrl));
        $this->get($welcomeImageUrl)->assertOk()->assertHeader('Content-Type', 'image/webp');
        $this->get($draftImageUrl)->assertNotFound();

        $previewUrl = URL::temporarySignedRoute('preview.home', now()->addHour(), ['locale' => 'en']);
        $this->get($previewUrl)->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home', false)
            ->has('slides', 3)
            ->where('slides.1.image_url', $draftImageUrl));
        $this->get($draftImageUrl)->assertOk()->assertHeader('Content-Type', 'image/webp');
    }

    public function test_reseeding_does_not_replace_edited_slides(): void
    {
        Storage::fake(config('cms.media_disk'));
        $this->seed(StakeholderDemoSeeder::class);

        $slide = HomepageSlide::query()->where('headline', 'Mutoko Rural District Council')->firstOrFail();
        $slide->headline = 'Updated council highlight';
        $slide->save();

        $this->seed(StakeholderDemoSeeder::class);

        $this->assertSame(3, HomepageSlide::query()->count());
        $this->assertSame('Updated council highlight', $slide->fresh()->headline);
    }

    public function test_public_homepage_shows_verified_content_without_preview_flag(): void
    {
        $this->seed([Stage4MutokoContentSeeder::class, StakeholderDemoSeeder::class]);

        $this->get('/en')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home', false)
            ->has('services', 9)
            ->has('news', 3)
            ->where('ward_count', 29)
            ->missing('preview'));

        $this->assertTrue(Page::query()->public()->where('slug', 'about-mutoko')->exists());
        $this->get('/en/pages/about-mutoko')->assertOk();
        $this->get('/en/about')->assertOk()->assertInertia(fn (Assert $page) => $page->component('About', false)->missing('page.content_pending'));
    }

    public function test_preview_routes_are_not_in_sitemap(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/preview/');
    }

    public function test_preview_detail_pages_resolve_for_draft_records(): void
    {
        $this->seed([Stage4MutokoContentSeeder::class, StakeholderDemoSeeder::class]);

        $home = URL::temporarySignedRoute('preview.home', now()->addHour(), ['locale' => 'en']);
        $this->get($home)->assertOk();

        $meetingId = CouncilMeeting::query()->firstOrFail()->id;

        $this->get('/preview/en/services/education-services')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/preview/en/news/demo-understanding-mrdc-role')->assertOk();
        $this->get('/preview/en/notices/how-service-updates-are-communicated')->assertOk();
        $this->get('/preview/en/documents/demo-public-enquiry-guide')->assertOk();
        $this->get('/preview/en/wards/ward-01')->assertOk();
        $this->get('/preview/en/officials/demo-office-ceo')->assertOk();
        $this->get('/preview/en/investment/solar-energy-mutoko')->assertOk();
        $this->get("/preview/en/meetings/{$meetingId}")->assertOk();
    }

    public function test_preview_download_serves_demo_guide_in_preview_session(): void
    {
        $this->seed([Stage4MutokoContentSeeder::class, StakeholderDemoSeeder::class]);

        // Published guides download publicly.
        $this->get('/en/documents/demo-public-enquiry-guide/download')->assertOk();

        // A draft guide stays hidden publicly but downloads in a preview session.
        $guide = Document::query()->where('slug', 'demo-community-participation-guide')->firstOrFail();
        $guide->status = 'draft';
        $guide->save();
        $this->get('/en/documents/demo-community-participation-guide/download')->assertNotFound();

        $home = URL::temporarySignedRoute('preview.home', now()->addHour(), ['locale' => 'en']);
        $this->get($home)->assertOk();

        $this->get('/en/documents/demo-community-participation-guide/download')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
