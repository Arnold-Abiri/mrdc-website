<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\Stage4MutokoContentSeeder;
use Database\Seeders\StakeholderDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                ->has('notices', 3));
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

    public function test_public_homepage_stays_empty_while_preview_has_content(): void
    {
        $this->seed(StakeholderDemoSeeder::class);

        $this->get('/en')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home', false)
            ->has('services', 0)
            ->has('news', 0)
            ->missing('preview'));

        $this->assertFalse(Page::query()->public()->where('slug', 'about-mutoko')->exists());
        $this->get('/en/pages/about-mutoko')->assertNotFound();
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

        $this->get('/preview/en/services/education-services')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/preview/en/news/demo-understanding-mrdc-role')->assertOk();
        $this->get('/preview/en/notices/sample-service-announcement')->assertOk();
        $this->get('/preview/en/documents/demo-public-enquiry-guide')->assertOk();
        $this->get('/preview/en/wards/ward-01')->assertOk();
        $this->get('/preview/en/officials/demo-office-ceo')->assertOk();
        $this->get('/preview/en/investment/solar-energy-mutoko')->assertOk();
    }

    public function test_preview_download_serves_demo_guide_in_preview_session(): void
    {
        $this->seed([Stage4MutokoContentSeeder::class, StakeholderDemoSeeder::class]);

        $this->get('/en/documents/demo-public-enquiry-guide/download')->assertNotFound();

        $home = URL::temporarySignedRoute('preview.home', now()->addHour(), ['locale' => 'en']);
        $this->get($home)->assertOk();

        $this->get('/en/documents/demo-public-enquiry-guide/download')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
