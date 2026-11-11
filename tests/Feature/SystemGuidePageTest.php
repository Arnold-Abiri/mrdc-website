<?php

use App\Filament\Pages\SystemGuide;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('authenticated administrator can access system guide page and see workflows', function () {
    $this->seed(SecuritySeeder::class);
    $admin = User::factory()->create();
    $role = Role::findByName('System Administrator');
    $admin->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $admin->id,
        'role_id' => $role->id,
        'scope_type' => 'global',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(SystemGuide::getUrl());

    $response->assertSuccessful();
    $response->assertSeeText('System Walkthrough');
    $response->assertSeeText('Admin User Guide');
    $response->assertDontSeeText('Council Live Operational Pipeline');
    $response->assertDontSeeText('Standard Operating Procedures Manual');
    $response->assertSeeText('Record a service interruption');
    $response->assertSeeText('Send Update to Citizen');
    $response->assertSeeText('Tenders, Procurement & Vacancies');
    $response->assertSeeText('Citizen Services & Enquiries');
});

test('authenticated administrator can access admin dashboard with sidebar walkthrough launcher', function () {
    $this->seed(SecuritySeeder::class);
    $admin = User::factory()->create();
    $role = Role::findByName('System Administrator');
    $admin->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $admin->id,
        'role_id' => $role->id,
        'scope_type' => 'global',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertSuccessful();
    $response->assertSeeText('System Walkthrough');
    $response->assertSeeText('task guides');
    $response->assertSeeText('Admin User Guide');
    $response->assertSee('x-trap.inert.noscroll', false);
});

test('system guide workflow catalog contains valid core categories', function () {
    $catalog = SystemGuide::getWorkflowCatalog();

    expect($catalog)->toBeArray()->not->toBeEmpty();

    $categoryIds = array_column($catalog, 'id');
    expect($categoryIds)->toContain('enquiries', 'tenders', 'council', 'projects', 'content', 'admin');

    foreach ($catalog as $group) {
        expect($group)->toHaveKeys(['id', 'category', 'icon', 'color', 'description', 'tasks']);
        expect($group['tasks'])->toBeArray()->not->toBeEmpty();
        foreach ($group['tasks'] as $task) {
            expect($task)->toHaveKeys(['title', 'keywords', 'steps']);
            expect($task['steps'])->toBeArray()->not->toBeEmpty();
        }
    }
});

test('auditor sees guidance without unauthorised workflow shortcuts', function () {
    $this->seed(SecuritySeeder::class);
    $auditor = User::factory()->create();
    $role = Role::findByName('Auditor');
    $auditor->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $auditor->id,
        'role_id' => $role->id,
        'scope_type' => 'global',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $this->actingAs($auditor);

    $catalog = SystemGuide::getWorkflowCatalog();
    $tasks = collect($catalog)->flatMap(fn (array $group): array => $group['tasks']);

    expect($tasks->firstWhere('title', 'Give a staff member access')['destination'])->toBeNull();
    expect($tasks->firstWhere('title', 'Publish a tender')['destination'])->toBeNull();
    expect($tasks->firstWhere('title', 'Record a service interruption')['destination'])->toBeNull();

    $this->get(SystemGuide::getUrl())
        ->assertSuccessful()
        ->assertSeeText('Ask an authorised colleague for help with this task.')
        ->assertDontSeeText('Council Live Operational Pipeline');
});
