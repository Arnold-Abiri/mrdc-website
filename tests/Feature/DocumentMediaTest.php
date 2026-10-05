<?php

namespace Tests\Feature;

use App\Domain\Cms\DocumentManager;
use App\Domain\Cms\MediaManager;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DocumentMediaTest extends TestCase
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

    public function test_spoofed_executable_upload_is_rejected(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $file = UploadedFile::fake()->createWithContent('report.pdf', '<?php echo "executed";');
        $this->expectException(ValidationException::class);
        app(MediaManager::class)->upload($admin, $file, ['title' => 'Test upload']);
    }

    public function test_documents_require_verification_and_publication_before_download(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $media = app(MediaManager::class)->upload($admin, UploadedFile::fake()->createWithContent('test.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"), ['title' => 'Development test file']);
        $manager = app(DocumentManager::class);
        $document = $manager->create($admin, ['slug' => 'development-test', 'title' => 'Development test document', 'category' => 'other', 'media_id' => $media->id, 'visibility' => 'public']);
        $this->get('/documents/development-test')->assertNotFound();
        $this->get('/documents/development-test/download')->assertNotFound();
        $this->expectException(HttpException::class);
        $manager->setStatus($admin, $document, 'published');
    }

    public function test_published_document_download_and_unpublish(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $media = app(MediaManager::class)->upload($admin, UploadedFile::fake()->createWithContent('test.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"), ['title' => 'Development test file']);
        $manager = app(DocumentManager::class);
        $document = $manager->create($admin, ['slug' => 'development-test', 'title' => 'Development test document', 'category' => 'other', 'media_id' => $media->id, 'visibility' => 'public']);
        $manager->setVerification($admin, $document, 'publishable');
        $manager->setStatus($admin, $document, 'published');
        $this->get('/documents/development-test')->assertOk();
        $this->get('/documents/development-test/download')->assertOk()->assertHeader('content-type', 'application/pdf');
        $manager->setStatus($admin, $document, 'unpublished');
        $this->get('/documents/development-test/download')->assertNotFound();
        $this->assertDatabaseHas('audit_events', ['action' => 'documents.unpublished', 'subject_id' => (string) $document->id]);
    }

    public function test_search_includes_only_approved_public_documents(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $media = app(MediaManager::class)->upload($admin, UploadedFile::fake()->createWithContent('guide.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"), ['title' => 'Development guide']);
        $manager = app(DocumentManager::class);
        $document = $manager->create($admin, ['slug' => 'development-guide', 'title' => 'Development guide', 'description' => 'Unique public water reference', 'category' => 'publication', 'media_id' => $media->id, 'visibility' => 'public']);
        $this->get('/search?q=Unique')->assertOk()->assertDontSee('Development guide');
        $manager->setVerification($admin, $document, 'publishable');
        $manager->setStatus($admin, $document, 'published');
        $this->get('/search?q=Unique')->assertOk()->assertSee('Development guide')->assertSee('Document');
        $manager->setStatus($admin, $document, 'unpublished');
        $this->get('/search?q=Unique')->assertOk()->assertDontSee('Development guide');
    }

    public function test_ordinary_user_cannot_publish(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $media = app(MediaManager::class)->upload($admin, UploadedFile::fake()->createWithContent('test.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"), ['title' => 'Development test file']);
        $document = app(DocumentManager::class)->create($admin, ['slug' => 'development-test', 'title' => 'Development test document', 'category' => 'other', 'media_id' => $media->id, 'visibility' => 'public']);
        $this->expectException(AuthorizationException::class);
        app(DocumentManager::class)->setStatus(User::factory()->create(), $document, 'published');
    }
}
