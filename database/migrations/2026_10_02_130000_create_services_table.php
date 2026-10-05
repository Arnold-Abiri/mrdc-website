<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('summary', 1000)->nullable();
            $table->longText('description')->nullable();
            $table->json('requirements')->nullable();
            $table->json('steps')->nullable();
            $table->text('fees_information')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('draft')->index();
            $table->string('verification_status', 20)->default('demo')->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
