<?php

namespace Tests\Feature;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Domain\Identity\DepartmentManager;
use App\Domain\Identity\RoleManager;
use App\Domain\Identity\UserManager;
use App\Filament\Pages\Profile;
use App\Models\AuditEvent;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Filament\Auth\Pages\Login;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityFoundationTest extends TestCase
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

    public function test_guest_and_ordinary_user_cannot_open_admin_records_directly(): void
    {
        $admin = $this->administrator();
        $department = Department::factory()->create();
        $event = new AuditEvent;
        $event->action = 'test';
        $event->subject_type = User::class;
        $event->subject_id = (string) $admin->id;
        $event->save();
        foreach (['/admin/users/'.$admin->id, '/admin/roles/'.Role::findByName('System Administrator')->id, '/admin/departments/'.$department->id, '/admin/audit-events/'.$event->id] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
        $ordinary = User::factory()->create();
        $this->actingAs($ordinary);
        foreach (['/admin/users/'.$admin->id, '/admin/roles/'.Role::findByName('System Administrator')->id, '/admin/departments/'.$department->id, '/admin/audit-events/'.$event->id] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_disabled_administrator_cannot_access_panel(): void
    {
        $admin = $this->administrator();
        $admin->forceFill(['status' => 'disabled'])->save();
        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }

    public function test_final_administrator_cannot_be_disabled_or_lose_role(): void
    {
        $admin = $this->administrator();
        $role = Role::findByName('System Administrator');
        try {
            app(UserManager::class)->setActive($admin, $admin, false);
            $this->fail('Self-disable succeeded');
        } catch (\Throwable $e) {
            $this->assertTrue($e instanceof AuthorizationException || $e instanceof ValidationException);
        }
        try {
            app(UserManager::class)->removeRole($admin, $admin, $role);
            $this->fail('Self-removal succeeded');
        } catch (ValidationException $e) {
            $this->assertTrue($admin->hasRole($role));
        }
    }

    public function test_role_permission_changes_are_audited_and_system_role_is_immutable(): void
    {
        $admin = $this->administrator();
        $role = Role::findByName('Website Administrator');
        app(RoleManager::class)->syncPermissions($admin, $role, ['admin.access', 'departments.view', 'departments.create']);
        $this->assertDatabaseHas('audit_events', ['actor_id' => $admin->id, 'action' => 'role.permissions_changed', 'subject_id' => (string) $role->id]);
        $this->expectException(AuthorizationException::class);
        app(RoleManager::class)->syncPermissions($admin, Role::findByName('System Administrator'), []);
    }

    public function test_department_lifecycle_is_authorized_and_audited(): void
    {
        $admin = $this->administrator();
        $other = User::factory()->create();
        try {
            app(DepartmentManager::class)->create($other, ['name' => 'Finance', 'code' => 'FIN']);
            $this->fail('Unauthorized create succeeded');
        } catch (AuthorizationException $e) {
            $this->assertDatabaseMissing('departments', ['code' => 'FIN']);
        }
        $department = app(DepartmentManager::class)->create($admin, ['name' => 'Finance', 'code' => 'FIN']);
        app(DepartmentManager::class)->setActive($admin, $department, false);
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'status' => 'inactive']);
        $this->assertDatabaseHas('audit_events', ['actor_id' => $admin->id, 'action' => 'department.deactivated']);
    }

    public function test_scope_checks_match_direct_record_and_query_results(): void
    {
        $admin = $this->administrator();
        $a = Department::factory()->create();
        $b = Department::factory()->create();
        $role = Role::findByName('Department Content Owner');
        $role->givePermissionTo('departments.view');
        $owner = User::factory()->create(['department_id' => $a->id]);
        app(UserManager::class)->assignRole($admin, $owner, $role, 'department', $a->id);
        $scope = app(DataScopeAuthorizer::class);
        $this->assertTrue($scope->allows($owner, 'departments.view', $a->id, null));
        $this->assertFalse($scope->allows($owner, 'departments.view', $b->id, null));
        $this->assertSame([$a->id], $scope->apply(Department::query(), $owner, 'departments.view', 'id', 'id')->pluck('id')->all());
        $this->actingAs($owner)->get('/admin/departments/'.$b->id)->assertNotFound();
    }

    public function test_audit_writer_redacts_sensitive_metadata(): void
    {
        $admin = $this->administrator();
        app(AuditWriter::class)->record($admin, 'test', $admin, ['password' => 'secret', 'token' => 'secret', 'nested' => ['session_id' => 'secret', 'safe' => 'ok']]);
        $event = AuditEvent::query()->latest('id')->firstOrFail();
        $this->assertSame(['nested' => ['safe' => 'ok']], $event->metadata);
    }

    public function test_user_creation_normalizes_email_and_emits_safe_audit(): void
    {
        $admin = $this->administrator();
        $user = app(UserManager::class)->create($admin, ['name' => 'New Staff', 'email' => ' NEW@EXAMPLE.COM ']);
        $this->assertSame('new@example.com', $user->email);
        $this->assertDatabaseHas('audit_events', ['actor_id' => $admin->id, 'action' => 'user.created', 'subject_id' => (string) $user->id]);
        $this->assertStringNotContainsString($user->password, AuditEvent::query()->latest('id')->firstOrFail()->toJson());
    }

    public function test_global_and_own_scopes_filter_users_and_direct_urls(): void
    {
        $admin = $this->administrator();
        $other = User::factory()->create();
        $role = Role::create(['name' => 'Scoped Reviewer', 'guard_name' => 'web']);
        $role->givePermissionTo(['admin.access', 'users.view']);
        $reviewer = User::factory()->create();
        app(UserManager::class)->assignRole($admin, $reviewer, $role, 'own');
        $scope = app(DataScopeAuthorizer::class);
        $this->assertTrue($scope->allows($reviewer, 'users.view', null, $reviewer->id));
        $this->assertFalse($scope->allows($reviewer, 'users.view', null, $other->id));
        $this->assertSame([$reviewer->id], $scope->apply(User::query(), $reviewer, 'users.view', 'department_id', 'id')->pluck('id')->all());
        $this->actingAs($reviewer)->get('/admin/users/'.$other->id)->assertNotFound();
        app(UserManager::class)->assignRole($admin, $reviewer, $role, 'global');
        $this->assertTrue($scope->allows($reviewer, 'users.view', null, $other->id));
        $this->assertCount(3, $scope->apply(User::query(), $reviewer, 'users.view', 'department_id', 'id')->get());
    }

    public function test_invalid_scope_combinations_are_rejected(): void
    {
        $admin = $this->administrator();
        $other = User::factory()->create();
        $department = Department::factory()->create();
        $role = Role::findByName('Website Administrator');
        foreach ([['global', $department->id], ['department', null], ['own', $department->id]] as [$scope,$departmentId]) {
            try {
                app(UserManager::class)->assignRole($admin, $other, $role, $scope, $departmentId);
                $this->fail('Invalid scope was accepted');
            } catch (ValidationException $e) {
                $this->assertFalse($other->hasRole($role));
            }
        }
    }

    public function test_audit_resource_is_read_only_for_administrators(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin);
        $this->get('/admin/audit-events/create')->assertNotFound();
        $event = new AuditEvent;
        $event->action = 'test';
        $event->subject_type = User::class;
        $event->subject_id = (string) $admin->id;
        $event->save();
        $this->get('/admin/audit-events/'.$event->id.'/edit')->assertNotFound();
        $this->assertFalse($admin->can('update', $event));
        $this->assertFalse($admin->can('delete', $event));
    }

    public function test_legacy_flag_alone_grants_no_panel_access(): void
    {
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_disabled_account_cannot_log_in_with_correct_password(): void
    {
        $admin = $this->administrator();
        $admin->forceFill(['status' => 'disabled', 'password' => 'correct-password'])->save();
        Livewire::test(Login::class)->set('data.email', $admin->email)->set('data.password', 'correct-password')->call('authenticate');
        $this->assertGuest();
    }

    public function test_system_administrator_role_edit_url_is_denied(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin)->get('/admin/roles/'.Role::findByName('System Administrator')->id.'/edit')->assertForbidden();
    }

    public function test_seed_is_idempotent_and_creates_no_accounts(): void
    {
        $this->seed(SecuritySeeder::class);
        $this->seed(SecuritySeeder::class);
        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount('permissions', 15);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_is_rejected_after_normalization(): void
    {
        $admin = $this->administrator();
        $existing = User::factory()->create(['email' => 'staff@example.com']);
        $this->expectException(ValidationException::class);
        app(UserManager::class)->create($admin, ['name' => 'Duplicate', 'email' => ' STAFF@EXAMPLE.COM ']);
    }

    public function test_editing_disabled_user_does_not_reactivate_account(): void
    {
        $admin = $this->administrator();
        $subject = User::factory()->create(['status' => 'disabled']);
        app(UserManager::class)->update($admin, $subject, ['name' => 'Edited Name', 'email' => $subject->email]);
        $this->assertSame('disabled', $subject->fresh()->status);
    }

    public function test_role_without_scope_cannot_enter_panel(): void
    {
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $user->assignRole('System Administrator');
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_mysql_rejects_scope_and_department_mismatch(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('MySQL CHECK constraint verification.');
        }

        $admin = $this->administrator();
        $department = Department::factory()->create();
        $this->expectException(QueryException::class);
        DB::table('user_role_scopes')->where('user_id', $admin->id)->update(['scope_type' => 'global', 'department_id' => $department->id]);
    }

    public function test_department_with_active_user_or_scope_cannot_be_deactivated(): void
    {
        $admin = $this->administrator();
        $department = Department::factory()->create();
        $user = User::factory()->create(['department_id' => $department->id]);
        try {
            app(DepartmentManager::class)->setActive($admin, $department, false);
            $this->fail('Active-user department was deactivated.');
        } catch (ValidationException $exception) {
            $this->assertSame('active', $department->fresh()->status);
        }
        $user->forceFill(['department_id' => null])->save();
        $role = Role::findByName('Department Content Owner');
        app(UserManager::class)->assignRole($admin, $user, $role, 'department', $department->id);
        $this->expectException(ValidationException::class);
        app(DepartmentManager::class)->setActive($admin, $department, false);
    }

    public function test_mfa_secret_and_recovery_codes_are_encrypted_and_hidden(): void
    {
        $user = User::factory()->create();
        $user->saveAppAuthenticationSecret('mfa-test-secret');
        $user->saveAppAuthenticationRecoveryCodes(['recovery-test-code']);
        $stored = DB::table('users')->where('id', $user->id)->first();
        $this->assertNotSame('mfa-test-secret', $stored->app_authentication_secret);
        $this->assertStringNotContainsString('recovery-test-code', $stored->app_authentication_recovery_codes);
        $this->assertSame('mfa-test-secret', $user->fresh()->getAppAuthenticationSecret());
        $this->assertSame(['recovery-test-code'], $user->fresh()->getAppAuthenticationRecoveryCodes());
        $this->assertArrayNotHasKey('app_authentication_secret', $user->toArray());
        $this->assertArrayNotHasKey('app_authentication_recovery_codes', $user->toArray());
        $this->assertDatabaseHas('audit_events', ['actor_id' => $user->id, 'action' => 'auth.mfa_secret_changed']);
        $this->assertDatabaseHas('audit_events', ['actor_id' => $user->id, 'action' => 'auth.mfa_recovery_changed']);
    }

    public function test_staff_profile_change_is_audited_without_sensitive_values(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin)->get('/admin/profile')->assertOk();
        Livewire::test(Profile::class)
            ->set('data.name', 'Updated Staff Name')
            ->call('save');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'name' => 'Updated Staff Name']);
        $this->assertDatabaseHas('audit_events', ['actor_id' => $admin->id, 'action' => 'user.profile_updated']);
    }
}
