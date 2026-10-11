<?php

namespace Tests\Feature;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Models\Media;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiteSettingLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_configured_logo_is_publicly_served_and_unselected_media_remains_private(): void
    {
        Storage::fake(config('cms.media_disk'));
        $this->seed(SecuritySeeder::class);
        $admin = User::factory()->create();
        $role = Role::findByName('System Administrator');
        $admin->assignRole($role);
        DB::table('user_role_scopes')->insert([
            'user_id' => $admin->id,
            'role_id' => $role->id,
            'scope_type' => 'global',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $setting = SiteSetting::query()->where('key', 'logo_media_id')->firstOrFail();
        Livewire::actingAs($admin)
            ->test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->set('data.logo_file', UploadedFile::fake()->image('council-logo.png'))
            ->call('save')
            ->assertHasNoErrors();

        $media = Media::query()->findOrFail($setting->fresh()->value);
        $this->get('/en/managed-media/'.$media->id)
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
        $this->get('/en')->assertOk();
        $this->assertStringContainsString(
            '/en/managed-media/'.$media->id,
            view('filament.components.admin-brand')->render(),
        );
    }
}
