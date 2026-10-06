<?php

namespace Tests\Feature;

use App\Domain\Cms\PageManager;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsPageTest extends TestCase
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

    private function content(string $slug = 'about-mutoko'): array
    {
        return ['slug' => $slug, 'title' => 'About Mutoko', 'summary' => 'Development review content.', 'blocks' => [['type' => 'paragraph', 'text' => 'Council information for review.']]];
    }

    public function test_pages_are_private_until_published_and_revisions_record_each_change(): void
    {
        $admin = $this->administrator();
        $manager = app(PageManager::class);
        $page = $manager->create($admin, $this->content());
        $this->get('/en/pages/about-mutoko')->assertNotFound();
        $manager->update($admin, $page, [...$this->content(), 'title' => 'Updated about Mutoko']);
        $this->assertSame(2, $page->revisions()->count());
        $manager->setStatus($admin, $page, 'published');
        $this->get('/en/pages/about-mutoko')->assertOk()->assertSee('Updated about Mutoko');
        $manager->setStatus($admin, $page, 'unpublished');
        $this->get('/en/pages/about-mutoko')->assertNotFound();
        $this->assertSame(4, $page->revisions()->count());
        $this->assertDatabaseHas('audit_events', ['action' => 'pages.unpublished', 'subject_id' => (string) $page->id]);
    }

    public function test_editing_a_published_page_returns_it_to_draft_and_review_state(): void
    {
        $admin = $this->administrator();
        $manager = app(PageManager::class);
        $page = $manager->create($admin, $this->content());
        $manager->setVerification($admin, $page, 'publishable');
        $manager->setStatus($admin, $page, 'published');
        $manager->update($admin, $page, [...$this->content(), 'title' => 'Revised']);
        $page->refresh();
        $this->assertSame('draft', $page->status);
        $this->assertSame('demo', $page->verification_status);
        $this->assertNull($page->published_at);
        $this->get('/en/pages/about-mutoko')->assertNotFound();
    }

    public function test_duplicate_slugs_and_unsafe_links_are_rejected(): void
    {
        $admin = $this->administrator();
        $manager = app(PageManager::class);
        $manager->create($admin, $this->content());
        try {
            $manager->create($admin, $this->content());
            $this->fail('Duplicate slug was accepted.');
        } catch (ValidationException) {
            $this->assertSame(1, Page::query()->count());
        }
        $this->expectException(ValidationException::class);
        $manager->create($admin, [...$this->content('unsafe'), 'blocks' => [['type' => 'cta', 'text' => 'Visit', 'url' => '//evil.example']]]);
    }

    public function test_search_only_returns_published_pages_and_escapes_wildcards(): void
    {
        $admin = $this->administrator();
        $manager = app(PageManager::class);
        $published = $manager->create($admin, $this->content('public-water'));
        $manager->update($admin, $published, [...$this->content('public-water'), 'title' => 'Water services']);
        $manager->setStatus($admin, $published, 'published');
        $manager->create($admin, [...$this->content('private-water'), 'title' => 'Water draft']);

        $this->get('/en/search?q=Water')->assertOk()->assertSee('Water services')->assertDontSee('Water draft');
        $this->get('/en/search?q=%25')->assertOk()->assertDontSee('Water services');
        $this->get('/en/search?q='.str_repeat('a', 101))->assertUnprocessable();
        $this->get('/en/search?q%5B%5D=Water')->assertUnprocessable();
    }

    public function test_ordinary_staff_cannot_publish(): void
    {
        $admin = $this->administrator();
        $page = app(PageManager::class)->create($admin, $this->content());
        $ordinary = User::factory()->create();
        $this->expectException(AuthorizationException::class);
        app(PageManager::class)->setStatus($ordinary, $page, 'published');
    }

    public function test_restoring_a_revision_preserves_history_resets_publication_and_records_actor(): void
    {
        $admin = $this->administrator();
        $manager = app(PageManager::class);
        $page = $manager->create($admin, $this->content('restore-check'));
        $original = $page->revisions()->firstOrFail();
        $manager->update($admin, $page, [...$this->content('restore-check'), 'title' => 'Edited title']);
        $manager->setVerification($admin, $page, 'publishable');
        $manager->setStatus($admin, $page, 'published');

        $unauthorized = User::factory()->create();
        try {
            $manager->restore($unauthorized, $page, $original);
            $this->fail('Unauthorized revision restore was accepted.');
        } catch (AuthorizationException) {
            $this->assertSame('published', $page->fresh()->status);
        }

        $manager->restore($admin, $page, $original);
        $page->refresh();
        $this->assertSame('About Mutoko', $page->title);
        $this->assertSame('draft', $page->status);
        $this->assertSame('demo', $page->verification_status);
        $this->assertNull($page->published_at);
        $this->assertSame(5, $page->revisions()->count());
        $this->get('/en/pages/restore-check')->assertNotFound();
        $this->assertDatabaseHas('audit_events', ['actor_id' => $admin->id, 'action' => 'pages.restored', 'subject_id' => (string) $page->id]);
        $manager->setVerification($admin, $page, 'publishable');
        $manager->setStatus($admin, $page, 'published');
        $this->get('/en/pages/restore-check')->assertOk()->assertSee('About Mutoko')->assertDontSee('Edited title');
    }
}
