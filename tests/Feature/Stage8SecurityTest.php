<?php

namespace Tests\Feature;

use App\Domain\Cms\CouncilProjectManager;
use App\Domain\Cms\TenderManager;
use App\Domain\Cms\VacancyManager;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Stage8SecurityTest extends TestCase
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

    private function scopedStaff(Department $department): User
    {
        $role = Role::firstOrCreate(['name' => 'QA scoped', 'guard_name' => 'web']);
        $role->givePermissionTo(['admin.access', 'tenders.view', 'enquiries.view']);
        $user = User::factory()->create(['department_id' => $department->id]);
        $user->assignRole($role);
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'department', 'department_id' => $department->id, 'created_at' => now(), 'updated_at' => now()]);

        return $user;
    }

    public function test_guest_disabled_and_limited_users_are_denied_admin_and_reports(): void
    {
        $this->administrator();
        $this->get('/admin/analytics-dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/audit-report')->assertRedirect('/admin/login');
        $this->get('/admin/system-health')->assertRedirect('/admin/login');
        $disabled = User::factory()->create(['status' => 'disabled']);
        $this->assertFalse($disabled->canAccessPanel(filament()->getPanel('admin')));
        $limited = User::factory()->create();
        $this->actingAs($limited)->get('/admin/analytics-dashboard')->assertForbidden();
        $this->actingAs($limited)->get('/admin/reports/audit-export')->assertForbidden();
    }

    public function test_department_scoped_staff_cannot_reach_other_departments_records(): void
    {
        $admin = $this->administrator();
        $own = Department::factory()->create();
        $other = Department::factory()->create();
        $staff = $this->scopedStaff($own);
        $manager = app(TenderManager::class);
        $foreign = $manager->create($admin, ['reference' => 'MRDC/2026/040', 'slug' => 'qa-foreign', 'title' => 'QA foreign', 'description' => 'x', 'lifecycle_status' => 'open', 'display_order' => 0, 'department_id' => $other->id]);
        $this->assertFalse($staff->can('view', $foreign));
        $this->assertFalse($staff->can('update', $foreign));
        $this->assertFalse($staff->can('award', $foreign));
        $ownTender = $manager->create($admin, ['reference' => 'MRDC/2026/041', 'slug' => 'qa-own', 'title' => 'QA own', 'description' => 'x', 'lifecycle_status' => 'open', 'display_order' => 0, 'department_id' => $own->id]);
        $this->assertTrue($staff->can('view', $ownTender));
    }

    public function test_stored_xss_payloads_are_escaped_on_public_pages_and_search(): void
    {
        $admin = $this->administrator();
        $manager = app(CouncilProjectManager::class);
        $project = $manager->create($admin, ['title' => 'QA xss proj', 'slug' => 'qa-xss-proj', 'project_type' => 'project', 'description' => '<script>alert(1)</script> plain', 'project_status' => 'ongoing', 'display_order' => 0]);
        $manager->setVerification($admin, $project, 'publishable');
        $manager->setStatus($admin, $project, 'published');
        $this->get('/en/projects/qa-xss-proj')->assertOk()->assertDontSee('<script>alert(1)', false);
        $this->get('/en/search?q=xss')->assertOk()->assertDontSee('<script>alert(1)', false);
    }

    public function test_state_changing_public_endpoints_retain_csrf_protection(): void
    {
        foreach (['contact.store', 'feedback.store', 'locale.update'] as $route) {
            $middleware = app('router')->getRoutes()->getByName($route)?->gatherMiddleware() ?? [];
            $this->assertContains('web', $middleware);
        }
        $this->assertTrue(collect(app('router')->getMiddlewareGroups()['web'] ?? [])->contains(fn ($middleware): bool => str_contains((string) $middleware, 'VerifyCsrfToken') || str_contains((string) $middleware, 'PreventRequestForgery')));
    }

    public function test_protected_lifecycle_fields_cannot_be_mass_assigned(): void
    {
        $admin = $this->administrator();
        $tender = app(TenderManager::class)->create($admin, ['reference' => 'MRDC/2026/042', 'slug' => 'qa-mass', 'title' => 'QA mass', 'description' => 'x', 'lifecycle_status' => 'open', 'display_order' => 0, 'status' => 'published', 'verification_status' => 'publishable', 'created_by' => 9999]);
        $this->assertSame('draft', $tender->fresh()->status);
        $this->assertSame('demo', $tender->fresh()->verification_status);
        $this->assertSame($admin->id, $tender->created_by);
        $vacancy = app(VacancyManager::class)->create($admin, ['slug' => 'qa-mass-v', 'title' => 'QA mass v', 'description' => 'x', 'display_order' => 0, 'status' => 'published']);
        $this->assertSame('draft', $vacancy->fresh()->status);
    }

    public function test_public_submission_abuse_controls_hold(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/en/contact', ['name' => 'QA', 'email' => 'qa@example.test', 'category' => 'general', 'subject' => 'QA subject', 'message' => 'Development-only message body.', '_token' => csrf_token()])->assertRedirect();
        }
        $this->post('/en/contact', ['name' => 'QA', 'email' => 'qa@example.test', 'category' => 'general', 'subject' => 'QA subject', 'message' => 'Development-only message body.', '_token' => csrf_token()])->assertStatus(429);
    }

    public function test_analytics_and_reports_are_absent_from_public_surfaces(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/admin', false);
        $this->get('/en/search?q=analytics')->assertOk()->assertDontSee('analytics-dashboard', false);
    }
}
