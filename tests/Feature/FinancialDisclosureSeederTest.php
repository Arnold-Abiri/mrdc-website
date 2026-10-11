<?php

use App\Models\Document;
use App\Models\Media;
use Database\Seeders\FinancialDisclosureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('official audit reports are seeded once and appear under the correct financial year', function () {
    Storage::fake(config('cms.media_disk'));

    $this->seed(FinancialDisclosureSeeder::class);
    $this->seed(FinancialDisclosureSeeder::class);

    expect(Document::query()->count())->toBe(2)
        ->and(Media::query()->count())->toBe(2);

    $this->get('/en/transparency')
        ->assertOk()
        ->assertSee('Auditor-General local authorities report (2024)')
        ->assertSee('Auditor-General local authorities report (2023)');

    $this->get('/en/transparency?category=financial_report&year=2023')
        ->assertOk()
        ->assertSee('Mutoko RDC 2023')
        ->assertDontSee('Mutoko RDC 2022');

    $this->get('/en/documents/auditor-general-local-authorities-2024-mutoko-2023/download')
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
