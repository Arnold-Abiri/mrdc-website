<?php

namespace Tests\Feature;

use App\Domain\Cms\DistrictStatisticManager;
use App\Domain\Cms\InvestmentManager;
use App\Domain\Cms\TenderManager;
use App\Domain\Cms\VacancyManager;
use App\Models\DistrictStatistic;
use App\Models\Tender;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Stage4CitizenServicesTest extends TestCase
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

    private function publishTender(User $admin, array $overrides = []): Tender
    {
        $manager = app(TenderManager::class);
        $tender = $manager->create($admin, ['reference' => 'MRDC/2026/014', 'slug' => 'qa-road-works', 'title' => 'QA road works', 'description' => 'Development-only tender', 'lifecycle_status' => 'open', 'display_order' => 0, ...$overrides]);
        $manager->setVerification($admin, $tender, 'publishable');
        $manager->setStatus($admin, $tender, 'published');

        return $tender;
    }

    public function test_draft_tender_stays_private_and_published_tender_is_visible_with_status(): void
    {
        $admin = $this->administrator();
        $manager = app(TenderManager::class);
        $tender = $manager->create($admin, ['reference' => 'MRDC/2026/014', 'slug' => 'qa-road-works', 'title' => 'QA road works', 'description' => 'Development-only tender', 'lifecycle_status' => 'open', 'display_order' => 0]);
        $this->get('/en/tenders')->assertDontSee('QA road works');
        $this->get('/en/tenders/qa-road-works')->assertNotFound();
        $this->get('/en/search?q=road+works')->assertDontSee('QA road works');
        $manager->setVerification($admin, $tender, 'publishable');
        $manager->setStatus($admin, $tender, 'published');
        $this->get('/en/tenders')->assertSee('QA road works');
        $this->get('/en/tenders/qa-road-works')->assertOk()->assertSee('MRDC\\/2026\\/014', false)->assertSee('"display_status":"open"', false);
        $this->get('/en/search?q=road+works')->assertSee('QA road works');
        $this->get('/en')->assertSee('QA road works');
    }

    public function test_closed_tender_is_marked_closed_and_past_deadline_reads_closed(): void
    {
        $admin = $this->administrator();
        $tender = $this->publishTender($admin, ['closes_at' => now()->subDay()->toDateTimeString()]);
        $this->assertSame('closed', $tender->fresh()->displayStatus());
        $this->get('/en/tenders/qa-road-works')->assertOk()->assertSee('closed');
        $this->assertFalse($tender->fresh()->isOpen());
    }

    public function test_cancelled_tender_is_never_open(): void
    {
        $admin = $this->administrator();
        $manager = app(TenderManager::class);
        $tender = $this->publishTender($admin);
        $manager->setLifecycleStatus($admin, $tender, 'cancelled');
        $this->assertSame('cancelled', $tender->fresh()->displayStatus());
        $this->get('/en/tenders/qa-road-works')->assertOk()->assertSee('"display_status":"cancelled"', false);
    }

    public function test_expired_vacancy_reads_closed_and_draft_is_private(): void
    {
        $admin = $this->administrator();
        $manager = app(VacancyManager::class);
        $vacancy = $manager->create($admin, ['slug' => 'qa-driver', 'title' => 'QA driver', 'description' => 'Development-only vacancy', 'closes_at' => today()->subDay()->toDateString(), 'display_order' => 0]);
        $this->get('/en/vacancies/qa-driver')->assertNotFound();
        $manager->setVerification($admin, $vacancy, 'publishable');
        $manager->setStatus($admin, $vacancy, 'published');
        $this->assertFalse($vacancy->fresh()->isOpen());
        $this->get('/en/vacancies')->assertSee('QA driver')->assertSee('"is_open":false', false);
        $this->get('/en/vacancies/qa-driver')->assertOk()->assertSee('"is_open":false', false);
        $this->get('/en/search?q=driver')->assertSee('QA driver');
    }

    public function test_investment_opportunity_lifecycle_and_search(): void
    {
        $admin = $this->administrator();
        $manager = app(InvestmentManager::class);
        $opportunity = $manager->create($admin, ['slug' => 'qa-solar', 'title' => 'QA solar park', 'sector' => 'Energy', 'summary' => 'Development-only summary', 'description' => 'Development-only description without return claims.', 'opportunity_status' => 'open', 'display_order' => 0]);
        $this->get('/en/investment')->assertDontSee('QA solar park');
        $this->get('/en/investment/qa-solar')->assertNotFound();
        $manager->setVerification($admin, $opportunity, 'publishable');
        $manager->setStatus($admin, $opportunity, 'published');
        $this->get('/en/investment')->assertSee('QA solar park');
        $this->get('/en/investment/qa-solar')->assertOk()->assertSee('Energy');
        $this->get('/en/search?q=solar')->assertSee('QA solar park');
        $this->get('/en')->assertSee('QA solar park');
    }

    public function test_district_statistics_appear_on_homepage_only_when_active(): void
    {
        $admin = $this->administrator();
        $statistic = DistrictStatistic::query()->create(['label' => 'QA wards', 'value' => '29', 'display_order' => 0, 'is_active' => false]);
        $this->get('/en')->assertDontSee('QA wards');
        app(DistrictStatisticManager::class)->update($admin, $statistic, ['label' => 'QA wards', 'value' => '29', 'display_order' => 0, 'is_active' => true]);
        $this->get('/en')->assertSee('QA wards')->assertSee('29');
    }

    public function test_about_and_downloads_aliases(): void
    {
        $this->get('/en/about')->assertRedirect('/en/pages/about-mutoko');
        $this->get('/en/downloads')->assertRedirect('/en/documents');
    }

    public function test_staff_without_tender_permission_cannot_create_tenders(): void
    {
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $this->assertFalse($user->can('create', Tender::class));
        $this->expectException(AuthorizationException::class);
        app(TenderManager::class)->create($user, ['reference' => 'MRDC/2026/099', 'slug' => 'qa-denied', 'title' => 'Denied', 'description' => 'Denied', 'lifecycle_status' => 'open', 'display_order' => 0]);
    }
}
