<?php

use App\Domain\Cms\EnquiryManager;
use App\Filament\Concerns\SlugField;
use App\Filament\Resources\Projects\CouncilProjectResource;
use App\Filament\Resources\Projects\CouncilProjectResource\RelationManagers\UpdatesRelationManager;
use App\Filament\Resources\ProjectUpdates\ProjectUpdateResource;
use App\Filament\Resources\Tenders\RelationManagers\BiddersRelationManager;
use App\Filament\Resources\Tenders\TenderResource;
use App\Mail\EnquiryStatusUpdated;
use App\Models\Document;
use App\Models\Enquiry;
use App\Models\Tender;
use App\Models\TenderBidder;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('officer can notify citizen and dispatch queued status update email', function () {
    Mail::fake();

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

    $enquiry = Enquiry::factory()->create([
        'name' => 'Farai Chikwanha',
        'email' => 'farai@example.test',
        'subject' => 'Borehole repair in Ward 3',
        'message' => 'The borehole near Mutoko high school pump has stopped working.',
        'status' => 'in_progress',
    ]);

    app(EnquiryManager::class)->notifyCitizen($admin, $enquiry, 'A repair technician has been dispatched to assess the pump.');

    Mail::assertQueued(EnquiryStatusUpdated::class, function (EnquiryStatusUpdated $mail) use ($enquiry): bool {
        return $mail->hasTo('farai@example.test')
            && str_contains($mail->officerNote, 'repair technician has been dispatched')
            && $mail->enquiry->id === $enquiry->id;
    });

    expect($enquiry->fresh()->notes()->count())->toBe(1);
    expect($enquiry->fresh()->notes()->first()->body)->toContain('[Citizen Notification Sent]');
});

test('public user registers and downloads tender document logging bidder record', function () {
    Storage::fake('local');
    Storage::disk('local')->put('tenders/test-tender.pdf', '%PDF-1.4 test file content');

    $mediaId = DB::table('media')->insertGetId([
        'title' => 'Tender Document Specifications',
        'original_filename' => 'Tender-MRDC-2026.pdf',
        'storage_path' => 'tenders/test-tender.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $doc = new Document([
        'slug' => 'tender-specs-doc',
        'title' => 'Tender Specifications Document',
        'category' => 'notice',
        'media_id' => $mediaId,
        'visibility' => 'public',
    ]);
    $doc->status = 'published';
    $doc->verification_status = 'publishable';
    $doc->published_at = now();
    $doc->save();

    $tender = new Tender([
        'slug' => 'mrdc-2026-boreholes',
        'reference' => 'MRDC/2026/015',
        'title' => 'Drilling of 10 Boreholes',
        'description' => 'Tender for borehole drilling across selected wards in Mutoko.',
        'lifecycle_status' => 'open',
        'document_id' => $doc->id,
        'display_order' => 1,
    ]);
    $tender->status = 'published';
    $tender->verification_status = 'publishable';
    $tender->published_at = now();
    $tender->save();

    $response = $this->post('/en/tenders/mrdc-2026-boreholes/download', [
        'company_name' => 'Mashonaland Drillers Pvt Ltd',
        'representative_name' => 'Tinashe Moyo',
        'email' => 'bids@mashdrillers.co.zw',
        'phone' => '+263772123456',
    ]);

    $response->assertOk();
    $response->assertHeader('content-disposition');

    expect(TenderBidder::query()->where('tender_id', $tender->id)->count())->toBe(1);

    $bidder = TenderBidder::query()->where('tender_id', $tender->id)->first();
    expect($bidder->company_name)->toBe('Mashonaland Drillers Pvt Ltd');
    expect($bidder->representative_name)->toBe('Tinashe Moyo');
    expect($bidder->email)->toBe('bids@mashdrillers.co.zw');
    expect($tender->bidders()->count())->toBe(1);
});

test('project updates resource is hidden from direct sidebar navigation while council project has relation manager', function () {
    expect(ProjectUpdateResource::shouldRegisterNavigation())->toBeFalse();

    $relations = CouncilProjectResource::getRelations();
    expect($relations)->toContain(UpdatesRelationManager::class);

    $tenderRelations = TenderResource::getRelations();
    expect($tenderRelations)->toContain(BiddersRelationManager::class);
});

test('slug field auto fills on create and preserves custom slug on edit', function () {
    $closure = SlugField::autoFillHook('slug', 160);

    $setCalls = [];
    $set = function ($field, $value) use (&$setCalls) {
        $setCalls[$field] = $value;
    };

    // Operation create sets slug
    $closure('Mutoko Growth Point Borehole Project', $set, 'create');
    expect($setCalls)->toHaveKey('slug');
    expect($setCalls['slug'])->toBe('mutoko-growth-point-borehole-project');

    // Operation edit does not overwrite existing slug
    $setCalls = [];
    $closure('Updated Title That Should Not Change Slug', $set, 'edit');
    expect($setCalls)->toBeEmpty();
});
