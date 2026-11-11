<?php

namespace Tests\Feature;

use App\Filament\Resources\Incidents\Pages\CreateIncident;
use App\Models\Incident;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IncidentCreateTest extends TestCase
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

    public function test_incident_create_page_saves_record(): void
    {
        $this->actingAs($this->administrator());

        Livewire::test(CreateIncident::class)
            ->fillForm([
                'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
                'source' => 'manual',
                'status' => 'open',
                'summary' => 'QA fiber cut on Main Street.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(1, Incident::query()->count());
        $this->assertSame('QA fiber cut on Main Street.', Incident::query()->sole()->summary);
    }

    public function test_incident_create_persists_shona_summary_translation(): void
    {
        $this->actingAs($this->administrator());

        Livewire::test(CreateIncident::class)
            ->fillForm([
                'started_at' => now()->subHour()->format('Y-m-d H:i:s'),
                'source' => 'manual',
                'status' => 'open',
                'summary' => 'Water supply interrupted in Ward 5.',
                'content_translations_sn_summary' => 'Mvura yavharwa muWard 5.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $incident = Incident::query()->sole();
        $this->assertSame('Mvura yavharwa muWard 5.', $incident->translated('summary', 'sn'));
    }

    public function test_incident_create_rejects_recovered_before_started(): void
    {
        $this->actingAs($this->administrator());

        Livewire::test(CreateIncident::class)
            ->fillForm([
                'started_at' => '2025-10-19 10:41:00',
                'recovered_at' => '2001-07-19 07:32:00',
                'source' => 'external_monitor',
                'status' => 'open',
                'summary' => 'Dolorem vel illo nobis magna recusandae.',
            ])
            ->call('create')
            ->assertHasFormErrors(['recovered_at']);

        $this->assertSame(0, Incident::query()->count());
    }
}
