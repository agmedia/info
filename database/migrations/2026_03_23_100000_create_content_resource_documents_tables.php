<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_resource_documents', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->string('group_code', 80)->default('downloads')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->text('download_url')->nullable();
            $table->text('cover_image_url')->nullable();
            $table->text('source_url')->nullable();
            $table->json('payload')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('content_resource_document_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('content_resource_documents')->cascadeOnDelete();
            $table->string('locale', 12)->index();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'locale'], 'content_resource_document_locale_unique');
            $table->unique(['locale', 'slug'], 'content_resource_document_locale_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_resource_document_translations');
        Schema::dropIfExists('content_resource_documents');
    }
};
