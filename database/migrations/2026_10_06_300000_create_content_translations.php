<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_translations', function (Blueprint $table): void {
            $table->id();
            $table->string('translatable_type', 160);
            $table->unsignedBigInteger('translatable_id')->index();
            $table->string('locale', 5)->index();
            $table->string('field', 60);
            $table->text('value');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'content_translations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_translations');
    }
};
