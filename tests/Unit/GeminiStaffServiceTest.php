<?php

namespace Tests\Unit;

use App\Services\Ai\GeminiService;
use App\Services\Ai\GeminiStaffService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiStaffServiceTest extends TestCase
{
    public function test_it_translates_editorial_to_shona(): void
    {
        Config::set('services.gemini.key', 'fake-key');

        $fakeOutput = json_encode([
            'title' => 'Chiziviso cheMusangano weKanzuru',
            'summary' => 'Musangano wevagari uchaitwa neChitatu.',
            'body' => 'Vagari vose vanokokwa kuMutoko Growth Point.',
            'seo_title' => 'Musangano weKanzuru - Mutoko RDC',
            'meta_description' => 'Chiziviso chemusangano wevagari uchaitwa kuMutoko.',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $fakeOutput],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GeminiStaffService(new GeminiService);
        $result = $service->translateEditorial(
            'Council Meeting Notice',
            'Resident meeting on Wednesday.',
            'All residents are invited to Mutoko Growth Point.',
            'Council Meeting - Mutoko RDC',
            'Notice of meeting for residents in Mutoko.',
            'sn'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('Chiziviso cheMusangano weKanzuru', $result['title']);
        $this->assertEquals('Musangano weKanzuru - Mutoko RDC', $result['seo_title']);
        $this->assertEquals('Chiziviso chemusangano wevagari uchaitwa kuMutoko.', $result['meta_description']);
    }

    public function test_it_translates_a_single_field_to_ndebele(): void
    {
        Config::set('services.gemini.key', 'fake-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode(['translation' => 'Ihhovisi leMitero'])],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GeminiStaffService(new GeminiService);
        $result = $service->translateField('Revenue Office', 'office name', 'nd');

        $this->assertTrue($result['success']);
        $this->assertEquals('Ihhovisi leMitero', $result['translation']);
    }

    public function test_it_translates_multiple_fields_at_once(): void
    {
        Config::set('services.gemini.key', 'fake-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode(['office' => 'Ihhovisi leMitero'])],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GeminiStaffService(new GeminiService);
        $result = $service->translateFields('PublicContact', ['office' => 'Revenue Office'], 'nd');

        $this->assertTrue($result['success']);
        $this->assertEquals('Ihhovisi leMitero', $result['translations']['office']);
    }

    public function test_it_drafts_citizen_response(): void
    {
        Config::set('services.gemini.key', 'fake-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Kwaziwai vaTendai. Ndatenda nekutizivisa nezve pombi iyi. Chikwata chedu chiri munzira.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GeminiStaffService(new GeminiService);
        $result = $service->draftEnquiryResponse(
            [
                'name' => 'Tendai M.',
                'subject' => 'Burst pipe',
                'message' => 'Mvura iri kubuda mumugwagwa',
                'category' => 'fault_report',
            ],
            'Plumbing team dispatched this morning',
            'sn'
        );

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Kwaziwai vaTendai', $result['response']);
    }
}
