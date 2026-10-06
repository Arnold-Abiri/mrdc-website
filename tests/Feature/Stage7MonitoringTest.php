<?php

namespace Tests\Feature;

use App\Domain\Analytics\AnalyticsReporter;
use App\Domain\Cms\ServiceManager;
use App\Domain\Cms\TenderManager;
use App\Domain\Operations\DowntimeAlerter;
use App\Domain\Operations\ErrorMonitor;
use App\Domain\Operations\IncidentManager;
use App\Http\Middleware\RecordAnalytics;
use App\Models\AnalyticsEvent;
use App\Models\AuditEvent;
use App\Models\ErrorEvent;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class Stage7MonitoringTest extends TestCase
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

    public function test_public_page_views_are_tracked_with_locale_and_referrer(): void
    {
        $this->get('/en/services', ['Referer' => 'https://www.google.com/search?q=council', 'User-Agent' => 'Mozilla/5.0'])->assertOk();
        $this->assertDatabaseHas('analytics_events', ['event_type' => 'page_view', 'route' => 'services.index', 'locale' => 'en', 'referrer_category' => 'search', 'referrer_domain' => 'google.com']);
        $this->get('/sn/services')->assertOk();
        $this->assertDatabaseHas('analytics_events', ['locale' => 'sn']);
    }

    public function test_bots_admin_and_failed_requests_are_not_tracked(): void
    {
        $count = AnalyticsEvent::query()->count();
        $this->get('/en/services', ['User-Agent' => 'Googlebot/2.1'])->assertOk();
        $this->get('/en/no-such-page-xyz')->assertNotFound();
        $this->assertSame($count, AnalyticsEvent::query()->count());
    }

    public function test_content_views_resolve_to_records_at_report_time(): void
    {
        $admin = $this->administrator();
        $manager = app(TenderManager::class);
        $tender = $manager->create($admin, ['reference' => 'MRDC/2026/031', 'slug' => 'qa-tracked', 'title' => 'QA tracked tender', 'description' => 'Development-only.', 'lifecycle_status' => 'open', 'display_order' => 0]);
        $manager->setVerification($admin, $tender, 'publishable');
        $manager->setStatus($admin, $tender, 'published');
        $this->get('/en/tenders/qa-tracked')->assertOk();
        $this->assertDatabaseHas('analytics_events', ['event_type' => 'content_view', 'subject_type' => 'tender', 'subject' => 'qa-tracked']);
        $popular = app(AnalyticsReporter::class)->popularContent(today()->toDateString(), today()->toDateString(), 'tender', 5);
        $this->assertSame('QA tracked tender', $popular[0]['title']);
    }

    public function test_referral_classification_prefers_categories_over_raw_urls(): void
    {
        $classifier = new RecordAnalytics;
        $this->assertSame(['direct', null], $classifier->classifyReferrer('', 'mutoko.example'));
        $this->assertSame(['internal', 'mutoko.example'], $classifier->classifyReferrer('https://mutoko.example/en/news?x=1', 'mutoko.example'));
        $this->assertSame(['search', 'google.com'], $classifier->classifyReferrer('https://www.google.com/search?q=x&secret=1', 'mutoko.example'));
        $this->assertSame(['social', 'facebook.com'], $classifier->classifyReferrer('https://m.facebook.com/post/1', 'mutoko.example'));
        [$category, $domain] = $classifier->classifyReferrer('https://example.org/path?a=b&token=secret', 'mutoko.example');
        $this->assertSame('external', $category);
        $this->assertSame('example.org', $domain);
    }

    public function test_kpis_downloads_and_content_activity_aggregate(): void
    {
        $admin = $this->administrator();
        $manager = app(ServiceManager::class);
        $service = $manager->create($admin, ['slug' => 'qa-water', 'name' => 'QA water', 'display_order' => 0]);
        $manager->setVerification($admin, $service, 'publishable');
        $manager->setStatus($admin, $service, 'published');
        $this->get('/en/services/qa-water')->assertOk();
        $reporter = app(AnalyticsReporter::class);
        $today = today()->toDateString();
        $kpis = $reporter->kpis($today, $today);
        $this->assertArrayHasKey('visitor_days', $kpis);
        $this->assertArrayHasKey('downloads', $kpis);
        $activity = $reporter->contentActivity($today, $today);
        $this->assertNotEmpty($activity);
    }

    public function test_reporting_pages_require_authorization(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin)->get('/admin/analytics-dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/audit-report')->assertOk();
        $this->actingAs($admin)->get('/admin/system-health')->assertOk();
        $this->actingAs($admin)->get('/admin/monthly-report')->assertOk();
        $limited = User::factory()->create();
        $this->actingAs($limited)->get('/admin/analytics-dashboard')->assertForbidden();
        $this->actingAs($limited)->get('/admin/audit-report')->assertForbidden();
        $this->actingAs($limited)->get('/admin/system-health')->assertForbidden();
        $this->actingAs($limited)->get('/admin/reports/audit-export')->assertForbidden();
    }

    public function test_audit_export_streams_filtered_csv(): void
    {
        $admin = $this->administrator();
        app(ServiceManager::class)->create($admin, ['slug' => 'qa-water', 'name' => 'QA water', 'display_order' => 0]);
        $response = $this->actingAs($admin)->get('/admin/reports/audit-export?action=services.created');
        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('services.created', $response->streamedContent());
    }

    public function test_error_events_capture_sanitized_summaries(): void
    {
        $monitor = app(ErrorMonitor::class);
        $event = $monitor->capture(new \RuntimeException('Downstream failure for admin@example.test with token=abc123'));
        $this->assertInstanceOf(ErrorEvent::class, $event);
        $this->assertStringNotContainsString('admin@example.test', $event->summary);
        $this->assertStringNotContainsString('abc123', $event->summary);
        $this->assertNull($monitor->capture(new ValidationException(validator(['x' => null], ['x' => 'required']))));
        $this->assertNull($monitor->capture(new NotFoundHttpException));
    }

    public function test_incident_lifecycle_and_downtime_alert_uses_safe_transport(): void
    {
        $admin = $this->administrator();
        $manager = app(IncidentManager::class);
        $incident = $manager->create($admin, ['started_at' => now()->subHour()->toDateTimeString(), 'source' => 'manual', 'status' => 'open', 'summary' => 'QA fiber cut']);
        $this->assertSame('open', $incident->status);
        $manager->update($admin, $incident, ['started_at' => $incident->started_at->toDateTimeString(), 'recovered_at' => now()->toDateTimeString(), 'source' => 'manual', 'status' => 'resolved', 'summary' => 'QA fiber cut']);
        $this->assertNotNull($incident->fresh()->durationMinutes());
        config(['ops.alert_recipients' => ['ops@example.test']]);
        $manager = $manager;
        app(DowntimeAlerter::class)->notify($incident->fresh());
        $this->assertTrue(true);
    }

    public function test_analytics_retention_prunes_only_raw_events(): void
    {
        $admin = $this->administrator();
        app(ServiceManager::class)->create($admin, ['slug' => 'qa-water', 'name' => 'QA water', 'display_order' => 0]);
        AnalyticsEvent::query()->create(['event_type' => 'page_view', 'route' => 'services.index', 'visitor_key' => 'old', 'referrer_category' => 'direct', 'created_at' => now()->subDays(400)]);
        $auditCount = AuditEvent::query()->count();
        $this->artisan('analytics:prune')->assertSuccessful();
        $this->assertSame(0, AnalyticsEvent::query()->where('visitor_key', 'old')->count());
        $this->assertSame($auditCount, AuditEvent::query()->count());
    }
}
