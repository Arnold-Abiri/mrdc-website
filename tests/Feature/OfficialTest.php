<?php

namespace Tests\Feature;

use App\Domain\Cms\MediaManager;
use App\Domain\Cms\OfficialManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OfficialTest extends TestCase
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

    public function test_official_photo_is_not_public_until_its_record_is_published(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/pWQAAAAASUVORK5CYII=', true);
        $media = app(MediaManager::class)->upload($admin, UploadedFile::fake()->createWithContent('official.png', $png ?: ''), ['title' => 'Development fixture portrait', 'alt_text' => 'Test fixture']);
        $manager = app(OfficialManager::class);
        $official = $manager->create($admin, ['name' => 'Test official', 'title' => 'Test title', 'slug' => 'test-official-photo', 'photo_media_id' => $media->id, 'display_order' => 0]);
        $this->get('/managed-media/'.$media->id)->assertNotFound();
        $manager->setVerification($admin, $official, 'publishable');
        $manager->setStatus($admin, $official, 'published');
        $this->get('/managed-media/'.$media->id)->assertOk()->assertHeader('content-type', 'image/png')->assertHeader('x-content-type-options', 'nosniff');
    }

    public function test_official_records_are_not_derived_from_employees_and_require_approval(): void
    {
        $admin = $this->administrator();
        $manager = app(OfficialManager::class);
        $official = $manager->create($admin, ['name' => 'Development fixture official', 'title' => 'Test title', 'slug' => 'fixture-official', 'biography' => 'Isolated fixture only.', 'display_order' => 0]);
        $this->get('/officials')->assertDontSee('Development fixture official');
        $this->get('/')->assertDontSee('Development fixture official');
        $this->get('/officials/fixture-official')->assertNotFound();
        $manager->setVerification($admin, $official, 'publishable');
        $manager->setStatus($admin, $official, 'published');
        $this->get('/officials')->assertSee('Development fixture official');
        $this->get('/')->assertSee('Development fixture official');
        $this->get('/officials/fixture-official')->assertOk()->assertSee('Isolated fixture only.');
        $this->get('/search?q=fixture')->assertSee('Development fixture official');
        $manager->update($admin, $official, ['name' => 'Edited fixture', 'title' => 'Edited title', 'slug' => 'fixture-official', 'biography' => 'Changed.', 'display_order' => 1]);
        $this->get('/officials/fixture-official')->assertNotFound();
        $this->get('/')->assertDontSee('Edited fixture');
        $this->assertSame('demo', $official->fresh()->verification_status);
        $this->assertDatabaseHas('audit_events', ['action' => 'officials.updated', 'subject_id' => (string) $official->id]);
    }
}
