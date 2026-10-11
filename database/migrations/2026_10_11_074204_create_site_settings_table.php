<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<int, array{key: string, label: string, group: string, type: string, value: string|null}>
     */
    private const DEFAULTS = [
        ['key' => 'whatsapp_number', 'label' => 'WhatsApp council desk number', 'group' => 'Contact', 'type' => 'phone', 'value' => ''],
        ['key' => 'phone', 'label' => 'Public phone number', 'group' => 'Contact', 'type' => 'phone', 'value' => ''],
        ['key' => 'email', 'label' => 'Public email address', 'group' => 'Contact', 'type' => 'email', 'value' => ''],
        ['key' => 'recruitment_email', 'label' => 'Recruitment & careers email', 'group' => 'Contact', 'type' => 'email', 'value' => 'recruitment@mutokordc.co.zw'],
        ['key' => 'physical_address', 'label' => 'Physical address', 'group' => 'Contact', 'type' => 'textarea', 'value' => "Mutoko Rural District Council\nMutoko, Mashonaland East\nZimbabwe"],
        ['key' => 'office_hours', 'label' => 'Office hours', 'group' => 'Contact', 'type' => 'text', 'value' => ''],
        ['key' => 'facebook_url', 'label' => 'Facebook URL', 'group' => 'Social', 'type' => 'url', 'value' => ''],
        ['key' => 'x_url', 'label' => 'X (Twitter) URL', 'group' => 'Social', 'type' => 'url', 'value' => ''],
        ['key' => 'youtube_url', 'label' => 'YouTube URL', 'group' => 'Social', 'type' => 'url', 'value' => ''],
    ];

    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('group')->default('General');
            $table->string('type')->default('text');
            $table->text('value')->nullable();
            $table->timestamps();
        });

        foreach (self::DEFAULTS as $row) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $row['key']],
                [...$row, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
