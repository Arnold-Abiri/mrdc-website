<?php

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

uses(TestCase::class, RefreshDatabase::class);

function createAdminUser(): User
{
    test()->seed(SecuritySeeder::class);
    $user = User::factory()->create();
    $role = Role::findByName('System Administrator');
    $user->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $user->id,
        'role_id' => $role->id,
        'scope_type' => 'global',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $user;
}

test('slide inline image upload attaches media', function () {
    $admin = createAdminUser();
    $this->actingAs($admin);
    Storage::fake(config('cms.media_disk'));

    $uploader = new class
    {
        use HandlesFeaturedImageUpload;

        public function run(array $data, ?string $title = null): array
        {
            return $this->attachInlineMedia(
                $data,
                uploadKey: 'slide_image_upload',
                targetKey: 'media_id',
                altKey: 'slide_image_alt',
                fallbackTitle: $title,
                titleSuffix: ' slide image',
            );
        }
    };

    $data = $uploader->run([
        'headline' => 'Welcome to Mutoko RDC',
        'slide_image_upload' => UploadedFile::fake()->image('hero.jpg', 1920, 1080),
        'slide_image_alt' => 'Aerial view of Mutoko centre',
    ], 'Welcome to Mutoko RDC');

    expect($data)->not->toHaveKey('slide_image_upload')
        ->not->toHaveKey('slide_image_alt')
        ->toHaveKey('media_id');

    $media = Media::query()->findOrFail($data['media_id']);
    expect($media->mime_type)->toBe('image/jpeg')
        ->and($media->title)->toBe('Welcome to Mutoko RDC slide image')
        ->and($media->alt_text)->toBe('Aerial view of Mutoko centre');
});

test('document inline pdf upload attaches media', function () {
    $admin = createAdminUser();
    $this->actingAs($admin);
    Storage::fake(config('cms.media_disk'));

    $uploader = new class
    {
        use HandlesFeaturedImageUpload;

        public function run(array $data, ?string $title = null): array
        {
            return $this->attachInlineMedia(
                $data,
                uploadKey: 'document_file_upload',
                targetKey: 'media_id',
                fallbackTitle: $title,
                titleSuffix: ' document',
            );
        }
    };

    $data = $uploader->run([
        'title' => 'Council Budget 2026',
        'category' => 'budget',
        'document_file_upload' => UploadedFile::fake()->createWithContent('budget.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"),
    ], 'Council Budget 2026');

    expect($data)->not->toHaveKey('document_file_upload')
        ->toHaveKey('media_id');

    $media = Media::query()->findOrFail($data['media_id']);
    expect($media->mime_type)->toBe('application/pdf')
        ->and($media->title)->toBe('Council Budget 2026 document');
});

test('official inline photo upload attaches media', function () {
    $admin = createAdminUser();
    $this->actingAs($admin);
    Storage::fake(config('cms.media_disk'));

    $uploader = new class
    {
        use HandlesFeaturedImageUpload;

        public function run(array $data, ?string $title = null): array
        {
            return $this->attachInlineMedia(
                $data,
                uploadKey: 'photo_upload',
                targetKey: 'photo_media_id',
                altKey: 'photo_alt',
                fallbackTitle: $title,
                titleSuffix: ' portrait',
            );
        }
    };

    $data = $uploader->run([
        'name' => 'John Doe',
        'photo_upload' => UploadedFile::fake()->image('john.png', 400, 400),
        'photo_alt' => 'Portrait of John Doe',
    ], 'John Doe');

    expect($data)->not->toHaveKey('photo_upload')
        ->not->toHaveKey('photo_alt')
        ->toHaveKey('photo_media_id');

    $media = Media::query()->findOrFail($data['photo_media_id']);
    expect($media->mime_type)->toBe('image/png')
        ->and($media->title)->toBe('John Doe portrait')
        ->and($media->alt_text)->toBe('Portrait of John Doe');
});
