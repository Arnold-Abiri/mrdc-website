<?php

namespace Tests\Feature;

use App\Domain\Cms\CouncilProjectManager;
use App\Domain\Cms\EnquiryManager;
use App\Domain\Cms\InvestmentManager;
use App\Domain\Cms\ServiceManager;
use App\Domain\Cms\TenderManager;
use App\Domain\Cms\VacancyManager;
use App\Models\Enquiry;
use App\Models\Tender;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class Stage5ParticipationTest extends TestCase
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

    private function publishedTender(User $admin): Tender
    {
        $manager = app(TenderManager::class);
        $tender = $manager->create($admin, ['reference' => 'MRDC/2026/020', 'slug' => 'qa-award-works', 'title' => 'QA award works', 'description' => 'Development-only tender', 'lifecycle_status' => 'open', 'display_order' => 0]);
        $manager->setVerification($admin, $tender, 'publishable');
        $manager->setStatus($admin, $tender, 'published');

        return $tender;
    }

    public function test_tender_award_lifecycle_and_public_display(): void
    {
        $admin = $this->administrator();
        $manager = app(TenderManager::class);
        $tender = $this->publishedTender($admin);
        $this->get('/en/tenders/qa-award-works')->assertOk()->assertDontSee('"award_status":"awarded"', false);
        $manager->recordAward($admin, $tender, ['award_status' => 'awarded', 'awarded_to' => 'QA Builders', 'awarded_at' => today()->toDateString(), 'award_reference' => 'MRDC/A/2026/03']);
        $fresh = $tender->fresh();
        $this->assertSame('awarded', $fresh->award_status);
        $this->assertSame('awarded', $fresh->lifecycle_status);
        $this->assertFalse($fresh->isOpen());
        $this->get('/en/tenders/qa-award-works')->assertOk()->assertSee('"award_status":"awarded"', false)->assertSee('QA Builders');
        $this->assertDatabaseHas('audit_events', ['action' => 'tender.award_recorded', 'subject_id' => (string) $tender->id]);
        $summary = $manager->complianceSummary();
        $this->assertSame(1, $summary['total']);
        $this->assertSame(1, $summary['awarded']);
    }

    public function test_award_requires_contractor_and_date_and_award_permission(): void
    {
        $admin = $this->administrator();
        $tender = $this->publishedTender($admin);
        try {
            app(TenderManager::class)->recordAward($admin, $tender, ['award_status' => 'awarded']);
            $this->fail('Expected award validation to fail.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
        $limited = User::factory()->create();
        $this->assertFalse($limited->can('award', $tender));
    }

    public function test_vacancy_reference_employment_type_and_expiry(): void
    {
        $admin = $this->administrator();
        $manager = app(VacancyManager::class);
        $vacancy = $manager->create($admin, ['slug' => 'qa-nurse', 'title' => 'QA nurse', 'reference' => 'MRDC/HR/2026/03', 'employment_type' => 'full_time', 'description' => 'Development-only vacancy', 'closes_at' => today()->subDay()->toDateString(), 'display_order' => 0]);
        $manager->setVerification($admin, $vacancy, 'publishable');
        $manager->setStatus($admin, $vacancy, 'published');
        $this->assertFalse($vacancy->fresh()->isOpen());
        $this->get('/en/vacancies/qa-nurse')->assertOk()->assertSee('MRDC\\/HR\\/2026\\/03', false);
    }

    public function test_project_publication_filtering_and_detail(): void
    {
        $admin = $this->administrator();
        $manager = app(CouncilProjectManager::class);
        $project = $manager->create($admin, ['title' => 'QA clinic block', 'slug' => 'qa-clinic-block', 'project_type' => 'project', 'location' => 'QA ward centre', 'summary' => 'Development-only summary', 'description' => 'Development-only description.', 'project_status' => 'ongoing', 'progress_percent' => 40, 'display_order' => 0]);
        $this->get('/en/projects')->assertDontSee('QA clinic block');
        $this->get('/en/projects/qa-clinic-block')->assertNotFound();
        $manager->setVerification($admin, $project, 'publishable');
        $manager->setStatus($admin, $project, 'published');
        $this->get('/en/projects')->assertSee('QA clinic block');
        $this->get('/en/projects?status=ongoing')->assertSee('QA clinic block');
        $this->get('/en/projects?status=completed')->assertDontSee('QA clinic block');
        $this->get('/en/projects/qa-clinic-block')->assertOk()->assertSee('40');
        $this->get('/en/search?q=clinic')->assertSee('QA clinic block');
        $this->get('/en')->assertSee('QA clinic block');
    }

    public function test_investor_enquiry_keeps_safe_context_and_routes_privately(): void
    {
        $admin = $this->administrator();
        $investment = app(InvestmentManager::class);
        $opportunity = $investment->create($admin, ['slug' => 'qa-invest', 'title' => 'QA invest park', 'description' => 'Development-only.', 'opportunity_status' => 'open', 'display_order' => 0]);
        $investment->setVerification($admin, $opportunity, 'publishable');
        $investment->setStatus($admin, $opportunity, 'published');
        $enquiry = app(EnquiryManager::class)->submit(['name' => 'QA Investor', 'email' => 'investor@example.test', 'category' => 'investment_enquiry', 'context_type' => 'investment', 'context_reference' => 'qa-invest', 'organisation' => 'QA Holdings', 'subject' => 'QA interest', 'message' => 'Development-only enquiry message.']);
        $this->assertSame('investment', $enquiry->context_type);
        $this->assertSame('qa-invest', $enquiry->context_reference);
        $this->assertSame('QA Holdings', $enquiry->organisation);
        $forged = app(EnquiryManager::class)->submit(['name' => 'QA Forged', 'email' => 'forged@example.test', 'category' => 'investment_enquiry', 'context_type' => 'investment', 'context_reference' => 'no-such-opportunity', 'subject' => 'QA forged', 'message' => 'Development-only forged message.']);
        $this->assertNull($forged->context_type);
        $this->assertNull($forged->context_reference);
        $this->get('/en/search?q=investor')->assertDontSee('QA Investor');
        $this->get('/sitemap.xml')->assertDontSee('QA Investor');
    }

    public function test_complaint_submission_returns_reference_and_stays_private(): void
    {
        $response = $this->post('/en/feedback', ['name' => 'QA Citizen', 'email' => 'citizen@example.test', 'category' => 'complaint', 'subject' => 'QA refuse complaint', 'message' => 'Development-only complaint message.', 'consent_given' => '1', '_token' => csrf_token()]);
        $response->assertRedirect();
        $enquiry = Enquiry::query()->where('subject', 'QA refuse complaint')->firstOrFail();
        $this->assertSame('complaint', $enquiry->category);
        $this->assertTrue($enquiry->consent_given);
        $this->assertStringContainsString(substr($enquiry->public_id, 0, 8), (string) $response->headers->get('Location'));
        $this->get('/en/search?q=refuse')->assertDontSee('QA refuse complaint');
    }

    public function test_service_enquiry_context_and_related_documents(): void
    {
        $admin = $this->administrator();
        $serviceManager = app(ServiceManager::class);
        $service = $serviceManager->create($admin, ['slug' => 'qa-water', 'name' => 'QA water', 'summary' => 'Testing only', 'display_order' => 0]);
        $serviceManager->setVerification($admin, $service, 'publishable');
        $serviceManager->setStatus($admin, $service, 'published');
        $enquiry = app(EnquiryManager::class)->submit(['name' => 'QA Resident', 'email' => 'resident@example.test', 'category' => 'service_enquiry', 'context_type' => 'service', 'context_reference' => 'qa-water', 'subject' => 'QA question', 'message' => 'Development-only service question.']);
        $this->assertSame('service', $enquiry->context_type);
        $this->assertSame('qa-water', $enquiry->context_reference);
        $this->get('/en/contact?context=service:qa-water')->assertOk();
        $this->get('/en/services/qa-water')->assertOk();
    }
}
