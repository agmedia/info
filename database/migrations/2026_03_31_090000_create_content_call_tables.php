<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_call_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->json('payload')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('content_call_post_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained('content_call_posts')->cascadeOnDelete();
            $table->string('locale', 12)->index();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('body_html')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'locale'], 'content_call_post_locale_unique');
            $table->unique(['locale', 'slug'], 'content_call_post_locale_slug_unique');
        });

        Schema::create('content_call_post_category', function (Blueprint $table): void {
            $table->foreignId('post_id')->constrained('content_call_posts')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();

            $table->primary(['post_id', 'category_id']);
            $table->index(['category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_call_post_category');
        Schema::dropIfExists('content_call_post_translations');
        Schema::dropIfExists('content_call_posts');
    }
};
