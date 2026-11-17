<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('site_settings')->insertOrIgnore([
            'key' => 'resident_portal_url',
            'label' => 'Resident Portal URL',
            'group' => 'General',
            'type' => 'url',
            'value' => 'https://mutokordc.co.zw/portal/',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SiteSetting::forgetCache();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->where('key', 'resident_portal_url')->delete();
        SiteSetting::forgetCache();
    }
};
