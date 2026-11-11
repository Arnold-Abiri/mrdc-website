<?php

namespace Tests\Unit;

use App\Services\Ai\GeminiService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    public function test_it_reports_unconfigured_when_no_api_key_is_present(): void
    {
        Config::set('services.gemini.key', null);
        $service = new GeminiService;

        $this->assertFalse($service->isConfigured());

        $response = $service->askCitizenAssistant('Makadii');
        $this->assertFalse($response['success']);
        $this->assertEquals('API_KEY_NOT_CONFIGURED', $response['error']);
    }

    public function test_it_generates_trilingual_system_instruction(): void
    {
        $service = new GeminiService('test-key');
        $instruction = $service->getSystemInstruction('Bylaw #4: Refuse collection on Tuesdays');

        $this->assertStringContainsString('ChiShona', $instruction);
        $this->assertStringContainsString('isiNdebele', $instruction);
        $this->assertStringContainsString('Mutoko Rural District Council', $instruction);
        $this->assertStringContainsString('Refuse collection on Tuesdays', $instruction);
    }

    public function test_it_successfully_calls_gemini_api_and_returns_content(): void
    {
        Config::set('services.gemini.key', 'fake-gemini-key');
        Config::set('services.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Makadii! Tingakubatsirai nei maererano nekanzuru yeMutoko?'],
                            ],
                        ],
                    ],
                ],
                'usageMetadata' => [
                    'promptTokenCount' => 45,
                    'candidatesTokenCount' => 12,
                ],
            ], 200),
        ]);

        $service = new GeminiService;
        $result = $service->askCitizenAssistant('Ndinoda kubhadhara rate yangu');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Makadii!', $result['text']);
    }

    public function test_it_classifies_citizen_enquiry_into_structured_json(): void
    {
        Config::set('services.gemini.key', 'fake-gemini-key');

        $fakeJson = json_encode([
            'department' => 'Engineering & Technical Services',
            'priority' => 'urgent',
            'language' => 'sn',
            'sentiment' => 'urgent',
            'summary_en' => 'Water pipe burst at Ward 3 Growth Point.',
            'summary_sn' => 'Pombi yemvura yaputika kuWard 3 paGrowth Point.',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $fakeJson],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GeminiService;
        $result = $service->classifyEnquiry([
            'subject' => 'Pombi yemvura yaputika',
            'message' => 'Mvura yakawanda iri kurasika paStand 42 kuWard 3, ndapota batsirai nekuchimbidza.',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Engineering & Technical Services', $result['classification']['department']);
        $this->assertEquals('urgent', $result['classification']['priority']);
        $this->assertEquals('sn', $result['classification']['language']);
    }
}
