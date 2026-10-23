<?php

use App\Models\CouncilMeeting;
use Database\Seeders\CouncilTeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('chairperson portal page renders correctly with authentic leadership profile across locales', function () {
    $this->seed(CouncilTeamSeeder::class);

    CouncilMeeting::query()->create([
        'title' => 'First Ordinary Full Council Session',
        'meeting_type' => 'full_council',
        'meeting_status' => 'scheduled',
        'scheduled_date' => now()->addDays(5)->toDateString(),
        'venue' => 'Mutoko RDC Council Chambers',
        'status' => 'published',
        'verification_status' => 'publishable',
        'published_at' => now(),
    ]);

    $titles = [
        'en' => 'Council Chairperson',
        'sn' => 'Sachigaro weKanzuru',
        'nd' => 'USihlalo woMkhandlu',
    ];

    foreach (['en', 'sn', 'nd'] as $locale) {
        $response = $this->get("/{$locale}/council/chairperson");
        $response->assertOk();
        $response->assertSee('Alderman Trust Kachidza');
        $response->assertSee($titles[$locale]);
    }
});
