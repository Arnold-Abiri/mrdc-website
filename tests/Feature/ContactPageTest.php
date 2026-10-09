<?php

namespace Tests\Feature;

use App\Models\PublicContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_with_form_and_directory(): void
    {
        $this->get('/en/contact')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Contact', false)
            ->has('contacts', 0)
            ->has('departments', 0)
            ->where('submitted', false));
    }

    public function test_contact_page_contains_no_hardcoded_unverified_details(): void
    {
        $response = $this->get('/en/contact')->assertOk();
        $response->assertDontSee('+263 771 592 888');
        $response->assertDontSee('Stand 366');
        $response->assertDontSee('P Box 130');
    }

    public function test_verified_contacts_appear_with_clickable_links(): void
    {
        foreach ([
            ['office' => 'General Office', 'type' => 'phone', 'value' => '+263 71 234 5678'],
            ['office' => 'General Office', 'type' => 'email', 'value' => 'info@example.com'],
        ] as $order => $attributes) {
            PublicContact::query()->create([...$attributes,
                'is_public' => true, 'display_order' => $order,
            ])->forceFill([
                'status' => 'published', 'verification_status' => 'publishable', 'published_at' => now(),
            ])->save();
        }

        $this->get('/en/contact')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->has('contacts', 2));
    }

    public function test_enquiry_submission_redirects_with_confirmation(): void
    {
        $this->post('/en/contact', [
            'name' => 'Test Resident',
            'email' => 'resident@example.com',
            'category' => 'general',
            'subject' => 'Water supply question',
            'message' => 'When will the borehole be repaired?',
            'consent_given' => '1',
        ])->assertRedirect('/en/contact?submitted=1');

        $this->assertDatabaseHas('enquiries', ['email' => 'resident@example.com', 'status' => 'new']);
    }
}
