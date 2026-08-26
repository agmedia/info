<?php

namespace Tests\Feature\Front;

use App\Models\Content\Support\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ContactSpamProtectionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('contact:ip-minute:'.hash('sha256', '203.0.113.10'));
        RateLimiter::clear('contact:ip-hour:'.hash('sha256', '203.0.113.10'));
    }

    public function test_contact_form_renders_the_honeypot(): void
    {
        $this->get('/kontakt')
            ->assertOk()
            ->assertSee('class="front-form-honeypot"', false)
            ->assertSee('name="website"', false);
    }

    public function test_filled_honeypot_is_acknowledged_without_storage_or_notification(): void
    {
        Mail::fake();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->postJson('/kontakt', [
                'name' => 'Automated Visitor',
                'email' => 'bot@example.test',
                'message' => 'Automated contact form submission.',
                'website' => 'https://spam.example',
            ])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => __('contact.sent_status'),
            ]);

        $this->assertDatabaseCount((new ContactMessage)->getTable(), 0);
        Mail::assertNothingSent();
    }

    public function test_contact_submissions_are_rate_limited_by_ip(): void
    {
        $ip = '203.0.113.10';

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->postJson('/kontakt', $this->validPayload($attempt))
                ->assertOk();
        }

        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/kontakt', $this->validPayload(6))
            ->assertStatus(429)
            ->assertExactJson([
                'ok' => false,
                'message' => __('contact.rate_limited'),
            ]);

        $this->assertDatabaseCount((new ContactMessage)->getTable(), 5);
        $this->assertDatabaseMissing((new ContactMessage)->getTable(), [
            'email' => 'sender6@example.test',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function validPayload(int $attempt): array
    {
        return [
            'name' => 'Sender '.$attempt,
            'email' => 'sender'.$attempt.'@example.test',
            'message' => 'Legitimate inquiry number '.$attempt.' for rate-limit testing.',
            'accept_terms' => '1',
        ];
    }
}
