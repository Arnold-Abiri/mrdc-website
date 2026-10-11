<?php

namespace Tests\Feature;

use App\Domain\Cms\GalleryManager;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\GallerySeeder;
use Database\Seeders\SecuritySeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_sourced_tere_ruins_photo_is_visible_in_the_public_gallery(): void
    {
        Storage::fake('local');
        $this->seed(GallerySeeder::class);

        $gallery = Gallery::query()->where('slug', 'tere-ruins-mutoko')->firstOrFail();
        $this->get('/en/galleries')->assertInertia(fn (Assert $page) => $page
            ->component('Galleries', false)
            ->where('galleries.0.slug', 'tere-ruins-mutoko')
            ->where('galleries.0.image_count', 1));
        $this->get('/en/galleries/tere-ruins-mutoko')->assertInertia(fn (Assert $page) => $page
            ->component('Gallery', false)
            ->where('images.0.url', public_route('managed-media.show', $gallery->cover_media_id)));
        $this->get('/en/managed-media/'.$gallery->cover_media_id)->assertOk()->assertHeader('Content-Type', 'image/jpeg');
    }

    private function administrator(): User
    {
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $role = Role::findByName('System Administrator');
        $user->assignRole($role);
        DB::table('user_role_scopes')->insert(['user_id' => $user->id, 'role_id' => $role->id, 'scope_type' => 'global', 'created_at' => now(), 'updated_at' => now()]);

        return $user;
    }

    private function image(string $storagePath): Media
    {
        $disk = Storage::disk(config('cms.media_disk', 'local'));
        $disk->put($storagePath, 'image-bytes');

        return Media::unguarded(fn (): Media => Media::query()->create([
            'title' => 'QA gallery image '.$storagePath,
            'alt_text' => 'QA image alt text',
            'caption' => 'QA image caption',
            'original_filename' => basename($storagePath),
            'storage_path' => $storagePath,
            'mime_type' => 'image/jpeg',
            'size' => 11,
            'width' => 1200,
            'height' => 800,
            'status' => 'active',
        ]));
    }

    /**
     * @param  list<Media>  $images
     * @return array<string, mixed>
     */
    private function payload(array $images, array $overrides = []): array
    {
        return [
            'slug' => 'qa-community-gallery',
            'title' => 'QA community gallery',
            'summary' => 'Testing only',
            'description' => 'Testing only body',
            'location' => 'Mutoko Centre',
            'album_date' => null,
            'is_featured' => false,
            'display_order' => 0,
            'media' => array_map(fn (Media $media): array => ['media_id' => $media->id, 'caption' => null], $images),
            ...$overrides,
        ];
    }

    public function test_draft_gallery_is_private_and_publishing_exposes_it_publicly(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $images = [$this->image('gallery/one.jpg'), $this->image('gallery/two.jpg')];
        $manager = app(GalleryManager::class);
        $gallery = $manager->create($admin, $this->payload($images));

        $this->get('/en/galleries')->assertOk()->assertDontSee('QA community gallery');
        $this->get('/en/galleries/qa-community-gallery')->assertNotFound();
        $this->get('/en/search?q=community+gallery')->assertOk()->assertDontSee('QA community gallery');
        $this->get('/en/managed-media/'.$images[0]->id)->assertNotFound();

        $manager->setVerification($admin, $gallery, 'publishable');
        $manager->setStatus($admin, $gallery, 'published');

        $this->get('/en/galleries')->assertInertia(fn (Assert $page) => $page
            ->component('Galleries', false)
            ->has('galleries', 1)
            ->where('galleries.0.title', 'QA community gallery')
            ->where('galleries.0.image_count', 2)
            ->where('galleries.0.cover_url', public_route('managed-media.show', $images[0]->id)));
        $this->get('/en/galleries/qa-community-gallery')->assertInertia(fn (Assert $page) => $page
            ->component('Gallery', false)
            ->has('images', 2)
            ->where('images.0.alt', 'QA image alt text')
            ->where('images.1.url', public_route('managed-media.show', $images[1]->id)));
        $this->get('/en/search?q=community+gallery')->assertOk()->assertSee('QA community gallery');
        $this->get('/en/managed-media/'.$images[1]->id)->assertOk();
        $this->get('/sitemap.xml')->assertOk()->assertSee('/en/galleries/qa-community-gallery', false);
    }

    public function test_gallery_images_keep_their_order_and_per_image_captions(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $first = $this->image('gallery/first.jpg');
        $second = $this->image('gallery/second.jpg');
        $manager = app(GalleryManager::class);

        $gallery = $manager->create($admin, $this->payload([$first, $second], [
            'media' => [
                ['media_id' => $second->id, 'caption' => 'Second presented first'],
                ['media_id' => $first->id, 'caption' => 'First presented second'],
            ],
        ]));

        $ordered = $gallery->fresh()->media;
        $this->assertSame([$second->id, $first->id], $ordered->pluck('id')->all());
        $this->assertSame('Second presented first', $ordered->first()->pivot->caption);
        $this->assertSame('First presented second', $ordered->last()->pivot->caption);
    }

    public function test_editing_a_published_gallery_returns_it_to_demo_draft(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $images = [$this->image('gallery/edit.jpg')];
        $manager = app(GalleryManager::class);
        $gallery = $manager->create($admin, $this->payload($images));
        $manager->setVerification($admin, $gallery, 'publishable');
        $manager->setStatus($admin, $gallery, 'published');

        $manager->update($admin, $gallery, $this->payload($images, ['title' => 'QA edited gallery']));

        $this->assertSame('draft', $gallery->fresh()->status);
        $this->assertSame('demo', $gallery->fresh()->verification_status);
        $this->get('/en/galleries/qa-community-gallery')->assertNotFound();
    }

    public function test_publishing_requires_a_verified_album(): void
    {
        Storage::fake('local');
        $admin = $this->administrator();
        $images = [$this->image('gallery/verify.jpg')];
        $gallery = app(GalleryManager::class)->create($admin, $this->payload($images));

        $this->expectException(HttpException::class);
        app(GalleryManager::class)->setStatus($admin, $gallery, 'published');
    }

    public function test_staff_without_gallery_permission_cannot_create_albums(): void
    {
        Storage::fake('local');
        $this->seed(SecuritySeeder::class);
        $user = User::factory()->create();
        $images = [$this->image('gallery/denied.jpg')];

        $this->assertFalse($user->can('create', Gallery::class));
        $this->expectException(AuthorizationException::class);
        app(GalleryManager::class)->create($user, $this->payload($images));
    }
}
