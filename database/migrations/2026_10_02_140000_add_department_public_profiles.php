<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table): void {
            $table->string('public_name')->nullable();
            $table->string('public_summary', 1000)->nullable();
            $table->longText('public_description')->nullable();
            $table->json('responsibilities')->nullable();
            $table->string('public_status', 20)->default('draft')->index();
            $table->string('public_verification_status', 20)->default('demo')->index();
            $table->unsignedInteger('public_display_order')->default(0);
            $table->timestamp('public_published_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table): void {
            $table->dropColumn(['public_name', 'public_summary', 'public_description', 'responsibilities', 'public_status', 'public_verification_status', 'public_display_order', 'public_published_at']);
        });
    }
};
