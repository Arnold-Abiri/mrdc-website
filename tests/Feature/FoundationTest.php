<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\SecuritySeeder;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_an_inertia_page(): void
    {
        $this->get('/en')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Home', false));
    }

    public function test_health_endpoint_responds(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_sitemap_contains_only_published_public_route(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->assertSee(route('home', ['locale' => 'en'], false));
    }

    public function test_sitemap_lists_localized_homepage_for_every_locale(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        foreach (['en', 'sn', 'nd'] as $locale) {
            $response->assertSee(route('home', ['locale' => $locale], false));
        }
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_explicit_admin_grant_allows_panel_access(): void
    {
        $user = User::factory()->create();
        $this->seed(SecuritySeeder::class);
        $user->assignRole('System Administrator');
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => Role::findByName('System Administrator')->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_coming_soon_route_uses_only_approved_topic_names(): void
    {
        $this->get('/en/coming-soon?topic=tenders')->assertOk()->assertInertia(fn (Assert $page) => $page->component('ComingSoon', false)->where('topic', 'Tenders'));
        $this->get('/en/coming-soon?topic=unapproved')->assertOk()->assertInertia(fn (Assert $page) => $page->where('topic', 'This section'));
    }

    public function test_default_seeder_creates_no_users(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_failed_filament_logins_are_rate_limited(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);
        $this->seed(SecuritySeeder::class);
        $user->assignRole('System Administrator');
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => Role::findByName('System Administrator')->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);
        $key = 'livewire-rate-limiter:'.sha1(Login::class.'|authenticate|127.0.0.1');
        RateLimiter::clear($key);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            Livewire::test(Login::class)
                ->set('data.email', $user->email)
                ->set('data.password', 'wrong-password')
                ->call('authenticate');
        }

        $this->assertTrue(RateLimiter::tooManyAttempts($key, 5));
        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'correct-password')
            ->call('authenticate');
        $this->assertGuest();
        RateLimiter::clear($key);
    }

    public function test_successful_filament_login_regenerates_session(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);
        $this->seed(SecuritySeeder::class);
        $user->assignRole('System Administrator');
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => Role::findByName('System Administrator')->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);
        $this->get('/admin/login');
        $previousSessionId = session()->getId();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'correct-password')
            ->call('authenticate');

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($previousSessionId, session()->getId());
    }

    public function test_logout_removes_admin_access_and_invalidates_session(): void
    {
        $user = User::factory()->create();
        $this->seed(SecuritySeeder::class);
        $user->assignRole('System Administrator');
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => Role::findByName('System Administrator')->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($user)->get('/admin')->assertOk();
        $previousSessionId = session()->getId();

        $this->post('/admin/logout')->assertRedirect();
        $this->assertGuest();
        $this->assertNotSame($previousSessionId, session()->getId());
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_production_errors_hide_stack_traces(): void
    {
        config()->set('app.debug', false);
        Route::get('/stage-one-error-probe', function (): never {
            throw new \RuntimeException('SENSITIVE_TEST_STACK_MARKER');
        });
        $this->get('/stage-one-error-probe')->assertStatus(500)->assertDontSee('SENSITIVE_TEST_STACK_MARKER');
    }

    public function test_public_response_has_security_headers(): void
    {
        $this->get('/en')->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }
}
