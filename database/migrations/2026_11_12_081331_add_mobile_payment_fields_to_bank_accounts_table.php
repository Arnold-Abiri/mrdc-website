<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table): void {
            $table->string('payment_type')->default('bank');
            $table->string('code_label')->nullable();
            $table->text('instructions')->nullable();
            $table->string('branch_name')->nullable()->change();
        });

        DB::table('bank_accounts')->insert([
            [
                'payment_type' => 'mobile',
                'bank_name' => 'EcoCash',
                'account_name' => 'EcoCash Biller Code Payment',
                'account_number' => '94812',
                'code_label' => 'Biller Code',
                'instructions' => "Dial *151# and enter your EcoCash PIN.\nSelect Make Payment → Pay Bill.\nEnter Council Biller Code: 94812.\nEnter Amount to pay (ZiG or USD equivalent).\nEnter Reference: Stand & Ward No (e.g. ST366-W18).",
                'currency' => 'Multi-Currency',
                'category' => 'rates',
                'display_order' => 5,
                'is_active' => false,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_type' => 'mobile',
                'bank_name' => 'NetOne OneMoney',
                'account_name' => 'NetOne OneMoney Merchant Code',
                'account_number' => '48920',
                'code_label' => 'Merchant Code',
                'instructions' => "Dial *111# on your NetOne line.\nSelect Pay Merchant.\nEnter Council Merchant Code: 48920.\nEnter Reference: Stand Number & Ratepayer Surname.\nConfirm transaction details with your OneMoney PIN.",
                'currency' => 'Multi-Currency',
                'category' => 'rates',
                'display_order' => 6,
                'is_active' => false,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('bank_accounts')->where('payment_type', 'mobile')->delete();

        DB::table('bank_accounts')->whereNull('branch_name')->update(['branch_name' => '']);

        Schema::table('bank_accounts', function (Blueprint $table): void {
            $table->string('branch_name')->nullable(false)->change();
            $table->dropColumn(['payment_type', 'code_label', 'instructions']);
        });
    }
};
