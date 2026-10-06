<?php

namespace Tests\Feature;

use App\Domain\Cms\CouncilMeetingManager;
use App\Domain\Cms\DocumentManager;
use App\Domain\Cms\EditorialManager;
use App\Domain\Cms\HomepageSlideManager;
use App\Domain\Cms\MediaManager;
use App\Models\CouncilMeeting;
use App\Models\Document;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Stage4bGovernanceTest extends TestCase
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

    private function pdf(User $admin, string $name): Media
    {
        return app(MediaManager::class)->upload($admin, UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"), ['title' => 'Development '.$name]);
    }

    private function publishedDocument(User $admin, array $overrides = []): Document
    {
        Storage::fake('local');
        $manager = app(DocumentManager::class);
        $document = $manager->create($admin, ['slug' => 'qa-budget-2026', 'title' => 'QA approved budget', 'category' => 'budget', 'media_id' => $this->pdf($admin, 'budget.pdf')->id, 'visibility' => 'public', 'reference_date' => '2026-03-31', ...$overrides]);
        $manager->setVerification($admin, $document, 'publishable');
        $manager->setStatus($admin, $document, 'published');

        return $document;
    }

    public function test_document_year_filtering_uses_reference_date(): void
    {
        $admin = $this->administrator();
        $this->publishedDocument($admin);
        $this->get('/documents?year=2026')->assertOk()->assertSee('QA approved budget');
        $this->get('/documents?year=2025')->assertOk()->assertDontSee('QA approved budget');
        $this->get('/documents?category=budget')->assertOk()->assertSee('QA approved budget');
        $this->get('/documents?category=policy')->assertOk()->assertDontSee('QA approved budget');
    }

    public function test_document_replacement_preserves_history_and_resets_to_draft(): void
    {
        $admin = $this->administrator();
        $document = $this->publishedDocument($admin);
        $replacement = $this->pdf($admin, 'budget-v2.pdf');
        app(DocumentManager::class)->replace($admin, $document, $replacement->id);
        $fresh = $document->fresh();
        $this->assertSame(2, $fresh->current_version);
        $this->assertSame('draft', $fresh->status);
        $this->assertCount(1, $fresh->versions);
        $this->assertSame(1, $fresh->versions->first()->version_number);
        $this->assertDatabaseHas('audit_events', ['action' => 'documents.replaced', 'subject_id' => (string) $document->id]);
        $this->get('/documents/qa-budget-2026')->assertNotFound();
        $this->get('/documents/qa-budget-2026/download')->assertNotFound();
    }

    public function test_public_download_is_tracked_and_private_documents_are_excluded(): void
    {
        $admin = $this->administrator();
        $document = $this->publishedDocument($admin);
        $this->get('/documents/qa-budget-2026/download')->assertOk();
        $this->assertSame(1, $document->fresh()->download_count);
        $this->assertDatabaseHas('document_downloads', ['document_id' => $document->id, 'version_number' => 1]);
        $this->get('/documents/qa-budget-2026')->assertSee('"download_count":1', false);
        $private = $this->publishedDocument($admin, ['slug' => 'qa-private', 'title' => 'QA private paper', 'visibility' => 'private']);
        $this->get('/documents/qa-private')->assertNotFound();
        $this->get('/documents/qa-private/download')->assertNotFound();
        $this->get('/search?q=private+paper')->assertDontSee('QA private paper');
        $this->assertSame($private->id, $private->id);
    }

    public function test_meeting_publication_with_private_agenda_and_minutes(): void
    {
        $admin = $this->administrator();
        $manager = app(CouncilMeetingManager::class);
        $meeting = $manager->create($admin, ['title' => 'QA full council', 'meeting_type' => 'full_council', 'scheduled_date' => today()->addWeek()->toDateString(), 'scheduled_time' => '10:00', 'venue' => 'Council chambers', 'meeting_status' => 'scheduled', 'display_order' => 0]);
        $this->get('/meetings')->assertDontSee('QA full council');
        $manager->setVerification($admin, $meeting, 'publishable');
        $manager->setStatus($admin, $meeting, 'published');
        $this->get('/meetings')->assertSee('QA full council');
        $this->get('/meetings/'.$meeting->id)->assertOk()->assertSee('Council chambers');
        $this->get('/search?q=full+council')->assertSee('QA full council');

        Storage::fake('local');
        $agenda = app(DocumentManager::class)->create($admin, ['slug' => 'qa-agenda', 'title' => 'QA private agenda', 'category' => 'agenda', 'media_id' => $this->pdf($admin, 'agenda.pdf')->id, 'visibility' => 'private']);
        $manager->update($admin, $meeting->fresh(), ['title' => 'QA full council', 'meeting_type' => 'full_council', 'scheduled_date' => today()->addWeek()->toDateString(), 'scheduled_time' => '10:00', 'venue' => 'Council chambers', 'meeting_status' => 'scheduled', 'agenda_document_id' => $agenda->id, 'display_order' => 0]);
        $manager->setVerification($admin, $meeting->fresh(), 'publishable');
        $manager->setStatus($admin, $meeting->fresh(), 'published');
        $this->get('/meetings/'.$meeting->id)->assertOk()->assertDontSee('QA private agenda');
    }

    public function test_financial_transparency_shows_only_finance_categories(): void
    {
        $admin = $this->administrator();
        $this->publishedDocument($admin);
        $this->get('/transparency')->assertOk()->assertSee('QA approved budget');
        $this->get('/transparency?category=budget')->assertSee('QA approved budget');
        $this->get('/transparency?category=financial_statement')->assertDontSee('QA approved budget');
        $this->get('/transparency?year=2026')->assertSee('QA approved budget');
        $this->get('/transparency?year=2024')->assertDontSee('QA approved budget');
    }

    public function test_homepage_slides_and_urgent_alert_lifecycle(): void
    {
        $admin = $this->administrator();
        $manager = app(HomepageSlideManager::class);
        $slide = $manager->create($admin, ['headline' => 'QA development milestone', 'supporting_text' => 'Testing only', 'cta_label' => 'Read more', 'cta_url' => '/news', 'display_order' => 0, 'is_active' => true]);
        $this->get('/')->assertDontSee('QA development milestone');
        $manager->setStatus($admin, $slide, 'published');
        $this->get('/')->assertSee('QA development milestone');

        $editorial = app(EditorialManager::class);
        $notice = $editorial->create($admin, ['type' => 'notice', 'slug' => 'qa-urgent', 'title' => 'QA urgent water notice', 'body' => 'Testing only', 'is_urgent' => true, 'display_order' => 0]);
        $this->get('/')->assertDontSee('QA urgent water notice');
        $editorial->setVerification($admin, $notice, 'publishable');
        $editorial->setStatus($admin, $notice, 'published');
        $this->get('/')->assertSee('QA urgent water notice');
        $notice->forceFill(['expires_at' => today()->subDay()])->save();
        $this->get('/')->assertDontSee('QA urgent water notice');
    }

    public function test_staff_without_meeting_permission_cannot_create_meetings(): void
    {
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $this->assertFalse($user->can('create', CouncilMeeting::class));
        $this->expectException(AuthorizationException::class);
        app(CouncilMeetingManager::class)->create($user, ['title' => 'Denied', 'meeting_type' => 'full_council', 'scheduled_date' => today()->toDateString(), 'meeting_status' => 'scheduled', 'display_order' => 0]);
    }
}
