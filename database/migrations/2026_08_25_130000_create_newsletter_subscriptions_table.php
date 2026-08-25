<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('locale', 12)->default('hr')->index();
            $table->string('provider', 32)->default('mailchimp')->index();
            $table->string('status', 32)->default('pending')->index();
            $table->string('provider_member_id', 191)->nullable();
            $table->char('subscriber_hash', 32)->nullable()->index();
            $table->unsignedInteger('attempts')->default(1);
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('error_code', 120)->nullable();
            $table->text('error_message')->nullable();
            $table->char('ip_hash', 64)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriptions');
    }
};
