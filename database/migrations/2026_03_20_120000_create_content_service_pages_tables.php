<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_service_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->string('template_key', 80)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->json('payload')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('content_service_page_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_page_id')->constrained('content_service_pages')->cascadeOnDelete();
            $table->string('locale', 12)->index();
            $table->string('title');
            $table->string('slug');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['service_page_id', 'locale'], 'content_service_page_locale_unique');
            $table->unique(['locale', 'slug'], 'content_service_page_locale_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_service_page_translations');
        Schema::dropIfExists('content_service_pages');
    }
};
