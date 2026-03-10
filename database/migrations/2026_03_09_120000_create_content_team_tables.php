<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_team_members', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('email', 190)->nullable()->index();
            $table->string('facebook_url', 2048)->nullable();
            $table->string('twitter_url', 2048)->nullable();
            $table->string('linkedin_url', 2048)->nullable();
            $table->json('payload')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('content_team_member_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_member_id')->constrained('content_team_members')->cascadeOnDelete();
            $table->string('locale', 12)->index();
            $table->string('name');
            $table->string('position')->nullable();
            $table->text('departments')->nullable();
            $table->longText('description_html')->nullable();
            $table->timestamps();

            $table->unique(['team_member_id', 'locale'], 'content_team_member_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_team_member_translations');
        Schema::dropIfExists('content_team_members');
    }
};
