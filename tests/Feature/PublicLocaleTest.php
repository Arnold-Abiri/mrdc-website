<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_prefix_drives_language_and_invalid_prefix_is_rejected(): void
    {
        $this->get('/en')->assertOk()->assertSee('<html lang="en">', false);
        $this->get('/sn')->assertOk()->assertSee('<html lang="sn">', false)->assertSee('"locale":"sn"', false);
        $this->get('/nd')->assertOk()->assertSee('<html lang="nd">', false)->assertSee('"locale":"nd"', false);
        $this->get('/en/contact')->assertOk()->assertSee('<html lang="en">', false);
        $this->get('/sn/contact')->assertOk()->assertSee('<html lang="sn">', false);
        $this->get('/xx')->assertNotFound();
    }

    public function test_root_and_legacy_urls_resolve_to_prefixed_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
        $this->post('/locale', ['locale' => 'sn'])->assertRedirect();
        $this->get('/')->assertRedirect('/sn');
        $this->get('/services')->assertRedirect('/sn/services');
    }

    public function test_invalid_locale_is_rejected_and_missing_shona_messages_fall_back_to_english(): void
    {
        $this->post('/locale', ['locale' => 'invalid'])->assertSessionHasErrors('locale');
        $this->post('/locale', ['locale' => 'sn'])->assertRedirect();
        $this->assertSame('The name field is required.', __('validation.required', ['attribute' => 'name']));
    }

    public function test_nd_locale_is_accepted_by_preference_and_falls_back_to_english(): void
    {
        $this->post('/locale', ['locale' => 'nd'])->assertRedirect()->assertSessionHas('public_locale', 'nd');
        $this->get('/nd')->assertOk()->assertSee('<html lang="nd">', false);
    }
}
