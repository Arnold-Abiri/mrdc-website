<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pages', 'verified_at')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->timestamp('verified_at')->nullable()->after('published_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pages', 'verified_at')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->dropColumn('verified_at');
            });
        }
    }
};
