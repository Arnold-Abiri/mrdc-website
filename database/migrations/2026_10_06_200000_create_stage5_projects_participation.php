<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->string('award_status', 20)->default('none')->index();
            $table->string('awarded_to', 255)->nullable();
            $table->date('awarded_at')->nullable();
            $table->decimal('award_amount', 14, 2)->nullable();
            $table->string('award_reference', 60)->nullable();
            $table->foreignId('award_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->text('award_remarks')->nullable();
        });

        Schema::table('vacancies', function (Blueprint $table): void {
            $table->string('reference', 60)->nullable()->unique();
            $table->string('employment_type', 40)->nullable();
        });

        Schema::table('enquiries', function (Blueprint $table): void {
            $table->string('context_type', 20)->nullable()->index();
            $table->string('context_reference', 160)->nullable();
            $table->string('organisation', 255)->nullable();
            $table->boolean('consent_given')->default(false);
        });

        Schema::create('council_projects', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('project_type', 40)->default('project')->index();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('location', 255)->nullable();
            $table->string('summary', 1000)->nullable();
            $table->longText('description');
            $table->date('starts_at')->nullable();
            $table->date('expected_completed_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('project_status', 20)->default('planned')->index();
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->foreignId('featured_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->text('contact_instructions')->nullable();
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

        Schema::create('council_project_ward', function (Blueprint $table): void {
            $table->foreignId('council_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ward_id')->constrained()->restrictOnDelete();
            $table->primary(['council_project_id', 'ward_id']);
        });

        Schema::create('council_project_document', function (Blueprint $table): void {
            $table->foreignId('council_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained()->restrictOnDelete();
            $table->primary(['council_project_id', 'document_id']);
        });

        Schema::create('project_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('council_project_id')->constrained()->cascadeOnDelete();
            $table->date('update_date')->index();
            $table->string('title');
            $table->string('summary', 1000)->nullable();
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_updates');
        Schema::dropIfExists('council_project_document');
        Schema::dropIfExists('council_project_ward');
        Schema::dropIfExists('council_projects');
        Schema::table('enquiries', function (Blueprint $table): void {
            $table->dropColumn(['context_type', 'context_reference', 'organisation', 'consent_given']);
        });
        Schema::table('vacancies', function (Blueprint $table): void {
            $table->dropColumn(['reference', 'employment_type']);
        });
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropColumn(['award_status', 'awarded_to', 'awarded_at', 'award_amount', 'award_reference', 'award_document_id', 'award_remarks']);
        });
    }
};
