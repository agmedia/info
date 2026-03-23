<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_download_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('content_resource_documents')->nullOnDelete();
            $table->string('document_code', 120)->nullable()->index();
            $table->string('document_title', 191);
            $table->string('document_slug', 191)->nullable()->index();
            $table->string('document_group_code', 80)->nullable()->index();
            $table->text('document_download_url')->nullable();
            $table->string('name', 191);
            $table->string('email', 191)->index();
            $table->string('phone', 80)->nullable();
            $table->string('company', 191)->nullable();
            $table->string('status', 32)->default('new')->index();
            $table->string('locale', 12)->nullable()->index();
            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->json('payload')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_download_requests');
    }
};
