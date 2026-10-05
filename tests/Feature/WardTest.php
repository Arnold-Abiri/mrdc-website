<?php

namespace Tests\Feature;

use App\Domain\Cms\WardManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WardTest extends TestCase
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

    public function test_ward_directory_is_empty_until_verified_and_published(): void
    {
        $admin = $this->administrator();
        $manager = app(WardManager::class);
        $ward = $manager->create($admin, ['slug' => 'development-fixture-ward', 'name' => 'Development fixture ward', 'description' => 'Test data only', 'boundaries_description' => 'No real boundary', 'display_order' => 1]);
        $this->get('/wards')->assertDontSee('Development fixture ward');
        $this->get('/wards/development-fixture-ward')->assertNotFound();
        $manager->setVerification($admin, $ward, 'publishable');
        $manager->setStatus($admin, $ward, 'published');
        $this->get('/wards')->assertSee('Development fixture ward');
        $this->get('/wards/development-fixture-ward')->assertOk()->assertSee('No real boundary');
        $this->get('/search?q=fixture')->assertSee('Development fixture ward');
        $manager->update($admin, $ward, ['slug' => 'development-fixture-ward', 'name' => 'Edited fixture', 'description' => 'Updated', 'boundaries_description' => 'Pending verification', 'display_order' => 1]);
        $this->get('/wards/development-fixture-ward')->assertNotFound();
        $this->assertSame('demo', $ward->fresh()->verification_status);
        $this->assertDatabaseHas('audit_events', ['action' => 'wards.updated', 'subject_id' => (string) $ward->id]);
    }
}
