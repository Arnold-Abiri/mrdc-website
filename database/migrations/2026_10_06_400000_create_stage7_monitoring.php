<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type', 20)->index();
            $table->string('route', 120)->nullable()->index();
            $table->string('locale', 5)->nullable()->index();
            $table->string('subject_type', 40)->nullable()->index();
            $table->string('subject', 180)->nullable()->index();
            $table->string('visitor_key', 64)->index();
            $table->string('referrer_category', 20)->default('direct')->index();
            $table->string('referrer_domain', 120)->nullable();
            $table->timestamp('created_at')->index()->nullable();
        });

        Schema::create('error_events', function (Blueprint $table): void {
            $table->id();
            $table->string('exception_class', 255)->index();
            $table->string('summary', 500);
            $table->string('route', 180)->nullable()->index();
            $table->string('correlation_id', 40)->index();
            $table->boolean('resolved')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('incidents', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('started_at')->index();
            $table->timestamp('recovered_at')->nullable();
            $table->string('source', 40)->default('manual')->index();
            $table->string('status', 20)->default('open')->index();
            $table->string('summary', 500)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('error_events');
        Schema::dropIfExists('analytics_events');
    }
};
