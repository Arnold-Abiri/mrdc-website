<?php

namespace Tests\Feature;

use App\Domain\Cms\EnquiryManager;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use App\Notifications\EnquiryWorkflowNotification;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnquiryNotificationTest extends TestCase
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
        $role = Role::findOrCreate('Notification Enquiry Handler', 'web');
        $role->givePermissionTo(['enquiries.view', 'enquiries.assign', 'enquiries.update']);
        $user = User::factory()->create(['department_id' => $department->id]);
        $user->assignRole($role);
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'department', 'department_id' => $department->id, 'created_at' => now(), 'updated_at' => now()]);

        return $user;
    }

    public function test_routing_assignment_and_status_send_queued_staff_notifications_without_resident_data(): void
    {
        Notification::fake();
        $admin = $this->administrator();
        $department = Department::factory()->create(['status' => 'active']);
        $staff = $this->scopedStaff($department);
        $enquiry = Enquiry::factory()->create(['name' => 'Private Resident', 'email' => 'resident@example.test', 'message' => 'This private message is not notification content.']);
        $manager = app(EnquiryManager::class);
        $manager->route($admin, $enquiry, $department);
        Notification::assertSentTo($staff, EnquiryWorkflowNotification::class);
        $manager->assign($admin, $enquiry, $staff);
        Notification::assertSentTo($staff, EnquiryWorkflowNotification::class);
        $manager->setStatus($admin, $enquiry, 'in_progress');
        Notification::assertSentTo($staff, EnquiryWorkflowNotification::class);
        Notification::assertSentTo($staff, EnquiryWorkflowNotification::class, function (EnquiryWorkflowNotification $notification) use ($enquiry): bool {
            $mail = $notification->toMail($staff = new User);
            $body = implode(' ', $mail->introLines);

            return str_contains($body, $enquiry->public_id)
                && ! str_contains($body, 'resident@example.test')
                && ! str_contains($body, 'This private message');
        });
    }

    public function test_notification_dispatch_failure_does_not_rollback_enquiry_state(): void
    {
        $admin = $this->administrator();
        $department = Department::factory()->create(['status' => 'active']);
        $this->scopedStaff($department);
        $enquiry = Enquiry::factory()->create(['department_id' => $department->id]);
        Notification::shouldReceive('send')->once()->andThrow(new \RuntimeException('Queue unavailable.'));
        $updated = app(EnquiryManager::class)->setStatus($admin, $enquiry, 'in_progress');
        $this->assertSame('in_progress', $updated->fresh()->status);
    }
}
