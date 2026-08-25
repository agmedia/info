<?php

namespace Tests\Unit\Models;

use App\Models\Content\Support\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_and_locale_are_normalized_and_operational_fields_are_cast(): void
    {
        $subscription = NewsletterSubscription::query()->create([
            'email' => '  User@Example.COM ',
            'locale' => ' EN ',
            'provider' => 'mailchimp',
            'status' => NewsletterSubscription::STATUS_PENDING,
            'attempts' => 2,
            'last_attempt_at' => now(),
            'payload' => ['source' => 'footer'],
        ]);

        $subscription->refresh();

        $this->assertSame('user@example.com', $subscription->email);
        $this->assertSame('en', $subscription->locale);
        $this->assertSame(2, $subscription->attempts);
        $this->assertNotNull($subscription->last_attempt_at);
        $this->assertSame(['source' => 'footer'], $subscription->payload);
    }
}
