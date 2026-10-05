<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officials', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('biography')->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('display_order')->default(0);
            $table->string('status', 20)->default('draft')->index();
            $table->string('verification_status', 20)->default('demo')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
