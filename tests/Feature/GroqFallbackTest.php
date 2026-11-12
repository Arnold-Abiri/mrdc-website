<?php

use App\Models\BankAccount;
use App\Services\Ai\GeminiService;
use App\Services\Ai\GeminiStaffService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('it falls back to groq when gemini fails on citizen chat', function (): void {
    Config::set('services.gemini.key', 'fake-gemini-key');
    Config::set('services.groq.key', 'fake-groq-key');

    // Seed dummy bank account so grounding context is populated
    BankAccount::query()->create([
        'bank_name' => 'CBZ Bank',
        'account_name' => 'Mutoko RDC Revenue',
        'account_number' => '02923984010012',
        'branch_name' => 'Mutoko',
        'currency' => 'ZiG',
        'is_active' => true,
        'status' => 'published',
    ]);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Resource exhausted'],
        ], 503),
        'https://api.groq.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Kwaziwai! To pay council rates, please deposit into CBZ Bank account 02923984010012.',
                    ],
                ],
            ],
            'usage' => ['total_tokens' => 120],
        ], 200),
    ]);

    $response = $this->postJson('/api/ai/chat', [
        'message' => 'How do I pay rates?',
        'locale' => 'en',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'reply' => 'Kwaziwai! To pay council rates, please deposit into CBZ Bank account 02923984010012.',
        ]);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    Http::assertSent(fn ($request) => str_contains($request->url(), 'api.groq.com'));
});

test('it uses groq directly if gemini key is missing', function (): void {
    Config::set('services.gemini.key', null);
    Config::set('services.groq.key', 'fake-groq-key');

    Http::fake([
        'https://api.groq.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Mutoko RDC Help Desk is operational via Groq Cloud.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/api/ai/chat', [
        'message' => 'Hello',
        'locale' => 'en',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'reply' => 'Mutoko RDC Help Desk is operational via Groq Cloud.',
        ]);

    Http::assertNotSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    Http::assertSent(fn ($request) => str_contains($request->url(), 'api.groq.com'));
});

test('it falls back to groq on editorial translation failure', function (): void {
    Config::set('services.gemini.key', 'fake-gemini-key');
    Config::set('services.groq.key', 'fake-groq-key');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Overloaded'],
        ], 503),
        'https://api.groq.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'title' => 'Chiziviso cheMvura',
                            'summary' => 'Kugadziriswa kwepombi',
                            'body' => 'Pombi dzechirwere dziri kugadziriswa.',
                            'seo_title' => 'Chiziviso',
                            'meta_description' => 'Mvura',
                        ]),
                    ],
                ],
            ],
        ], 200),
    ]);

    $staffService = app(GeminiStaffService::class);
    $result = $staffService->translateEditorial('Water Notice', 'Maintenance', 'Fixing pipes', null, null, 'sn');

    expect($result)->toHaveKey('success', true);
    expect($result)->toHaveKey('title', 'Chiziviso cheMvura');
});

test('it falls back to groq on enquiry classification failure', function (): void {
    Config::set('services.gemini.key', 'fake-gemini-key');
    Config::set('services.groq.key', 'fake-groq-key');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Rate limit'],
        ], 429),
        'https://api.groq.com/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'department' => 'Engineering & Technical Services',
                            'priority' => 'urgent',
                            'language' => 'sn',
                            'sentiment' => 'urgent',
                            'summary_en' => 'Water pipe burst reported in Ward 12.',
                            'summary_sn' => 'Pombi yemvura yaputika kuWard 12.',
                        ]),
                    ],
                ],
            ],
        ], 200),
    ]);

    $geminiService = app(GeminiService::class);
    $result = $geminiService->classifyEnquiry([
        'subject' => 'Burst pipe',
        'message' => 'Pombi yaputika',
    ]);

    expect($result)->toHaveKey('success', true);
    expect($result['classification'])->toHaveKey('department', 'Engineering & Technical Services');
    expect($result['classification'])->toHaveKey('priority', 'urgent');
});
