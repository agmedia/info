<?php

namespace App\Services\Newsletter;

use App\Models\Content\Support\NewsletterSubscription;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class NewsletterSubscriptionService
{
    private const PROVIDER_MAILCHIMP = 'mailchimp';

    public function __construct(
        private readonly SystemSettingsService $settings,
        private readonly MailchimpCredentialCodec $credentialCodec,
    ) {}

    public function subscribe(string $email, string $locale, ?string $ipAddress = null): NewsletterSubscriptionResult
    {
        $email = Str::lower(trim($email));
        $locale = Str::lower(trim($locale)) ?: 'hr';
        $provider = Str::lower(trim((string) $this->settings->get('store_newsletter_provider', 'none')));
        $subscription = $this->recordAttempt($email, $locale, $provider, $ipAddress);

        if ($provider === 'none' || $provider === '') {
            $subscription->forceFill([
                'provider' => 'none',
                'status' => NewsletterSubscription::STATUS_RECEIVED,
                'error_code' => null,
                'error_message' => null,
            ])->save();

            return NewsletterSubscriptionResult::received();
        }

        if ($provider !== self::PROVIDER_MAILCHIMP) {
            return $this->fail(
                $subscription,
                'unsupported_provider',
                'The configured newsletter provider is not supported.',
            );
        }

        $apiKey = $this->credentialCodec->decode(
            (string) $this->settings->get('store_newsletter_mailchimp_api_key', ''),
        );
        $listId = trim((string) $this->settings->get('store_newsletter_mailchimp_list_id', ''));
        $serverPrefix = Str::lower(trim((string) $this->settings->get(
            'store_newsletter_mailchimp_server_prefix',
            '',
        )));
        $dataCenter = $this->mailchimpDataCenter($apiKey, $serverPrefix);

        if ($apiKey === '' || $listId === '' || $dataCenter === null || ! preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $listId)) {
            return $this->fail(
                $subscription,
                'configuration_missing',
                'Mailchimp is enabled but its credentials are incomplete or invalid.',
            );
        }

        $subscriberHash = md5($email);
        $endpoint = sprintf(
            'https://%s.api.mailchimp.com/3.0/lists/%s/members/%s',
            $dataCenter,
            rawurlencode($listId),
            $subscriberHash,
        );

        try {
            $response = $this->sendToMailchimp($endpoint, $apiKey, [
                'email_address' => $email,
                'status_if_new' => 'pending',
                'language' => $locale,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Newsletter provider request failed.', [
                'subscription_id' => $subscription->getKey(),
                'provider' => self::PROVIDER_MAILCHIMP,
                'error_code' => 'connection_failed',
                'exception' => $exception::class,
            ]);

            return $this->fail(
                $subscription,
                'connection_failed',
                'The newsletter provider could not be reached.',
                subscriberHash: $subscriberHash,
                shouldLog: false,
            );
        }

        if (! $response->successful()) {
            return $this->failFromResponse($subscription, $response, $email, $apiKey, $listId, $subscriberHash);
        }

        $providerStatus = Str::lower(trim((string) $response->json('status', '')));

        if (in_array($providerStatus, ['transactional', 'unsubscribed'], true)) {
            try {
                $response = $this->sendToMailchimp($endpoint, $apiKey, [
                    'email_address' => $email,
                    'status' => 'pending',
                    'language' => $locale,
                ]);
            } catch (Throwable $exception) {
                Log::warning('Newsletter provider confirmation request failed.', [
                    'subscription_id' => $subscription->getKey(),
                    'provider' => self::PROVIDER_MAILCHIMP,
                    'error_code' => 'confirmation_request_failed',
                    'exception' => $exception::class,
                ]);

                return $this->fail(
                    $subscription,
                    'confirmation_request_failed',
                    'The newsletter confirmation request could not be sent.',
                    subscriberHash: $subscriberHash,
                    shouldLog: false,
                );
            }

            if (! $response->successful()) {
                return $this->failFromResponse($subscription, $response, $email, $apiKey, $listId, $subscriberHash);
            }

            $providerStatus = Str::lower(trim((string) $response->json('status', '')));
        }

        if (! in_array($providerStatus, ['pending', 'subscribed'], true)) {
            return $this->fail(
                $subscription,
                'unexpected_provider_status',
                'Mailchimp returned an unsupported member status.',
                subscriberHash: $subscriberHash,
            );
        }

        $subscription->forceFill([
            'status' => $providerStatus === 'subscribed'
                ? NewsletterSubscription::STATUS_SUBSCRIBED
                : NewsletterSubscription::STATUS_CONFIRMATION_PENDING,
            'provider_member_id' => Str::limit(trim((string) $response->json('id', $subscriberHash)), 191, ''),
            'subscriber_hash' => $subscriberHash,
            'subscribed_at' => $providerStatus === 'subscribed'
                ? ($subscription->subscribed_at ?: now())
                : $subscription->subscribed_at,
            'last_synced_at' => now(),
            'error_code' => null,
            'error_message' => null,
            'payload' => array_merge((array) $subscription->payload, [
                'source' => 'footer',
                'provider_status' => $providerStatus,
            ]),
        ])->save();

        return NewsletterSubscriptionResult::accepted();
    }

    /**
     * @param  array<string, string>  $payload
     */
    private function sendToMailchimp(string $endpoint, string $apiKey, array $payload): Response
    {
        return Http::acceptJson()
            ->asJson()
            ->withBasicAuth('alphacapitalis-newsletter', $apiKey)
            ->connectTimeout(5)
            ->timeout(12)
            ->put($endpoint, $payload);
    }

    private function recordAttempt(string $email, string $locale, string $provider, ?string $ipAddress): NewsletterSubscription
    {
        $subscription = NewsletterSubscription::query()->firstOrNew(['email' => $email]);
        $subscription->fill([
            'locale' => $locale,
            'provider' => $provider !== '' ? $provider : 'none',
            'status' => $subscription->exists
                ? $subscription->status
                : NewsletterSubscription::STATUS_PENDING,
            'attempts' => max(0, (int) $subscription->attempts) + 1,
            'last_attempt_at' => now(),
            'ip_hash' => $ipAddress !== null && trim($ipAddress) !== ''
                ? hash_hmac('sha256', trim($ipAddress), (string) config('app.key'))
                : null,
            'error_code' => null,
            'error_message' => null,
            'payload' => [
                'source' => 'footer',
                'consent' => true,
                'consent_recorded_at' => now()->toIso8601String(),
            ],
        ]);
        $subscription->save();

        return $subscription;
    }

    private function failFromResponse(
        NewsletterSubscription $subscription,
        Response $response,
        string $email,
        string $apiKey,
        string $listId,
        string $subscriberHash,
    ): NewsletterSubscriptionResult {
        $errorCode = $this->providerErrorCode($response);
        $errorMessage = $this->sanitizeProviderError($response, [$email, $apiKey, $listId]);

        return $this->fail(
            $subscription,
            $errorCode,
            $errorMessage,
            subscriberHash: $subscriberHash,
            httpStatus: $response->status(),
        );
    }

    private function fail(
        NewsletterSubscription $subscription,
        string $errorCode,
        string $errorMessage,
        ?string $subscriberHash = null,
        ?int $httpStatus = null,
        bool $shouldLog = true,
    ): NewsletterSubscriptionResult {
        $subscription->forceFill([
            'status' => NewsletterSubscription::STATUS_FAILED,
            'subscriber_hash' => $subscriberHash ?: $subscription->subscriber_hash,
            'error_code' => Str::limit($errorCode, 120, ''),
            'error_message' => Str::limit($errorMessage, 1000, ''),
            'last_synced_at' => now(),
        ])->save();

        if ($shouldLog) {
            Log::warning('Newsletter subscription synchronization failed.', array_filter([
                'subscription_id' => $subscription->getKey(),
                'provider' => $subscription->provider,
                'error_code' => $subscription->error_code,
                'http_status' => $httpStatus,
            ], static fn (mixed $value): bool => $value !== null));
        }

        return NewsletterSubscriptionResult::unavailable();
    }

    private function providerErrorCode(Response $response): string
    {
        $candidate = trim((string) ($response->json('title') ?: $response->json('type') ?: 'provider_error'));
        $candidate = Str::snake(Str::ascii($candidate));

        return Str::limit($candidate !== '' ? $candidate : 'provider_error', 120, '');
    }

    /**
     * @param  array<int, string>  $sensitiveValues
     */
    private function sanitizeProviderError(Response $response, array $sensitiveValues): string
    {
        $message = trim(implode(' — ', array_filter([
            trim((string) $response->json('title', '')),
            trim((string) $response->json('detail', '')),
        ])));

        if ($message === '') {
            $message = 'Mailchimp rejected the subscription request.';
        }

        $message = strip_tags($message);
        $message = (string) preg_replace('/[\r\n\t]+/', ' ', $message);

        foreach ($sensitiveValues as $sensitiveValue) {
            if ($sensitiveValue !== '') {
                $message = str_ireplace($sensitiveValue, '[redacted]', $message);
            }
        }

        return Str::limit(trim($message), 1000, '');
    }

    private function mailchimpDataCenter(string $apiKey, string $configuredPrefix): ?string
    {
        if (preg_match('/-(us\d+)$/i', trim($apiKey), $matches) !== 1) {
            return null;
        }

        $apiKeyPrefix = Str::lower((string) $matches[1]);

        if ($configuredPrefix !== '' && $configuredPrefix !== $apiKeyPrefix) {
            return null;
        }

        return $configuredPrefix !== '' ? $configuredPrefix : $apiKeyPrefix;
    }
}
