<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_glossary_terms', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->string('collection_code', 80)->default('svijet-financija')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->json('payload')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('content_glossary_term_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('term_id')->constrained('content_glossary_terms')->cascadeOnDelete();
            $table->string('locale', 12)->index();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('body_html')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['term_id', 'locale'], 'content_glossary_term_locale_unique');
            $table->unique(['locale', 'slug'], 'content_glossary_term_locale_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_glossary_term_translations');
        Schema::dropIfExists('content_glossary_terms');
    }
};
