<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_job_openings', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->dateTime('published_at')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('content_job_opening_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_opening_id')->constrained('content_job_openings')->cascadeOnDelete();
            $table->string('locale', 12)->index();
            $table->string('title');
            $table->string('slug', 191);
            $table->string('locations', 500);
            $table->text('excerpt')->nullable();
            $table->longText('body_html');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['job_opening_id', 'locale'], 'content_job_opening_locale_unique');
            $table->unique(['locale', 'slug'], 'content_job_opening_locale_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_job_opening_translations');
        Schema::dropIfExists('content_job_openings');
    }
};
