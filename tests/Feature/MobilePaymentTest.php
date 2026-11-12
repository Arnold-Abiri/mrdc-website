<?php

use App\Filament\Resources\BankAccounts\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccount;
use App\Models\BankAccount;
use App\Models\User;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('imports the previous mobile payment instructions as inactive drafts', function (): void {
    expect(BankAccount::query()->where('payment_type', 'mobile')->count())->toBe(2);
    expect(BankAccount::query()->where('payment_type', 'mobile')->public()->count())->toBe(0);
    $this->get('/en/rates')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->has('mobilePayments', 0)->has('bankAccounts', 0));
});

it('allows administrators to create edit publish and withdraw mobile payment details', function (): void {
    $this->seed(SecuritySeeder::class);
    $administrator = User::factory()->create();
    $role = Role::findByName('System Administrator');
    $administrator->assignRole($role);
    DB::table('user_role_scopes')->insert([
        'user_id' => $administrator->id, 'role_id' => $role->id, 'scope_type' => 'global',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $this->actingAs($administrator);

    Livewire::test(CreateBankAccount::class)->fillForm([
        'payment_type' => 'mobile', 'bank_name' => 'Test Provider',
        'account_name' => 'Council Mobile Payments', 'account_number' => '00123',
        'code_label' => 'Merchant Code', 'instructions' => "Dial *123#.\nEnter code 00123.",
        'currency' => 'ZiG', 'category' => 'rates', 'display_order' => 1,
        'is_active' => true, 'status' => 'published',
    ])->call('create')->assertHasNoFormErrors();

    $payment = BankAccount::query()->where('account_number', '00123')->sole();
    $this->get('/en/rates')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->has('mobilePayments', 1)->has('bankAccounts', 0)
        ->where('mobilePayments.0.account_number', '00123')
        ->where('mobilePayments.0.instructions', "Dial *123#.\nEnter code 00123."));

    Livewire::test(EditBankAccount::class, ['record' => $payment->getRouteKey()])->fillForm([
        'account_number' => '00456', 'instructions' => 'Enter code 00456.',
    ])->call('save')->assertHasNoFormErrors();
    $this->get('/en/rates')->assertInertia(fn (AssertableInertia $page) => $page
        ->where('mobilePayments.0.account_number', '00456')
        ->where('mobilePayments.0.instructions', 'Enter code 00456.'));

    foreach ([['status' => 'draft', 'is_active' => true], ['status' => 'published', 'is_active' => false]] as $visibility) {
        Livewire::test(EditBankAccount::class, ['record' => $payment->getRouteKey()])
            ->fillForm($visibility)->call('save')->assertHasNoFormErrors();
        $this->get('/en/rates')->assertInertia(fn (AssertableInertia $page) => $page->has('mobilePayments', 0));
    }
});

it('prevents users without payment permissions from editing mobile payment details', function (): void {
    $this->actingAs(User::factory()->create());
    $payment = BankAccount::query()->where('payment_type', 'mobile')->firstOrFail();
    Livewire::test(EditBankAccount::class, ['record' => $payment->getRouteKey()])->assertNotFound();
});
