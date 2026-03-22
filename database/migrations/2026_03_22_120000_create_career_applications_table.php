<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name', 80);
            $table->string('last_name', 120);
            $table->string('email', 191)->index();
            $table->text('message')->nullable();
            $table->string('cv_path', 255);
            $table->string('cv_disk', 32)->default('local');
            $table->string('cv_original_name', 255);
            $table->string('cv_mime_type', 191)->nullable();
            $table->unsignedBigInteger('cv_size')->nullable();
            $table->string('status', 32)->default('new')->index();
            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_applications');
    }
};
