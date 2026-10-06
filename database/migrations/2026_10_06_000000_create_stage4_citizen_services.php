<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 60)->nullable();
            $table->text('description');
            $table->date('opens_at')->nullable();
            $table->dateTime('closes_at')->nullable();
            $table->string('lifecycle_status', 20)->default('upcoming')->index();
            $table->text('contact_instructions')->nullable();
            $table->foreignId('document_id')->nullable()->constrained()->restrictOnDelete();
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

        Schema::create('vacancies', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('grade', 60)->nullable();
            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->text('application_instructions')->nullable();
            $table->foreignId('document_id')->nullable()->constrained()->restrictOnDelete();
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

        Schema::create('investment_opportunities', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('sector', 60)->nullable();
            $table->string('summary', 1000)->nullable();
            $table->longText('description');
            $table->string('location', 255)->nullable();
            $table->string('opportunity_status', 20)->default('open')->index();
            $table->foreignId('document_id')->nullable()->constrained()->restrictOnDelete();
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

        Schema::create('district_statistics', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('value', 60);
            $table->string('unit', 60)->nullable();
            $table->string('icon', 60)->nullable();
            $table->string('source_note', 255)->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_statistics');
        Schema::dropIfExists('investment_opportunities');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('tenders');
    }
};
