<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_english_and_selection_persists_across_public_routes(): void
    {
        $this->get('/')->assertOk()->assertSee('<html lang="en">', false);
        $this->post('/locale', ['locale' => 'sn'])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('<html lang="sn">', false)->assertSee('"locale":"sn"', false);
        $this->get('/contact')->assertOk()->assertSee('<html lang="sn">', false);
        $this->post('/locale', ['locale' => 'en'])->assertRedirect();
        $this->get('/')->assertSee('<html lang="en">', false);
    }

    public function test_invalid_locale_is_rejected_and_missing_shona_messages_fall_back_to_english(): void
    {
        $this->post('/locale', ['locale' => 'invalid'])->assertSessionHasErrors('locale');
        $this->post('/locale', ['locale' => 'sn'])->assertRedirect();
        $this->assertSame('The name field is required.', __('validation.required', ['attribute' => 'name']));
    }
}
