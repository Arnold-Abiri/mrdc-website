<?php

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Route::get('/__err/{code}', function (string $code) {
        abort((int) $code);
    })->whereIn('code', ['400', '401', '403', '404', '419', '429', '500', '503']);
});

test('unknown public page renders branded 404', function () {
    $this->get('/en/no-such-page-anywhere')->assertStatus(404)->assertSee('Page not found')->assertSee('Mutoko Rural District Council');
});

foreach ([400, 401, 403, 404, 419, 429, 500, 503] as $code) {
    test("error {$code} page renders council branding and recovery links", function () use ($code) {
        $response = $this->get("/__err/{$code}");
        $response->assertStatus($code)
            ->assertSee("ERROR {$code}")
            ->assertSee('Mutoko Rural District Council')
            ->assertSee('Return to homepage')
            ->assertSee('Contact the council');
    });
}

test('error pages carry noindex and require no javascript', function () {
    $response = $this->get('/__err/404');
    $response->assertSee('noindex, nofollow', false)->assertDontSee('@vite', false);
});
