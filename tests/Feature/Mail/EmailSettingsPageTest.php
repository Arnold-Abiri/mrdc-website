<?php

namespace Tests\Feature\Mail;

use App\Filament\Pages\EmailSettings;
use App\Models\AuditEvent;
use App\Models\MailConfiguration;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SecuritySeeder::class);
    }

    public function test_system_administrator_can_access_email_settings_page(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->get('/admin/administration/email-settings')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_access_email_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/administration/email-settings')
            ->assertForbidden();
    }

    public function test_admin_can_save_draft_configuration_and_audit_log_is_recorded(): void
    {
        $admin = $this->createAdminUser();

        Livewire::actingAs($admin)
            ->test(EmailSettings::class)
            ->fillForm([
                'name' => 'New Council SMTP',
                'provider' => 'custom',
                'host' => 'smtp.mutokordc.org.zw',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'ict@mutokordc.org.zw',
                'password' => 'MySecretPassword123',
                'from_name' => 'Mutoko Council',
                'from_address' => 'noreply@mutokordc.org.zw',
            ])
            ->call('saveDraft')
            ->assertHasNoErrors();

        $config = MailConfiguration::query()->where('name', 'New Council SMTP')->first();
        $this->assertNotNull($config);
        $this->assertSame('smtp.mutokordc.org.zw', $config->host);
        $this->assertFalse((bool) $config->is_active);

        // Ensure audit event was logged without the password
        $audit = AuditEvent::query()->where('action', 'mail.settings_saved')->first();
        $this->assertNotNull($audit);
        $this->assertArrayNotHasKey('password', $audit->metadata);
        $this->assertSame('New Council SMTP', $audit->metadata['name']);
    }

    public function test_admin_can_activate_configuration_and_clear_other_active(): void
    {
        $admin = $this->createAdminUser();

        $c1 = MailConfiguration::create([
            'name' => 'Old Config',
            'host' => 'old.example.com',
            'port' => 587,
            'from_name' => 'Mutoko',
            'from_address' => 'old@example.com',
            'is_active' => true,
        ]);

        $c2 = MailConfiguration::create([
            'name' => 'New Config',
            'host' => 'new.example.com',
            'port' => 587,
            'from_name' => 'Mutoko',
            'from_address' => 'new@example.com',
            'is_active' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(EmailSettings::class)
            ->set('formData.id', $c2->id)
            ->call('activateConfiguration')
            ->assertHasNoErrors();

        $this->assertFalse((bool) $c1->fresh()->is_active);
        $this->assertTrue((bool) $c2->fresh()->is_active);
    }

    private function createAdminUser(): User
    {
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

        return $admin;
    }
}
