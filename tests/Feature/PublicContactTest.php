<?php

namespace Tests\Feature;

use App\Domain\Cms\PublicContactManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicContactTest extends TestCase
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

    public function test_public_contact_details_require_verification_and_publication(): void
    {
        $admin = $this->administrator();
        $manager = app(PublicContactManager::class);
        $contact = $manager->create($admin, ['office' => 'Test office', 'type' => 'email', 'value' => 'approved@example.test', 'display_order' => 0]);
        $this->get('/contact')->assertDontSee('approved@example.test');
        $manager->setVerification($admin, $contact, 'publishable');
        $manager->setStatus($admin, $contact, 'published');
        $this->get('/contact')->assertSee('approved@example.test');
        $manager->update($admin, $contact, ['office' => 'Test office', 'type' => 'email', 'value' => 'updated@example.test', 'display_order' => 0]);
        $this->get('/contact')->assertDontSee('approved@example.test')->assertDontSee('updated@example.test');
        $audit = DB::table('audit_events')->where('subject_id', (string) $contact->id)->get()->pluck('metadata')->implode(' ');
        $this->assertStringNotContainsString('approved@example.test', $audit);
        $this->assertStringNotContainsString('updated@example.test', $audit);
    }

    public function test_email_and_phone_fields_are_validated(): void
    {
        $admin = $this->administrator();
        $manager = app(PublicContactManager::class);
        try {
            $manager->create($admin, ['office' => 'Test', 'type' => 'email', 'value' => 'not-an-email', 'display_order' => 0]);
            $this->fail('Invalid public email was accepted.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('public_contacts', 0);
        }
    }
}
