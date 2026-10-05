<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_contacts', function (Blueprint $table): void {
            $table->id();
            $table->string('office');
            $table->string('type', 20);
            $table->text('value');
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->boolean('is_public')->default(false)->index();
            $table->string('status', 20)->default('draft')->index();
            $table->string('verification_status', 20)->default('demo')->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_contacts');
    }
};
