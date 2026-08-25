<?php

namespace App\Services\Newsletter;

final readonly class NewsletterSubscriptionResult
{
    public function __construct(
        public bool $successful,
        public string $messageKey,
        public int $httpStatus,
    ) {}

    public static function accepted(): self
    {
        return new self(true, 'newsletter.success', 200);
    }

    public static function received(): self
    {
        return new self(true, 'newsletter.received', 200);
    }

    public static function unavailable(): self
    {
        return new self(false, 'newsletter.unavailable', 503);
    }
}
