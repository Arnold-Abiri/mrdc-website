<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['key' => 'finance_email', 'label' => 'Finance payment enquiries email', 'type' => 'email'],
            ['key' => 'finance_whatsapp_number', 'label' => 'Finance payment enquiries WhatsApp number', 'type' => 'phone'],
        ] as $setting) {
            DB::table('site_settings')->insertOrIgnore([
                ...$setting, 'group' => 'Finance', 'value' => '',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        SiteSetting::forgetCache();
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', ['finance_email', 'finance_whatsapp_number'])->delete();
        SiteSetting::forgetCache();
    }
};
