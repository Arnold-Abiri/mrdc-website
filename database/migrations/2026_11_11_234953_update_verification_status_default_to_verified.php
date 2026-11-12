<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'pages',
            'documents',
            'services',
            'editorial_items',
            'wards',
            'officials',
            'public_contacts',
            'tenders',
            'vacancies',
            'investment_opportunities',
            'council_meetings',
            'council_projects',
            'galleries',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'verification_status')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('verification_status', 20)->default('verified')->change();
                });
            }
        }

        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'public_verification_status')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->string('public_verification_status', 20)->default('verified')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'pages',
            'documents',
            'services',
            'editorial_items',
            'wards',
            'officials',
            'public_contacts',
            'tenders',
            'vacancies',
            'investment_opportunities',
            'council_meetings',
            'council_projects',
            'galleries',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'verification_status')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('verification_status', 20)->default('demo')->change();
                });
            }
        }

        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'public_verification_status')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->string('public_verification_status', 20)->default('demo')->change();
            });
        }
    }
};
