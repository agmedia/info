<?php

namespace Tests\Feature\Front;

use App\Models\Content\Support\NewsletterSubscription;
use App\Models\Settings\Local\Language;
use App\Services\Front\NavigationMenuService;
use App\Services\Newsletter\MailchimpCredentialCodec;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NewsletterSubscriptionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Language::query()->create([
            'code' => 'hr',
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->create([
            'code' => 'en',
            'locale' => 'en_GB',
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_footer_signup_is_saved_locally_and_sent_to_the_configured_mailchimp_audience(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response([
                'id' => md5('person@example.com'),
                'email_address' => 'person@example.com',
                'status' => 'pending',
            ]),
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])
            ->postJson(route('newsletter.subscribe'), [
                'email' => '  Person@Example.COM ',
                'website' => '',
                'consent' => '1',
            ])
            ->assertOk()
            ->assertExactJson([
                'ok' => true,
                'message' => __('newsletter.success'),
            ]);

        $subscription = NewsletterSubscription::query()->sole();
        $this->assertSame('person@example.com', $subscription->email);
        $this->assertSame('hr', $subscription->locale);
        $this->assertSame('mailchimp', $subscription->provider);
        $this->assertSame(NewsletterSubscription::STATUS_CONFIRMATION_PENDING, $subscription->status);
        $this->assertSame(md5('person@example.com'), $subscription->subscriber_hash);
        $this->assertSame(1, $subscription->attempts);
        $this->assertSame(
            hash_hmac('sha256', '203.0.113.42', (string) config('app.key')),
            $subscription->ip_hash,
        );
        $this->assertTrue((bool) data_get($subscription->payload, 'consent'));
        $this->assertNotEmpty(data_get($subscription->payload, 'consent_recorded_at'));
        $this->assertNull($subscription->error_code);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'PUT'
                && $request->url() === 'https://us21.api.mailchimp.com/3.0/lists/audience123/members/'.md5('person@example.com')
                && $request->hasHeader(
                    'Authorization',
                    'Basic '.base64_encode('alphacapitalis-newsletter:super-secret-us21'),
                )
                && $request['email_address'] === 'person@example.com'
                && $request['status_if_new'] === 'pending'
                && $request['language'] === 'hr'
                && ! isset($request['status']);
        });
    }

    public function test_footer_renders_localized_post_routes_and_never_exposes_the_mailchimp_key(): void
    {
        $apiKey = 'testmailchimpapikey00000000000001-us21';
        app(SystemSettingsService::class)->putMany([
            NavigationMenuService::CHROME_SETTINGS_KEY => [
                'hr' => [
                    'footer_newsletter_label' => 'Newsletter',
                    'footer_newsletter_title' => 'Primajte važne novosti na',
                    'footer_newsletter_accent' => 'vrijeme.',
                    'footer_newsletter_email_placeholder' => 'Vaša email adresa',
                    'footer_newsletter_submit_label' => 'Prijavi se',
                    'footer_newsletter_consent' => 'Želim primati newsletter i prihvaćam obradu podataka u tu svrhu.',
                ],
                'en' => [
                    'footer_newsletter_label' => 'Newsletter',
                    'footer_newsletter_title' => 'Receive important updates',
                    'footer_newsletter_accent' => 'on time.',
                    'footer_newsletter_email_placeholder' => 'Your email address',
                    'footer_newsletter_submit_label' => 'Subscribe',
                    'footer_newsletter_consent' => 'I want to receive the newsletter and consent to data processing for this purpose.',
                ],
            ],
            'store_newsletter_provider' => 'mailchimp',
            'store_newsletter_mailchimp_server_prefix' => 'us21',
            'store_newsletter_mailchimp_api_key' => app(MailchimpCredentialCodec::class)->encode($apiKey),
            'store_newsletter_mailchimp_list_id' => 'audience123',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('action="'.route('newsletter.subscribe').'"', false)
            ->assertSee('data-csrf-refresh-url="'.route('newsletter.csrf-token', [], false).'"', false)
            ->assertSee('method="post"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="consent"', false)
            ->assertSee('name="website"', false)
            ->assertSee('Želim primati newsletter')
            ->assertDontSee($apiKey, false)
            ->assertDontSee(MailchimpCredentialCodec::PREFIX, false);

        $this->withSession(['front_locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('action="'.route('newsletter.subscribe.en').'"', false)
            ->assertSee('Your email address')
            ->assertSee('I want to receive the newsletter')
            ->assertDontSee($apiKey, false);
    }

    public function test_ajax_signup_can_refresh_a_stale_csrf_token_and_retry_in_the_same_session(): void
    {
        $this->app['env'] = 'production';
        app(SystemSettingsService::class)->put('store_newsletter_provider', 'none');
        Http::preventStrayRequests();

        $initial = $this->get(route('newsletter.csrf-token'))
            ->assertOk()
            ->assertJsonStructure(['token']);

        $sessionCookieName = (string) config('session.cookie');
        $sessionCookie = collect($initial->headers->getCookies())
            ->first(static fn ($cookie): bool => $cookie->getName() === $sessionCookieName);

        $this->assertNotNull($sessionCookie);

        $this->withCredentials()
            ->withUnencryptedCookie($sessionCookieName, $sessionCookie->getValue())
            ->postJson(route('newsletter.subscribe'), [
                '_token' => 'stale-token',
                'email' => 'refreshed@example.com',
                'consent' => '1',
            ])
            ->assertStatus(419);

        $this->assertDatabaseCount('newsletter_subscriptions', 0);

        $refresh = $this->get(route('newsletter.csrf-token'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonStructure(['token']);

        $refreshedToken = $refresh->json('token');
        $this->assertIsString($refreshedToken);
        $this->assertNotSame('', $refreshedToken);

        $this->postJson(route('newsletter.subscribe'), [
            '_token' => $refreshedToken,
            'email' => 'refreshed@example.com',
            'consent' => '1',
        ])
            ->assertOk()
            ->assertExactJson([
                'ok' => true,
                'message' => __('newsletter.received'),
            ]);

        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'refreshed@example.com',
            'status' => NewsletterSubscription::STATUS_RECEIVED,
        ]);
        Http::assertNothingSent();
    }

    public function test_repeated_signup_updates_one_local_record_and_does_not_duplicate_the_member(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::sequence()
                ->push(['id' => md5('repeat@example.com'), 'status' => 'pending'])
                ->push(['id' => md5('repeat@example.com'), 'status' => 'subscribed']),
        ]);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'repeat@example.com', 'consent' => '1'])->assertOk();
        $this->postJson(route('newsletter.subscribe'), ['email' => 'REPEAT@example.com', 'consent' => '1'])->assertOk();

        $this->assertSame(1, NewsletterSubscription::query()->count());
        $subscription = NewsletterSubscription::query()->sole();
        $this->assertSame(2, $subscription->attempts);
        $this->assertSame(NewsletterSubscription::STATUS_SUBSCRIBED, $subscription->status);
        $this->assertNotNull($subscription->subscribed_at);
        Http::assertSentCount(2);
    }

    public function test_an_unsubscribed_mailchimp_member_gets_a_new_confirmation_request(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::sequence()
                ->push(['id' => md5('returning@example.com'), 'status' => 'unsubscribed'])
                ->push(['id' => md5('returning@example.com'), 'status' => 'pending']),
        ]);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'returning@example.com', 'consent' => '1'])
            ->assertOk();

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request): bool => ($request['status'] ?? null) === 'pending');
        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'returning@example.com',
            'status' => NewsletterSubscription::STATUS_CONFIRMATION_PENDING,
        ]);
    }

    public function test_a_transactional_mailchimp_member_gets_a_double_opt_in_confirmation_request(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::sequence()
                ->push(['id' => md5('transactional@example.com'), 'status' => 'transactional'])
                ->push(['id' => md5('transactional@example.com'), 'status' => 'pending']),
        ]);

        $this->postJson(route('newsletter.subscribe'), [
            'email' => 'transactional@example.com',
            'consent' => '1',
        ])->assertOk();

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request): bool => ($request['status'] ?? null) === 'pending'
            && ($request['language'] ?? null) === 'hr');
        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'transactional@example.com',
            'status' => NewsletterSubscription::STATUS_CONFIRMATION_PENDING,
        ]);
    }

    public function test_a_cleaned_mailchimp_member_is_not_forcibly_resubscribed(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response([
                'id' => md5('cleaned@example.com'),
                'status' => 'cleaned',
            ]),
        ]);

        $this->postJson(route('newsletter.subscribe'), [
            'email' => 'cleaned@example.com',
            'consent' => '1',
        ])->assertStatus(503)->assertJson(['ok' => false]);

        Http::assertSentCount(1);
        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'cleaned@example.com',
            'status' => NewsletterSubscription::STATUS_FAILED,
            'error_code' => 'unexpected_provider_status',
        ]);
    }

    public function test_provider_configuration_failure_is_saved_but_secrets_and_email_are_not_logged_or_stored_in_the_error(): void
    {
        Log::spy();
        app(SystemSettingsService::class)->putMany([
            'store_newsletter_provider' => 'mailchimp',
            'store_newsletter_mailchimp_server_prefix' => 'us21',
            'store_newsletter_mailchimp_api_key' => 'secret-without-server-suffix',
            'store_newsletter_mailchimp_list_id' => 'audience123',
        ]);
        Http::preventStrayRequests();

        $this->postJson(route('newsletter.subscribe'), ['email' => 'private@example.com', 'consent' => '1'])
            ->assertStatus(503)
            ->assertExactJson([
                'ok' => false,
                'message' => __('newsletter.unavailable'),
            ]);

        $subscription = NewsletterSubscription::query()->sole();
        $this->assertSame(NewsletterSubscription::STATUS_FAILED, $subscription->status);
        $this->assertSame('configuration_missing', $subscription->error_code);
        $this->assertStringNotContainsString('private@example.com', (string) $subscription->error_message);
        $this->assertStringNotContainsString('secret-without-server-suffix', (string) $subscription->error_message);
        Http::assertNothingSent();

        Log::shouldHaveReceived('warning')->once()->withArgs(function (string $message, array $context): bool {
            return $message === 'Newsletter subscription synchronization failed.'
                && ! str_contains(json_encode($context, JSON_THROW_ON_ERROR), 'private@example.com')
                && ! str_contains(json_encode($context, JSON_THROW_ON_ERROR), 'secret-without-server-suffix');
        });
    }

    public function test_signup_is_recorded_successfully_when_an_external_provider_is_disabled(): void
    {
        app(SystemSettingsService::class)->put('store_newsletter_provider', 'none');
        Http::preventStrayRequests();

        $this->postJson(route('newsletter.subscribe'), [
            'email' => 'local@example.com',
            'consent' => '1',
        ])->assertOk()->assertExactJson([
            'ok' => true,
            'message' => __('newsletter.received'),
        ]);

        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'local@example.com',
            'provider' => 'none',
            'status' => NewsletterSubscription::STATUS_RECEIVED,
        ]);
        Http::assertNothingSent();
    }

    public function test_mailchimp_error_details_are_sanitized_before_local_storage(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response([
                'title' => 'Invalid Resource',
                'detail' => 'bad@example.com failed with super-secret-us21 for audience123',
            ], 400),
        ]);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'bad@example.com', 'consent' => '1'])
            ->assertStatus(503);

        $subscription = NewsletterSubscription::query()->sole();
        $this->assertSame('invalid_resource', $subscription->error_code);
        $this->assertStringNotContainsString('bad@example.com', (string) $subscription->error_message);
        $this->assertStringNotContainsString('super-secret-us21', (string) $subscription->error_message);
        $this->assertStringNotContainsString('audience123', (string) $subscription->error_message);
        $this->assertStringContainsString('[redacted]', (string) $subscription->error_message);
    }

    public function test_honeypot_is_acknowledged_without_storage_or_provider_request(): void
    {
        $this->configureMailchimp();
        Http::preventStrayRequests();

        $this->postJson(route('newsletter.subscribe'), [
            'email' => 'bot@example.com',
            'website' => 'https://spam.example',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseCount('newsletter_subscriptions', 0);
        Http::assertNothingSent();
    }

    public function test_explicit_consent_is_required_before_a_signup_is_saved(): void
    {
        $this->configureMailchimp();
        Http::preventStrayRequests();

        $this->postJson(route('newsletter.subscribe'), ['email' => 'no-consent@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('consent')
            ->assertJsonPath('errors.consent.0', __('newsletter.validation.consent_required'));

        $this->assertDatabaseCount('newsletter_subscriptions', 0);
        Http::assertNothingSent();
    }

    public function test_english_route_returns_english_responses_and_records_the_english_locale(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response([
                'id' => md5('english@example.com'),
                'status' => 'pending',
            ]),
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->postJson(route('newsletter.subscribe.en'), ['email' => 'english@example.com', 'consent' => '1'])
            ->assertOk()
            ->assertExactJson([
                'ok' => true,
                'message' => 'Thank you! Check your email and confirm your newsletter subscription.',
            ]);

        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'english@example.com',
            'locale' => 'en',
        ]);
        Http::assertSent(fn (Request $request): bool => ($request['language'] ?? null) === 'en');
    }

    public function test_non_javascript_signup_redirects_back_with_a_status_message(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response([
                'id' => md5('redirect@example.com'),
                'status' => 'pending',
            ]),
        ]);

        $this->from('/')->post(route('newsletter.subscribe'), ['email' => 'redirect@example.com', 'consent' => '1'])
            ->assertRedirect('/')
            ->assertSessionHas('newsletter_success', __('newsletter.success'));

        $this->from('/')->post(route('newsletter.subscribe'), ['email' => 'not-an-email', 'consent' => '1'])
            ->assertRedirect('/')
            ->assertSessionHas('newsletter_error', __('newsletter.validation.email_invalid'))
            ->assertSessionHasErrors('email');
    }

    public function test_footer_signup_is_rate_limited_by_ip(): void
    {
        $this->configureMailchimp();
        Http::fake([
            'https://us21.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response([
                'id' => 'member-id',
                'status' => 'pending',
            ]),
        ]);

        foreach (range(1, 10) as $index) {
            $this->postJson(route('newsletter.subscribe'), [
                'email' => "person{$index}@example.com",
                'consent' => '1',
            ])->assertOk();
        }

        $this->postJson(route('newsletter.subscribe'), ['email' => 'blocked@example.com', 'consent' => '1'])
            ->assertStatus(429)
            ->assertExactJson([
                'ok' => false,
                'message' => __('newsletter.rate_limited'),
            ]);

        $this->assertDatabaseMissing('newsletter_subscriptions', ['email' => 'blocked@example.com']);
        Http::assertSentCount(10);
    }

    private function configureMailchimp(): void
    {
        app(SystemSettingsService::class)->putMany([
            'store_newsletter_provider' => 'mailchimp',
            'store_newsletter_mailchimp_server_prefix' => 'us21',
            'store_newsletter_mailchimp_api_key' => app(MailchimpCredentialCodec::class)
                ->encode('super-secret-us21'),
            'store_newsletter_mailchimp_list_id' => 'audience123',
        ]);
    }
}
