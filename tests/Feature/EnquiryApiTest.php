<?php

use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

function apiSubmissionPayload(): array
{
    return ['name' => 'Resident', 'email' => 'resident@example.test', 'subject' => 'Service request', 'message' => 'Please assist with this service request.'];
}

test('residents can submit and privately track either submission type', function (string $type) {
    $data = $this->postJson('/api/v1/'.$type, apiSubmissionPayload() + ['status' => 'resolved', 'assigned_to' => 999, 'tracking_token_hash' => 'attacker'])->assertCreated()->assertHeader('Cache-Control', 'no-store, private')->json('data');
    $enquiry = Enquiry::query()->sole();
    expect($enquiry->status)->toBe('new')->and($enquiry->assigned_to)->toBeNull()
        ->and($enquiry->category)->toBe($type === 'feedback' ? 'feedback' : 'general')
        ->and($enquiry->tracking_token_hash)->toBe(hash('sha256', $data['tracking_token']))
        ->and($enquiry->toArray())->not->toHaveKey('tracking_token_hash');
    $enquiry->forceFill(['status' => 'in_progress'])->save();
    $this->postJson('/api/v1/'.$type.'/track', ['reference' => $data['reference'], 'tracking_token' => $data['tracking_token']])->assertOk()->assertExactJson(['data' => [
        'reference' => $enquiry->public_id, 'status' => 'in_progress', 'submitted_at' => $enquiry->submitted_at->toISOString(), 'updated_at' => $enquiry->updated_at->toISOString(),
    ]]);
    $this->postJson('/api/v1/'.$type.'/track', ['reference' => $data['reference'], 'tracking_token' => str_repeat('a', 64)])->assertNotFound();
})->with(['enquiries', 'feedback']);

test('a token cannot access a different submission or a legacy enquiry', function () {
    $data = $this->postJson('/api/v1/enquiries', apiSubmissionPayload())->assertCreated()->json('data');
    $other = Enquiry::factory()->create();
    $this->postJson('/api/v1/enquiries/track', ['reference' => $other->public_id, 'tracking_token' => $data['tracking_token']])->assertNotFound();
    $this->postJson('/api/v1/enquiries/track', ['reference' => '00000000-0000-4000-8000-000000000000', 'tracking_token' => $data['tracking_token']])->assertNotFound();
    $this->getJson('/api/v1/enquiries/'.$data['reference'])->assertNotFound();
});

test('submissions reuse validation and reject honeypots', function () {
    $this->postJson('/api/v1/feedback', apiSubmissionPayload() + ['website' => 'spam'])->assertUnprocessable()->assertJsonValidationErrors('website');
    $this->postJson('/api/v1/enquiries', ['email' => 'invalid'])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    expect(Enquiry::count())->toBe(0);
    $this->postJson('/api/v1/enquiries/track', ['reference' => 'bad', 'tracking_token' => 'bad'])->assertUnprocessable()->assertJsonValidationErrors(['reference', 'tracking_token']);
});

test('submission requests share a rate limit across both entry points', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->postJson('/api/v1/enquiries', apiSubmissionPayload())->assertCreated();
    }
    $this->postJson('/api/v1/feedback', apiSubmissionPayload())->assertStatus(429);
});

test('tracking attempts are throttled', function () {
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $this->postJson('/api/v1/enquiries/track', [])->assertUnprocessable();
    }
    $this->postJson('/api/v1/feedback/track', [])->assertStatus(429);
});
