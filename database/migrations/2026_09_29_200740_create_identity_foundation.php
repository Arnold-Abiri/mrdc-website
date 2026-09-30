<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 32)->unique();
            $table->text('description')->nullable();
            $table->string('status', 16)->default('active')->index();
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status', 16)->default('active')->index();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->foreignId('disabled_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('user_role_scopes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('scope_type', 16);
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
            $table->index(['scope_type', 'department_id']);
        });

        Schema::create('audit_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80)->index();
            $table->string('subject_type', 160);
            $table->string('subject_id', 64);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_role_scopes ADD CONSTRAINT user_role_scopes_valid CHECK ((scope_type = 'department' AND department_id IS NOT NULL) OR (scope_type IN ('global', 'own') AND department_id IS NULL))");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_valid CHECK (status IN ('active', 'disabled'))");
            DB::statement("ALTER TABLE departments ADD CONSTRAINT departments_status_valid CHECK (status IN ('active', 'inactive'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('user_role_scopes');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('disabled_by');
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['status', 'last_login_at', 'disabled_at']);
        });
        Schema::dropIfExists('departments');
    }
};
