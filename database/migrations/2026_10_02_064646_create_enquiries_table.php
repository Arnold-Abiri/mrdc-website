<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name', 160);
            $table->string('email', 254);
            $table->string('phone', 40)->nullable();
            $table->string('category', 40);
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('subject', 200);
            $table->text('message');
            $table->string('status', 20)->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            $table->index(['department_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
