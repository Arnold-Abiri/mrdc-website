<?php

namespace Tests\Feature;

use App\Domain\Cms\ServiceManager;
use App\Domain\Content\TranslationManager;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class Stage6LocalizationTest extends TestCase
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

    private function publishedService(User $admin): Service
    {
        $manager = app(ServiceManager::class);
        $service = $manager->create($admin, ['slug' => 'qa-water', 'name' => 'QA water', 'summary' => 'Testing only', 'display_order' => 0]);
        $manager->setVerification($admin, $service, 'publishable');
        $manager->setStatus($admin, $service, 'published');

        return $service;
    }

    public function test_translated_content_renders_with_english_fallback(): void
    {
        $admin = $this->administrator();
        $service = $this->publishedService($admin);
        app()->setLocale('sn');
        $this->assertSame('QA water', $service->translated('name'));
        app(TranslationManager::class)->saveTranslations($admin, $service, ['sn' => ['name' => 'QA mvura', 'summary' => null]]);
        $this->assertSame('QA mvura', $service->fresh()->translated('name', 'sn'));
        $this->assertSame('Testing only', $service->fresh()->translated('summary', 'sn'));
        $this->assertSame('QA water', $service->fresh()->translated('name', 'nd'));
        $this->get('/sn/services/qa-water')->assertOk();
        $this->get('/nd/services/qa-water')->assertOk();
    }

    public function test_translation_completeness_tracks_required_fields(): void
    {
        $admin = $this->administrator();
        $service = $this->publishedService($admin);
        $status = $service->translationCompleteness();
        $this->assertSame('missing', $status['sn']['status']);
        app(TranslationManager::class)->saveTranslations($admin, $service, ['sn' => ['name' => 'QA mvura']]);
        $status = $service->fresh()->translationCompleteness();
        $this->assertSame('partial', $status['sn']['status']);
        $this->assertSame(1, $status['sn']['translated']);
        $this->assertSame(count(Service::translatableFields()), $status['sn']['required']);
    }

    public function test_translations_reject_unknown_locales_fields_and_unauthorized_users(): void
    {
        $admin = $this->administrator();
        $service = $this->publishedService($admin);
        try {
            app(TranslationManager::class)->saveTranslations($admin, $service, ['fr' => ['name' => 'x']]);
            $this->fail('Expected unsupported locale to be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
        try {
            app(TranslationManager::class)->saveTranslations($admin, $service, ['sn' => ['nope' => 'x']]);
            $this->fail('Expected unknown field to be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
        $this->expectException(AuthorizationException::class);
        app(TranslationManager::class)->saveTranslations(User::factory()->create(), $service, ['sn' => ['name' => 'x']]);
    }

    public function test_localized_pages_expose_canonical_and_hreflang(): void
    {
        $this->get('/en/services')->assertOk()->assertSee('<link rel="canonical" href="http://localhost:8000/en/services"', false);
        $response = $this->get('/sn/services');
        $response->assertOk()->assertSee('<link rel="alternate" hreflang="sn" href="http://localhost:8000/sn/services"', false);
        $response->assertSee('<link rel="alternate" hreflang="nd" href="http://localhost:8000/nd/services"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_sitemap_lists_prefixed_public_urls_without_private_records(): void
    {
        $admin = $this->administrator();
        $this->publishedService($admin);
        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertSee('http://localhost:8000/en/services/qa-water', false);
        $response->assertSee('http://localhost:8000/sn/services/qa-water', false);
        $response->assertSee('http://localhost:8000/nd/services/qa-water', false);
    }

    public function test_search_never_exposes_drafts_in_any_locale(): void
    {
        $admin = $this->administrator();
        $manager = app(ServiceManager::class);
        $manager->create($admin, ['slug' => 'qa-draft', 'name' => 'QA hidden draft', 'display_order' => 0]);
        $this->get('/sn/search?q=hidden')->assertDontSee('QA hidden draft');
        $this->get('/nd/search?q=hidden')->assertDontSee('QA hidden draft');
    }
}
