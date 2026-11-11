<?php

use App\Models\BankAccount;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('it validates message input on citizen chat endpoint', function (): void {
    $response = $this->postJson('/api/ai/chat', [
        'message' => '',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'error' => 'VALIDATION_ERROR',
        ]);
});

test('it responds gracefully when gemini is not configured', function (): void {
    Config::set('services.gemini.key', null);

    $response = $this->postJson('/api/ai/chat', [
        'message' => 'How do I pay rates?',
        'locale' => 'en',
    ]);

    $response->assertStatus(503)
        ->assertJson([
            'success' => false,
            'error' => 'API_NOT_CONFIGURED',
        ]);
    expect($response->json('reply'))->toContain('council digital help desk is temporarily unavailable');
});

test('it returns grounded council answers via gemini', function (): void {
    Config::set('services.gemini.key', 'fake-gemini-key');

    // Seed a bank account and service
    BankAccount::query()->create([
        'bank_name' => 'CBZ Bank',
        'account_name' => 'Mutoko RDC Revenue',
        'account_number' => '0123456789',
        'branch_name' => 'Mutoko',
        'currency' => 'USD',
        'is_active' => true,
        'status' => 'published',
    ]);

    Service::query()->create([
        'slug' => 'business-licence',
        'name' => 'Shop Licence Application',
        'summary' => 'Licence required for operating grocery shops and retail outlets.',
        'display_order' => 1,
        'status' => 'published',
        'verification_status' => 'publishable',
        'published_at' => now(),
    ]);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Makadii! Kuti mubhadhare mitero yeKanzuru, munogona kushandisa account yeCBZ Bank: 0123456789 kuMutoko Branch.'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/api/ai/chat', [
        'message' => 'Ndingabhadhara sei mitero?',
        'history' => [],
        'locale' => 'sn',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    expect($response->json('reply'))->toContain('CBZ Bank');
});
