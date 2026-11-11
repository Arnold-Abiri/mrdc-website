<?php

use App\Filament\Resources\Enquiries\Pages\ViewEnquiry;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function viewRefreshAdmin(): User
{
    $role = Role::findOrCreate('View Refresh Admin', 'web');
    $role->givePermissionTo(['admin.access', 'enquiries.view', 'enquiries.update', 'enquiries.route']);
    $user = User::factory()->create(['status' => 'active']);
    $user->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'global',
        'department_id' => null, 'created_at' => now(), 'updated_at' => now(),
    ]);

    return $user;
}

test('route header action refreshes the viewed record', function (): void {
    $this->seed(SecuritySeeder::class);
    $actor = viewRefreshAdmin();
    $this->actingAs($actor);
    $department = Department::factory()->create(['status' => 'active']);
    $enquiry = Enquiry::factory()->create(['department_id' => null]);

    $page = Livewire::test(ViewEnquiry::class, ['record' => $enquiry->getRouteKey()])
        ->callAction('route', data: ['department_id' => $department->id])
        ->assertHasNoErrors();

    expect($enquiry->fresh()->department_id)->toBe($department->id);
    expect($page->get('record')->department_id)->toBe($department->id);
});

test('note header action persists and is visible on a fresh read', function (): void {
    $this->seed(SecuritySeeder::class);
    $actor = viewRefreshAdmin();
    $this->actingAs($actor);
    $enquiry = Enquiry::factory()->create(['department_id' => null]);

    Livewire::test(ViewEnquiry::class, ['record' => $enquiry->getRouteKey()])
        ->callAction('note', data: ['body' => 'Called the citizen back.'])
        ->assertHasNoErrors();

    expect($enquiry->fresh()->notes()->count())->toBe(1);
});
