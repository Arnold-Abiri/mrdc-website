<?php

namespace Tests\Feature\Mail;

use App\Filament\Pages\EmailOperations;
use App\Models\AuditEvent;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailOperationsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SecuritySeeder::class);
    }

    public function test_system_administrator_can_access_email_operations(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->get('/admin/reports/email-operations')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_access_email_operations(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/reports/email-operations')
            ->assertForbidden();
    }

    public function test_admin_can_retry_failed_job_and_action_is_audited(): void
    {
        $admin = $this->createAdminUser();

        $uuid = (string) Str::uuid();
        DB::table('failed_jobs')->insert([
            'uuid' => $uuid,
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Mail\\EnquiryStatusUpdated']),
            'exception' => "Connection timed out\nStack trace...",
            'failed_at' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(EmailOperations::class)
            ->call('retryJob', $uuid)
            ->assertHasNoErrors();

        $audit = AuditEvent::query()->where('action', 'queue.job_retried')->first();
        $this->assertNotNull($audit);
        $this->assertSame($uuid, $audit->metadata['job_uuid']);
    }

    public function test_admin_can_forget_failed_job(): void
    {
        $admin = $this->createAdminUser();

        $uuid = (string) Str::uuid();
        DB::table('failed_jobs')->insert([
            'uuid' => $uuid,
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Mail\\EnquiryStatusUpdated']),
            'exception' => "Connection timed out\nStack trace...",
            'failed_at' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(EmailOperations::class)
            ->call('forgetJob', $uuid)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('failed_jobs', ['uuid' => $uuid]);

        $audit = AuditEvent::query()->where('action', 'queue.job_deleted')->first();
        $this->assertNotNull($audit);
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
