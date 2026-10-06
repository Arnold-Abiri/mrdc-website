<?php

namespace Tests\Feature;

use App\Domain\Identity\DepartmentManager;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicDepartmentTest extends TestCase
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

    public function test_authoritative_department_profile_requires_verification_and_publication(): void
    {
        $admin = $this->administrator();
        $department = Department::factory()->create();
        $manager = app(DepartmentManager::class);
        $manager->update($admin, $department, ['name' => $department->name, 'code' => $department->code, 'description' => $department->description, 'sort_order' => $department->sort_order, 'public_name' => 'Development review profile', 'public_summary' => 'Example test only', 'public_description' => 'Isolated fixture.', 'responsibilities' => ['Review requests'], 'public_display_order' => 4]);
        $this->get('/en/departments')->assertOk()->assertDontSee('Development review profile');
        $this->get('/en/search?q=Development')->assertOk()->assertDontSee('Development review profile');
        $manager->setPublicVerification($admin, $department, 'publishable');
        $manager->setPublicStatus($admin, $department, 'published');
        $this->get('/en/departments')->assertSee('Development review profile');
        $this->get('/en/departments/'.$department->id)->assertOk()->assertSee('Review requests');
        $this->get('/en/search?q=Development')->assertSee('Development review profile');
        $manager->setPublicStatus($admin, $department, 'unpublished');
        $this->get('/en/departments/'.$department->id)->assertNotFound();
    }
}
