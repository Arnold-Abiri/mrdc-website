<?php

namespace Tests\Feature;

use App\Domain\Cms\EditorialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditorialWorkflowTest extends TestCase
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

    public function test_slug_is_suggested_from_title(): void
    {
        $this->assertSame('bridge-commissioning-2026', HandlesFeaturedImageUpload::slugFromTitle('Bridge Commissioning 2026!'));
        $this->assertSame('', HandlesFeaturedImageUpload::slugFromTitle('!!!'));
        $this->assertLessThanOrEqual(160, strlen(HandlesFeaturedImageUpload::slugFromTitle(str_repeat('Long title ', 50))));
    }

    public function test_inline_image_upload_attaches_featured_media(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin);
        Storage::fake(config('cms.media_disk'));

        $uploader = new class
        {
            use HandlesFeaturedImageUpload;

            public function run(array $data, ?string $title = null): array
            {
                return $this->attachInlineFeaturedImage($data, $title);
            }
        };

        $data = $uploader->run([
            'title' => 'Bridge opening',
            'department_id' => null,
            'featured_image_upload' => UploadedFile::fake()->image('bridge.jpg', 800, 600),
            'featured_image_alt' => 'Crowd at bridge',
            'featured_image_caption' => 'Photo: Council',
        ], 'Bridge opening');

        $this->assertArrayNotHasKey('featured_image_upload', $data);
        $this->assertArrayNotHasKey('featured_image_alt', $data);
        $this->assertArrayNotHasKey('featured_image_caption', $data);
        $this->assertNotNull($data['featured_media_id']);

        $media = Media::query()->findOrFail($data['featured_media_id']);
        $this->assertSame('image/jpeg', $media->mime_type);
        $this->assertSame('Crowd at bridge', $media->alt_text);
        $this->assertSame('Photo: Council', $media->caption);
    }

    public function test_verify_then_publish_makes_news_public(): void
    {
        $admin = $this->administrator();
        $manager = app(EditorialManager::class);
        $item = $manager->create($admin, [
            'type' => 'news', 'title' => 'QA bridge', 'slug' => 'qa-bridge',
            'body' => 'Testing only', 'is_urgent' => 0, 'display_order' => 0,
        ]);

        $manager->setVerification($admin, $item, 'publishable');
        $manager->setStatus($admin, $item->fresh(), 'published');

        $fresh = $item->fresh();
        $this->assertSame('publishable', $fresh->verification_status);
        $this->assertSame('published', $fresh->status);
        $this->get('/en/news/qa-bridge')->assertOk();
    }
}
