<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // The base users migration already creates the sessions table.
    }

    public function down(): void
    {
        // The sessions table belongs to the base users migration.
    }
};
