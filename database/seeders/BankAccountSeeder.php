<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'bank_name' => 'CBZ Bank',
                'account_name' => 'Mutoko Rural District Council - General Revenue',
                'account_number' => '02923984010012',
                'branch_name' => 'Mutoko Branch',
                'branch_code' => '4129',
                'currency' => 'ZiG',
                'category' => 'rates',
                'purpose' => 'Assessment rates, monthly water consumption, and refuse collection levies.',
                'display_order' => 1,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'bank_name' => 'AFC Commercial Bank (Agribank)',
                'account_name' => 'Mutoko RDC - Development & Unit Tax',
                'account_number' => '100004829104',
                'branch_name' => 'Mutoko Branch',
                'branch_code' => '3104',
                'currency' => 'ZiG',
                'category' => 'rates',
                'purpose' => 'Rural unit tax, communal development levies, and agricultural plot assessments.',
                'display_order' => 2,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'bank_name' => 'ZB Bank',
                'account_name' => 'Mutoko RDC - Capital Projects & Housing Nostro',
                'account_number' => '4521-789320-200',
                'branch_name' => 'Mutoko Branch',
                'branch_code' => '5201',
                'currency' => 'USD / Nostro',
                'category' => 'stands',
                'purpose' => 'Serviced residential/commercial stand purchases, lease premiums, and infrastructure capital.',
                'display_order' => 3,
                'is_active' => true,
                'status' => 'published',
            ],
            [
                'bank_name' => 'CABS',
                'account_name' => 'Mutoko RDC - Business & Commercial Licensing',
                'account_number' => '1004928172',
                'branch_name' => 'Mutoko / Corporate Branch',
                'branch_code' => '2401',
                'currency' => 'Multi-Currency',
                'category' => 'licenses',
                'purpose' => 'Shop licenses, market stall fees, mining development levies, and health inspection permits.',
                'display_order' => 4,
                'is_active' => true,
                'status' => 'published',
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::query()->updateOrCreate(
                ['account_number' => $account['account_number']],
                $account
            );
        }
    }
}
