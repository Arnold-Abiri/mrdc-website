<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->foreignId('media_id')->constrained('media')->restrictOnDelete();
            $table->foreignId('replaced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replaced_at')->nullable();
            $table->timestamps();
            $table->unique(['document_id', 'version_number']);
        });

        Schema::table('documents', function (Blueprint $table): void {
            $table->unsignedInteger('current_version')->default(1);
            $table->unsignedBigInteger('download_count')->default(0);
        });

        Schema::create('document_downloads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->timestamp('downloaded_at')->index();
        });

        Schema::create('council_meetings', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('meeting_type', 40)->default('full_council')->index();
            $table->date('scheduled_date')->index();
            $table->string('scheduled_time', 20)->nullable();
            $table->string('venue', 255)->nullable();
            $table->string('meeting_status', 20)->default('scheduled')->index();
            $table->text('summary')->nullable();
            $table->foreignId('agenda_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('minutes_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('draft')->index();
            $table->string('verification_status', 20)->default('demo')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_slides', function (Blueprint $table): void {
            $table->id();
            $table->string('headline');
            $table->string('supporting_text', 1000)->nullable();
            $table->string('cta_label', 120)->nullable();
            $table->string('cta_url', 2048)->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('editorial_items', function (Blueprint $table): void {
            $table->boolean('is_urgent')->default(false)->index();
            $table->string('seo_title', 255)->nullable();
            $table->string('meta_description', 320)->nullable();
        });

        Schema::table('officials', function (Blueprint $table): void {
            $table->boolean('is_department_head')->default(false)->index();
        });

        Schema::table('wards', function (Blueprint $table): void {
            $table->string('map_url', 2048)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wards', function (Blueprint $table): void {
            $table->dropColumn('map_url');
        });
        Schema::table('officials', function (Blueprint $table): void {
            $table->dropColumn('is_department_head');
        });
        Schema::table('editorial_items', function (Blueprint $table): void {
            $table->dropColumn(['is_urgent', 'seo_title', 'meta_description']);
        });
        Schema::dropIfExists('homepage_slides');
        Schema::dropIfExists('council_meetings');
        Schema::dropIfExists('document_downloads');
        Schema::table('documents', function (Blueprint $table): void {
            $table->dropColumn(['current_version', 'download_count']);
        });
        Schema::dropIfExists('document_versions');
    }
};
