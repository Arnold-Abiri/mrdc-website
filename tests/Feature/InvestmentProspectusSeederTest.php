<?php

use App\Models\InvestmentOpportunity;
use Database\Seeders\InvestmentProspectusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('sourced Mutoko prospectus opportunities are published once and visible publicly', function () {
    $this->seed(InvestmentProspectusSeeder::class);
    $this->seed(InvestmentProspectusSeeder::class);

    expect(InvestmentOpportunity::query()->count())->toBe(5)
        ->and(InvestmentOpportunity::public()->count())->toBe(5);

    $this->get('/en/investment')
        ->assertOk()
        ->assertSee('Mutoko weighbridge proposal')
        ->assertSee('Black granite cutting and polishing plant')
        ->assertSee('Fruit and vegetable processing plant')
        ->assertSee('Lithium processing plant')
        ->assertSee('Mutemwa and Mutoko Ruins tourism facilities');

    $this->get('/en/investment/mutoko-weighbridge-prospectus')
        ->assertOk()
        ->assertSee('Confirm current availability and terms with council');
});
