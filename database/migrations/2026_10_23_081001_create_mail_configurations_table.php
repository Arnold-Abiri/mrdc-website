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
        Schema::create('mail_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default SMTP');
            $table->string('provider', 50)->default('custom'); // gmail, cpanel, microsoft365, custom
            $table->string('host');
            $table->unsignedInteger('port')->default(587);
            $table->string('encryption', 20)->default('tls'); // tls, ssl, none
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->string('from_name');
            $table->string('from_address');
            $table->string('reply_to_address')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->boolean('mail_enabled')->default(true);
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_tested_status', 30)->nullable(); // success, failed
            $table->text('last_tested_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_configurations');
    }
};
