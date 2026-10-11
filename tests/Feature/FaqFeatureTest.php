<?php

namespace Tests\Feature;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_faqs_page_renders_successfully(): void
    {
        $response = $this->get('/en/faqs');

        $response->assertStatus(200);
    }

    public function test_public_rates_page_renders_successfully(): void
    {
        $response = $this->get('/en/rates');

        $response->assertStatus(200);
    }

    public function test_search_query_finds_matching_faqs(): void
    {
        Faq::create([
            'question' => 'How do I pay my rates via Ecocash or bank transfer?',
            'answer' => 'Always quote your council assessment number.',
            'category' => 'billing',
            'display_order' => 1,
            'is_published' => true,
            'is_featured' => true,
        ]);

        $response = $this->get('/en/search?q=Ecocash');

        $response->assertStatus(200);
    }
}
