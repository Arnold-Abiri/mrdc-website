<?php

namespace Tests\Feature;

use App\Domain\Cms\EditorialManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditorialTest extends TestCase
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

    private function payload(string $type = 'notice'): array
    {
        return ['type' => $type, 'slug' => 'development-item', 'title' => 'Development review notice', 'summary' => 'Testing only', 'body' => '<script>alert(1)</script> plain text content', 'category' => 'development', 'is_urgent' => false, 'display_order' => 0];
    }

    public function test_draft_notice_stays_private_and_expired_notice_is_excluded(): void
    {
        $admin = $this->administrator();
        $manager = app(EditorialManager::class);
        $item = $manager->create($admin, $this->payload());
        $this->get('/notices')->assertDontSee('Development review notice');
        $this->get('/notices/development-item')->assertNotFound();
        $this->get('/search?q=Development')->assertDontSee('Development review notice');
        $manager->setVerification($admin, $item, 'publishable');
        $manager->setStatus($admin, $item, 'published');
        $this->get('/notices/development-item')->assertOk()->assertSee('\\u003Cscript\\u003Ealert(1)', false)->assertDontSee('<script>alert');
        $this->get('/search?q=Development')->assertSee('Development review notice');
        $item->forceFill(['expires_at' => today()->subDay()])->save();
        $this->get('/notices')->assertDontSee('Development review notice');
        $this->get('/notices/development-item')->assertNotFound();
        $this->get('/search?q=Development')->assertDontSee('Development review notice');
    }

    public function test_editing_published_news_returns_it_to_demo_draft_and_staff_cannot_publish(): void
    {
        $admin = $this->administrator();
        $manager = app(EditorialManager::class);
        $item = $manager->create($admin, $this->payload('news'));
        $manager->setVerification($admin, $item, 'publishable');
        $manager->setStatus($admin, $item, 'published');
        $manager->update($admin, $item, [...$this->payload('news'), 'title' => 'Changed editorial item']);
        $this->assertSame('draft', $item->fresh()->status);
        $this->assertSame('demo', $item->fresh()->verification_status);
        $this->get('/news/development-item')->assertNotFound();
        $this->expectException(AuthorizationException::class);
        $manager->setStatus(User::factory()->create(), $item->fresh(), 'published');
    }
}
