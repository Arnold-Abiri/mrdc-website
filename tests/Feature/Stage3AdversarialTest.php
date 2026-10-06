<?php

namespace Tests\Feature;

use App\Domain\Cms\MediaManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Stage3AdversarialTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(SecuritySeeder::class);
        $actor = User::factory()->create();
        $role = Role::findByName('System Administrator');
        $actor->assignRole($role);
        DB::table('user_role_scopes')->insert(['user_id' => $actor->id, 'role_id' => $role->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);

        return $actor;
    }

    public function test_hostile_uploads_are_rejected_before_storage(): void
    {
        Storage::fake('local');
        $actor = $this->administrator();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/pWQAAAAASUVORK5CYII=', true);
        $attempts = [
            UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;'),
            UploadedFile::fake()->createWithContent('payload.html', '<script>alert(1)</script>'),
            UploadedFile::fake()->createWithContent('vector.svg', '<svg onload="alert(1)"/>'),
            UploadedFile::fake()->createWithContent('spoofed.png', '<?php echo 1;'),
            UploadedFile::fake()->createWithContent('double.php.png', '<?php echo 1;'),
            UploadedFile::fake()->createWithContent('mismatch.php', $png ?: ''),
            UploadedFile::fake()->createWithContent('broken.png', 'not an image'),
            UploadedFile::fake()->create('oversized.pdf', 10241, 'application/pdf'),
        ];
        foreach ($attempts as $file) {
            try {
                app(MediaManager::class)->upload($actor, $file, ['title' => 'Hostile test']);
                $this->fail('Hostile upload was accepted: '.$file->getClientOriginalName());
            } catch (ValidationException) {
                $this->assertDatabaseCount('media', 0);
            }
        }
        $this->assertSame([], Storage::disk('local')->allFiles('cms'));
    }

    public function test_suspicious_image_filename_is_sanitized_and_stored_under_generated_name(): void
    {
        Storage::fake('local');
        $actor = $this->administrator();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/pWQAAAAASUVORK5CYII=', true);
        $media = app(MediaManager::class)->upload($actor, UploadedFile::fake()->createWithContent('shell.php.😈.png', $png ?: ''), ['title' => 'Isolated filename test']);
        $this->assertMatchesRegularExpression('/^cms\/\d{4}\/\d{2}\/[a-f0-9-]{36}\.png$/', $media->storage_path);
        $this->assertStringNotContainsString('php', $media->storage_path);
        $this->assertStringNotContainsString('/', $media->original_filename);
        $this->get('/en/managed-media/'.$media->id)->assertNotFound();
    }

    public function test_guest_cannot_enter_admin_or_read_private_resource_identifiers(): void
    {
        $this->get('/en')->assertHeader('Content-Security-Policy', "frame-ancestors 'self'");
        $this->get('/admin/media')->assertRedirect('/admin/login');
        $this->get('/en/managed-media/999999')->assertNotFound();
        $this->get('/en/documents/private-draft')->assertNotFound();
        $this->get('/en/pages/private-draft')->assertNotFound();
        $this->get('/en/search?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/en/search?q='.str_repeat('a', 101))->assertStatus(422);
        $this->get('/en/search?q=%25%5F')->assertOk();
        $this->get('/en/search?q=%27%20OR%201%3D1--')->assertOk();
        $this->get('/en/search?q=%FF')->assertOk();
    }
}
