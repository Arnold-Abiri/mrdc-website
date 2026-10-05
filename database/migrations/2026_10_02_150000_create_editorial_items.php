<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_items', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 12)->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('summary', 1000)->nullable();
            $table->longText('body');
            $table->string('category', 60)->nullable();
            $table->foreignId('featured_media_id')->nullable()->constrained('media')->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('draft')->index();
            $table->string('verification_status', 20)->default('demo')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->date('expires_at')->nullable()->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
        Schema::create('editorial_item_document', function (Blueprint $table): void {
            $table->foreignId('editorial_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained()->restrictOnDelete();
            $table->primary(['editorial_item_id', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_item_document');
        Schema::dropIfExists('editorial_items');
    }
};
