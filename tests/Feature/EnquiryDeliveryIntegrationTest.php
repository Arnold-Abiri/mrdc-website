<?php

namespace Tests\Feature;

use App\Domain\Cms\EnquiryManager;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;
use Tests\TestCase;

class EnquiryDeliveryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function connectionsToTransact(): array
    {
        return [];
    }

    public function test_database_queue_worker_delivers_to_the_resolved_staff_recipient(): void
    {
        Artisan::call('migrate:fresh', ['--force' => true]);
        config(['queue.default' => 'database', 'mail.default' => 'array']);
        $this->seed(SecuritySeeder::class);
        $admin = User::factory()->create();
        $adminRole = Role::findByName('System Administrator');
        $admin->assignRole($adminRole);
        DB::table('user_role_scopes')->insert(['user_id' => $admin->id, 'role_id' => $adminRole->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);
        $department = Department::factory()->create(['status' => 'active']);
        $staffRole = Role::findOrCreate('Delivery test handler', 'web');
        $staffRole->givePermissionTo('enquiries.view');
        $staff = User::factory()->create(['department_id' => $department->id]);
        $staff->assignRole($staffRole);
        DB::table('user_role_scopes')->insert(['user_id' => $staff->id, 'role_id' => $staffRole->id, 'scope_type' => 'department', 'department_id' => $department->id, 'created_at' => now(), 'updated_at' => now()]);
        $enquiry = Enquiry::factory()->create(['department_id' => $department->id]);

        app(EnquiryManager::class)->setStatus($admin, $enquiry, 'in_progress');
        $this->assertDatabaseCount('jobs', 1);
        Artisan::call('queue:work', ['connection' => 'database', '--once' => true, '--queue' => 'default', '--tries' => 1]);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 0);
        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $this->assertSame($staff->email, $messages->first()->getEnvelope()->getRecipients()[0]->getAddress());

        Mail::mailer()->setSymfonyTransport(new class implements TransportInterface
        {
            public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
            {
                throw new \RuntimeException('Isolated delivery failure');
            }

            public function __toString(): string
            {
                return 'isolated-failure';
            }
        });
        app(EnquiryManager::class)->setStatus($admin, $enquiry->fresh(), 'resolved');
        $this->assertDatabaseCount('jobs', 1);
        Artisan::call('queue:work', ['connection' => 'database', '--once' => true, '--queue' => 'default', '--tries' => 2]);
        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseCount('failed_jobs', 0);
        Artisan::call('queue:work', ['connection' => 'database', '--once' => true, '--queue' => 'default', '--tries' => 2]);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 1);
    }
}
