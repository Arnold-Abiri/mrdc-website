<?php

namespace Tests\Feature;

use App\Domain\Cms\ServiceManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceTest extends TestCase
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

    private function payload(): array
    {
        return ['slug' => 'development-test-service', 'name' => 'Development test service', 'summary' => 'A test only description', 'description' => 'Development fixture, not council information.', 'requirements' => ['Proof of identity'], 'steps' => ['Submit the form'], 'display_order' => 1];
    }

    public function test_service_is_public_only_after_explicit_verification_and_publication(): void
    {
        $admin = $this->administrator();
        $manager = app(ServiceManager::class);
        $service = $manager->create($admin, $this->payload());
        $this->get('/services')->assertOk()->assertDontSee('Development test service');
        $this->get('/services/development-test-service')->assertNotFound();
        $manager->setVerification($admin, $service, 'publishable');
        $manager->setStatus($admin, $service, 'published');
        $this->get('/services')->assertOk()->assertSee('Development test service');
        $this->get('/services/development-test-service')->assertOk()->assertSee('Proof of identity');
        $this->get('/search?q=Development')->assertOk()->assertSee('Development test service');
        $manager->update($admin, $service, [...$this->payload(), 'name' => 'Updated service']);
        $this->get('/services/development-test-service')->assertNotFound();
        $this->assertDatabaseHas('audit_events', ['action' => 'services.updated', 'subject_id' => (string) $service->id]);
    }

    public function test_unauthorized_user_cannot_create_or_publish_services(): void
    {
        $admin = $this->administrator();
        $service = app(ServiceManager::class)->create($admin, $this->payload());
        $ordinary = User::factory()->create();
        $this->expectException(AuthorizationException::class);
        app(ServiceManager::class)->setStatus($ordinary, $service, 'published');
    }
}
