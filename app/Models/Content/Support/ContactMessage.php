<?php

namespace App\Models\Content\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_RESOLVED = 'resolved';
    public const FORM_TYPE_CONTACT = 'contact';
    public const FORM_TYPE_SERVICE_CONTACT = 'service_contact';
    public const FORM_TYPE_COLLABORATION_ASSESSMENT = 'collaboration_assessment';
    public const FORM_TYPE_EU_FUNDS_QUESTIONNAIRE = 'eu_funds_questionnaire';
    public const SUBJECT_EU_FUNDS_QUESTIONNAIRE = 'EU Fondovi upitnik';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'form_type',
        'ip_address',
        'user_agent',
        'payload',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'payload' => 'array',
        'reviewed_by' => 'int',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $message): void {
            $payloadFormType = trim((string) data_get($message->payload, 'form_type', ''));

            if (in_array($payloadFormType, self::formTypes(), true)) {
                $message->form_type = $payloadFormType;

                return;
            }

            if (trim((string) $message->form_type) === '') {
                $message->form_type = $message->subject === self::SUBJECT_EU_FUNDS_QUESTIONNAIRE
                    ? self::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE
                    : self::FORM_TYPE_CONTACT;
            }
        });
    }

    /**
     * @return array<int, string>
     */
    public static function formTypes(): array
    {
        return [
            self::FORM_TYPE_CONTACT,
            self::FORM_TYPE_SERVICE_CONTACT,
            self::FORM_TYPE_COLLABORATION_ASSESSMENT,
            self::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }
}
