<?php

namespace App\Mail;

use App\Models\Content\Support\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ContactAdminMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $theme = 'alpha';

    public function __construct(
        public ContactMessage $contactMessage
    ) {
        $this->locale($this->resolveLocale());
    }

    public function envelope(): Envelope
    {
        $replyTo = [];

        if (filter_var($this->contactMessage->email, FILTER_VALIDATE_EMAIL)) {
            $replyTo[] = new Address(
                (string) $this->contactMessage->email,
                $this->safeHeaderValue((string) $this->contactMessage->name, 'Website contact')
            );
        }

        return new Envelope(
            replyTo: $replyTo,
            subject: $this->subjectLine()
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-admin',
            text: 'mail.contact-admin-text',
            with: $this->viewData()
        );
    }

    private function subjectLine(): string
    {
        $subject = $this->safeHeaderValue(
            (string) $this->contactMessage->subject,
            $this->translate('fallback_subject')
        );
        $sender = $this->safeHeaderValue(
            (string) $this->contactMessage->name,
            $this->translate('fallback_sender')
        );

        return Str::limit($this->translate('subject', [
            'type' => $this->formLabel(),
            'subject' => $subject,
            'sender' => $sender,
        ]), 190, '…');
    }

    /**
     * @return array<string, mixed>
     */
    private function viewData(): array
    {
        $payload = (array) ($this->contactMessage->payload ?? []);
        $company = trim((string) ($payload['company'] ?? data_get($payload, 'answers.company_name', '')));
        $sourcePage = trim((string) ($payload['source_page'] ?? ''));
        $sourceUrl = $this->safeSourceUrl($sourcePage, trim((string) ($payload['url'] ?? '')));
        $email = trim((string) $this->contactMessage->email);
        $phone = trim((string) ($this->contactMessage->phone ?? ''));
        $name = $this->plainText((string) $this->contactMessage->name);
        $subject = $this->plainText((string) $this->contactMessage->subject);
        $message = $this->plainText((string) $this->contactMessage->message);
        $emailDisplay = $this->plainText($email !== '' ? $email : $this->translate('not_provided'));
        $phoneDisplay = $this->plainText($phone !== '' ? $phone : $this->translate('not_provided'));
        $companyDisplay = $company !== '' && $company !== trim((string) $this->contactMessage->name)
            ? $this->plainText($company)
            : null;
        $sourceLabel = $sourcePage !== '' ? $sourcePage : ($sourceUrl !== null ? $sourceUrl : null);
        $createdAt = $this->contactMessage->created_at;

        return [
            'formLabel' => $this->formLabel(),
            'adminUrl' => $this->adminUrl(),
            'replyUrl' => filter_var($email, FILTER_VALIDATE_EMAIL)
                ? 'mailto:'.$email.'?subject='.rawurlencode('Re: '.(string) $this->contactMessage->subject)
                : null,
            'nameText' => $name,
            'nameHtml' => $this->escapedPlainTextHtml($name),
            'subjectText' => $subject,
            'subjectHtml' => $this->escapedPlainTextHtml($subject),
            'messageText' => $message,
            'messageHtml' => $this->escapedPlainTextHtml($message),
            'emailText' => $emailDisplay,
            'emailHtml' => $this->escapedPlainTextHtml($emailDisplay),
            'phoneText' => $phoneDisplay,
            'phoneHtml' => $this->escapedPlainTextHtml($phoneDisplay),
            'companyText' => $companyDisplay,
            'companyHtml' => $companyDisplay !== null ? $this->escapedPlainTextHtml($companyDisplay) : null,
            'sourceLabelText' => $sourceLabel !== null ? $this->plainText($sourceLabel) : null,
            'sourceLabelHtml' => $sourceLabel !== null
                ? $this->escapedPlainTextHtml($this->plainText($sourceLabel))
                : null,
            'localeLabel' => strtoupper($this->mailLocale()),
            'submittedAt' => $createdAt?->copy()
                ->timezone((string) config('app.display_timezone', 'Europe/Zagreb'))
                ->format('d.m.Y. H:i'),
            'reference' => $this->contactMessage->getKey() !== null
                ? '#'.$this->contactMessage->getKey()
                : null,
        ];
    }

    private function formType(): string
    {
        $formType = trim((string) $this->contactMessage->form_type);

        if ($formType !== '') {
            return $formType;
        }

        return trim((string) data_get(
            $this->contactMessage->payload,
            'form_type',
            ContactMessage::FORM_TYPE_CONTACT
        ));
    }

    private function formLabel(): string
    {
        $key = match ($this->formType()) {
            ContactMessage::FORM_TYPE_SERVICE_CONTACT => 'types.service_contact',
            ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT => 'types.collaboration_assessment',
            ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE => 'types.eu_funds_questionnaire',
            default => 'types.contact',
        };

        return $this->translate($key);
    }

    private function adminUrl(): string
    {
        $route = match ($this->formType()) {
            ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT => 'admin.messages.collaboration-assessment.index',
            ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE => 'admin.messages.eu-funds-questionnaire.index',
            default => 'admin.messages.contact.index',
        };

        return route($route);
    }

    private function safeSourceUrl(string $sourcePage, string $fallbackUrl): ?string
    {
        if ($sourcePage !== '' && str_starts_with($sourcePage, '/') && ! str_starts_with($sourcePage, '//')) {
            return url($sourcePage);
        }

        if (filter_var($fallbackUrl, FILTER_VALIDATE_URL)) {
            $scheme = strtolower((string) parse_url($fallbackUrl, PHP_URL_SCHEME));

            if (in_array($scheme, ['http', 'https'], true)) {
                return $fallbackUrl;
            }
        }

        return null;
    }

    private function resolveLocale(): string
    {
        $locale = strtolower(trim((string) data_get($this->contactMessage->payload, 'locale', '')));

        if (in_array($locale, ['hr', 'en'], true)) {
            return $locale;
        }

        $fallback = strtolower(trim((string) config('app.locale', 'hr')));

        return in_array($fallback, ['hr', 'en'], true) ? $fallback : 'hr';
    }

    private function mailLocale(): string
    {
        return is_string($this->locale) && $this->locale !== '' ? $this->locale : $this->resolveLocale();
    }

    /**
     * @param  array<string, scalar|null>  $replace
     */
    private function translate(string $key, array $replace = []): string
    {
        return Lang::get('mail.contact_admin.'.$key, $replace, $this->mailLocale());
    }

    private function safeHeaderValue(string $value, string $fallback): string
    {
        $value = trim((string) preg_replace('/\s+/u', ' ', $value));

        return Str::limit($value !== '' ? $value : $fallback, 120, '…');
    }

    private function plainText(string $value): string
    {
        return str_replace(["\0", "\r\n", "\r"], ['', "\n", "\n"], $value);
    }

    private function escapedPlainTextHtml(string $value): HtmlString
    {
        return new HtmlString(
            '<div class="mail-plain-text">'.nl2br(e($value), false).'</div>'
        );
    }
}
