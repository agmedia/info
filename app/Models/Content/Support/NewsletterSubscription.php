<?php

namespace App\Models\Content\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    public const STATUS_RECEIVED = 'received';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMATION_PENDING = 'confirmation_pending';

    public const STATUS_SUBSCRIBED = 'subscribed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'email',
        'locale',
        'provider',
        'status',
        'provider_member_id',
        'subscriber_hash',
        'attempts',
        'subscribed_at',
        'last_attempt_at',
        'last_synced_at',
        'error_code',
        'error_message',
        'ip_hash',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'subscribed_at' => 'datetime',
            'last_attempt_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $subscription): void {
            $subscription->email = Str::lower(trim((string) $subscription->email));
            $subscription->locale = Str::lower(trim((string) $subscription->locale)) ?: 'hr';
        });
    }
}
