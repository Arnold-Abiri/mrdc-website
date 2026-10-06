<?php

use App\Domain\Cms\EnquiryManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function enquiryPayload(): array
{
    return [
        'name' => 'Resident',
        'email' => 'resident@example.test',
        'category' => 'general',
        'subject' => 'A service question',
        'message' => 'Please provide more information about this service.',
    ];
}

function scopedEnquiryUser(Department $department): User
{
    $role = Role::findOrCreate('Enquiry Handler', 'web');
    $role->givePermissionTo(['admin.access', 'enquiries.view', 'enquiries.update']);
    $user = User::factory()->create(['department_id' => $department->id]);
    $user->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'department',
        'department_id' => $department->id, 'created_at' => now(), 'updated_at' => now(),
    ]);

    return $user;
}

test('visitor can submit an enquiry without controlling staff fields or reading it back', function (): void {
    $this->post('/en/contact', [...enquiryPayload(), 'status' => 'resolved', 'assigned_to' => 1])
        ->assertRedirect('/en/contact?submitted=1');
    $enquiry = Enquiry::query()->sole();
    expect($enquiry->status)->toBe('new')->and($enquiry->assigned_to)->toBeNull()
        ->and($enquiry->public_id)->not->toBeEmpty();
    $this->get('/en/contact/'.$enquiry->public_id)->assertNotFound();
    $this->get('/en/enquiries')->assertNotFound();
    $this->get('/admin/enquiries')->assertRedirect();
});

test('invalid and bot submissions are rejected', function (): void {
    $this->post('/en/contact', [...enquiryPayload(), 'website' => 'spam.example'])->assertSessionHasErrors('website');
    $this->post('/en/contact', [...enquiryPayload(), 'email' => 'invalid'])->assertSessionHasErrors('email');
    $this->post('/en/contact', [...enquiryPayload(), 'message' => '<script>'])->assertSessionHasErrors('message');
    expect(Enquiry::query()->count())->toBe(0);
});

test('enquiry endpoint is throttled', function (): void {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post('/en/contact', enquiryPayload())->assertRedirect();
    }
    $this->post('/en/contact', enquiryPayload())->assertStatus(429);
});

test('department scope applies to enquiry lists, direct records and administrative updates', function (): void {
    $this->seed(SecuritySeeder::class);
    $ownDepartment = Department::factory()->create();
    $otherDepartment = Department::factory()->create();
    $actor = scopedEnquiryUser($ownDepartment);
    $own = Enquiry::factory()->create(['department_id' => $ownDepartment->id]);
    $other = Enquiry::factory()->create(['department_id' => $otherDepartment->id]);
    $scoped = app(DataScopeAuthorizer::class)
        ->apply(Enquiry::query(), $actor, 'enquiries.view', 'department_id', null)->pluck('id')->all();
    expect($scoped)->toContain($own->id)->not->toContain($other->id);
    expect($actor->can('view', $own))->toBeTrue()->and($actor->can('view', $other))->toBeFalse();
    expect($actor->can('update', $other))->toBeFalse();
    $this->actingAs($actor)->get('/admin/enquiries/'.$own->id)->assertOk();
    $this->actingAs($actor)->get('/admin/enquiries/'.$other->id)->assertNotFound();
    $manager = app(EnquiryManager::class);
    $manager->setStatus($actor, $own, 'in_progress');
    expect($own->fresh()->status)->toBe('in_progress');
    $this->assertDatabaseHas('audit_events', ['action' => 'enquiries.status_changed', 'subject_id' => (string) $own->id]);
    expect(fn () => $manager->setStatus($actor, $other, 'resolved'))->toThrow(AuthorizationException::class);
    expect($other->fresh()->status)->toBe('new');
});

test('own scoped enquiry permission cannot expose a matching numeric record id', function (): void {
    $this->seed(SecuritySeeder::class);
    $role = Role::findOrCreate('Own Enquiry Reader', 'web');
    $role->givePermissionTo('enquiries.view');
    $actor = User::factory()->create();
    $actor->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $actor->id, 'role_id' => $role->id, 'scope_type' => 'own',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    Enquiry::factory()->create(['id' => $actor->id]);
    $visible = app(DataScopeAuthorizer::class)
        ->apply(Enquiry::query(), $actor, 'enquiries.view', 'department_id', null)->count();
    expect($visible)->toBe(0);
});

test('status transitions reject invalid jumps and repeat changes', function (): void {
    $this->seed(SecuritySeeder::class);
    $department = Department::factory()->create();
    $actor = scopedEnquiryUser($department);
    $enquiry = Enquiry::factory()->create(['department_id' => $department->id]);
    $manager = app(EnquiryManager::class);

    expect(fn () => $manager->setStatus($actor, $enquiry, 'resolved'))->toThrow(HttpException::class);
    $manager->setStatus($actor, $enquiry, 'in_progress');
    expect(fn () => $manager->setStatus($actor, $enquiry, 'in_progress'))->toThrow(HttpException::class);
    $manager->setStatus($actor, $enquiry, 'resolved');
    $manager->setStatus($actor, $enquiry, 'closed');
    expect($enquiry->fresh()->status)->toBe('closed');
});

test('routing and assignment respect source and destination scopes', function (): void {
    $this->seed(SecuritySeeder::class);
    $source = Department::factory()->create(['status' => 'active']);
    $destination = Department::factory()->create(['status' => 'active']);
    $actor = scopedEnquiryUser($source);
    $actor->roles->first()->givePermissionTo(['enquiries.route', 'enquiries.assign']);
    $assignee = scopedEnquiryUser($destination);
    $enquiry = Enquiry::factory()->create(['department_id' => $source->id]);
    $manager = app(EnquiryManager::class);

    expect(fn () => $manager->route($actor, $enquiry, $destination))->toThrow(HttpException::class);
    expect(fn () => $manager->assign($actor, $enquiry, $assignee))->toThrow(HttpException::class);
    $manager->assign($actor, $enquiry, $actor);
    expect($enquiry->fresh()->assigned_to)->toBe($actor->id);
    $manager->route($actor, $enquiry, $source);
    expect($enquiry->fresh()->assigned_to)->toBeNull();
    $this->assertDatabaseHas('audit_events', ['action' => 'enquiries.routed', 'subject_id' => (string) $enquiry->id]);
});

test('internal notes are scoped and excluded from public responses', function (): void {
    $this->seed(SecuritySeeder::class);
    $department = Department::factory()->create();
    $otherDepartment = Department::factory()->create();
    $actor = scopedEnquiryUser($department);
    $enquiry = Enquiry::factory()->create(['department_id' => $department->id]);
    $other = Enquiry::factory()->create(['department_id' => $otherDepartment->id]);
    $manager = app(EnquiryManager::class);
    $manager->addNote($actor, $enquiry, 'Internal follow-up only');
    expect($enquiry->notes()->sole()->body)->toBe('Internal follow-up only');
    expect(fn () => $manager->addNote($actor, $other, 'Should fail'))->toThrow(AuthorizationException::class);
    $this->get('/en/contact')->assertDontSee('Internal follow-up only');
});

test('global enquiry administrator can route and reassign across departments without leaking private notes', function (): void {
    $this->seed(SecuritySeeder::class);
    $source = Department::factory()->create(['status' => 'active']);
    $destination = Department::factory()->create(['status' => 'active']);
    $admin = User::factory()->create();
    $role = Role::findByName('System Administrator');
    $admin->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $admin->id, 'role_id' => $role->id, 'scope_type' => 'global',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $staff = scopedEnquiryUser($destination);
    $enquiry = Enquiry::factory()->create(['department_id' => $source->id]);
    $manager = app(EnquiryManager::class);

    $manager->route($admin, $enquiry, $destination);
    $manager->assign($admin, $enquiry, $staff);
    $manager->addNote($admin, $enquiry, 'Private resident information');

    expect($enquiry->fresh()->department_id)->toBe($destination->id)
        ->and($enquiry->fresh()->assigned_to)->toBe($staff->id);
    expect($staff->can('view', $enquiry->fresh()))->toBeTrue();
    $metadata = DB::table('audit_events')->where('action', 'enquiries.note_added')->value('metadata');
    expect($metadata)->not->toContain('Private resident information');
    $this->actingAs($staff)->get('/admin/enquiries/'.$enquiry->id)->assertOk();
});
