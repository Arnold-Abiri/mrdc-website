<?php

use App\Models\BankAccount;
use App\Models\Department;
use App\Models\Faq;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\Ward;
use App\Services\Ai\CitizenKnowledgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('only exposes current published billing FAQs and configured payment contacts', function (): void {
    $faq = Faq::query()->create([
        'question' => 'Approved billing question', 'answer' => 'Approved answer',
        'category' => 'billing', 'is_published' => true,
    ]);
    SiteSetting::query()->where('key', 'finance_email')->firstOrFail()->update(['value' => 'payments@example.com']);
    SiteSetting::query()->where('key', 'finance_whatsapp_number')->firstOrFail()->update(['value' => '0771234567']);

    $this->get('/en/rates')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->has('billingFaqs', 1)->where('billingFaqs.0.question', 'Approved billing question')
        ->where('paymentContacts.email', 'payments@example.com')
        ->where('paymentContacts.whatsapp_url', 'https://wa.me/263771234567'));

    $faq->update(['is_published' => false]);
    SiteSetting::query()->where('key', 'finance_email')->firstOrFail()->update(['value' => '']);
    SiteSetting::query()->where('key', 'finance_whatsapp_number')->firstOrFail()->update(['value' => '']);
    $this->get('/en/rates')->assertInertia(fn (AssertableInertia $page) => $page
        ->has('billingFaqs', 0)->where('paymentContacts.email', null)->where('paymentContacts.whatsapp_url', null));
});

it('exposes tourism CMS content only while the page is published', function (): void {
    $page = new Page([
        'slug' => 'tourism-mutoko', 'title' => 'Council Tourism Guide', 'summary' => 'Approved tourism summary',
        'blocks' => [['type' => 'paragraph', 'text' => 'Approved attraction information']],
    ]);
    $page->status = 'published';
    $page->published_at = now()->subMinute();
    $page->save();
    $this->get('/en/tourism')->assertOk()->assertInertia(fn (AssertableInertia $view) => $view
        ->where('page.title', 'Council Tourism Guide')->where('page.blocks.0.text', 'Approved attraction information'));
    $page->status = 'draft';
    $page->save();
    $this->get('/en/tourism')->assertNotFound();
});

it('reports the published ward count without substituting 29', function (): void {
    $this->get('/en/about')->assertInertia(fn (AssertableInertia $page) => $page->where('about.ward_count', 0));
    Ward::query()->create([
        'slug' => 'ward-one', 'name' => 'Ward One', 'status' => 'published',
        'verification_status' => 'publishable', 'published_at' => now()->subMinute(),
    ]);
    $this->get('/en/about')->assertInertia(fn (AssertableInertia $page) => $page->where('about.ward_count', 1));
});

it('grounds chatbot contacts in current settings and removes withdrawn content immediately', function (): void {
    SiteSetting::query()->whereIn('key', ['phone', 'email', 'physical_address', 'office_hours'])->update(['value' => '']);
    SiteSetting::forgetCache();
    $service = app(CitizenKnowledgeService::class);
    expect($service->getCouncilContext())->not->toContain('OFFICIAL CONTACT & LOCATION', '+263 77 000 0000', 'info@mutokordc.gov.zw', '08:00');

    SiteSetting::query()->where('key', 'phone')->firstOrFail()->update(['value' => '+263771111111']);
    $account = BankAccount::query()->create([
        'bank_name' => 'Approved Bank', 'account_name' => 'Council', 'account_number' => '01234',
        'branch_name' => 'Main', 'status' => 'published', 'is_active' => true,
    ]);
    Department::factory()->create(['public_name' => 'Draft Department']);
    expect($service->getCouncilContext())->toContain('Official Phone: +263771111111', 'Approved Bank')
        ->not->toContain('Draft Department');

    $account->update(['status' => 'draft']);
    SiteSetting::query()->where('key', 'phone')->firstOrFail()->update(['value' => '']);
    expect($service->getCouncilContext())->not->toContain('Approved Bank', '+263771111111');
});
