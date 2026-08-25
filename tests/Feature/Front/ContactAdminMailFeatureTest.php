<?php

namespace Tests\Feature\Front;

use App\Mail\ContactAdminMail;
use App\Models\Content\Support\ContactMessage;
use App\Services\Front\StoreNotificationService;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactAdminMailFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_service_sends_the_admin_mailable_to_the_configured_recipient(): void
    {
        Mail::fake();

        app(SystemSettingsService::class)->putMany([
            'store_email_enabled' => true,
            'store_email_contact_to' => 'office@example.test',
        ]);

        $message = $this->createMessage([
            'name' => 'Tomislav Jureša',
            'email' => 'tomislav@example.test',
            'subject' => 'Upit s kontakt forme',
        ]);

        app(StoreNotificationService::class)->sendContactNotification($message);

        Mail::assertSent(ContactAdminMail::class, function (ContactAdminMail $mail) use ($message): bool {
            return $mail->hasTo('office@example.test')
                && $mail->hasReplyTo('tomislav@example.test', 'Tomislav Jureša')
                && $mail->contactMessage->is($message);
        });
    }

    public function test_admin_mail_renders_the_complete_message_in_branded_html_and_plain_text(): void
    {
        $longMessage = "Početak poruke\n\n"
            .str_repeat('Ovo je puni sadržaj vrlo duge poruke. ', 220)
            .'Sigurnosna provjera <script>alert("x")</script> KRAJ-DUGE-PORUKE';
        $message = $this->createMessage([
            'name' => 'Ivana Horvat',
            'email' => 'ivana@example.test',
            'phone' => '+385 91 111 2222',
            'subject' => 'Dogovor sastanka',
            'message' => $longMessage,
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_SERVICE_CONTACT,
                'locale' => 'hr',
                'company' => 'Primjer d.o.o.',
                'source_page' => '/revizija#kontakt',
                'url' => 'https://info.test/kontakt',
            ],
        ]);

        $mail = new ContactAdminMail($message);

        $mail->assertHasSubject('[Upit za uslugu] Dogovor sastanka — Ivana Horvat')
            ->assertHasReplyTo('ivana@example.test', 'Ivana Horvat')
            ->assertSeeInHtml('ALPHA CAPITALIS · WEB UPIT')
            ->assertSeeInHtml('Primjer d.o.o.')
            ->assertSeeInHtml('KRAJ-DUGE-PORUKE')
            ->assertSeeInHtml('#03121f', false)
            ->assertDontSeeInHtml('<script>', false)
            ->assertSeeInText('Sigurnosna provjera')
            ->assertSeeInText('<script>alert("x")</script>')
            ->assertSeeInText('KRAJ-DUGE-PORUKE')
            ->assertSeeInText(route('admin.messages.contact.index'));
    }

    public function test_user_content_is_plain_text_and_cannot_inject_markdown_or_html(): void
    {
        $message = $this->createMessage([
            'name' => '## Lažni naslov ![name-pixel](https://evil.example/name.png)',
            'subject' => '[Lažna poveznica](https://evil.example/subject)',
            'message' => implode("\n", [
                '# Umetnuti naslov',
                '![tracking-pixel](https://evil.example/pixel.png)',
                '[Klikni ovdje](https://evil.example/phish)',
                '<script>alert("xss")</script>',
            ]),
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_CONTACT,
                'locale' => 'hr',
                'company' => '![company-pixel](https://evil.example/company.png)',
                'source_page' => '',
                'url' => 'https://evil.example/source',
            ],
        ]);
        $mail = new ContactAdminMail($message);

        $mail->assertSeeInHtml('![tracking-pixel](https://evil.example/pixel.png)')
            ->assertSeeInHtml('[Klikni ovdje](https://evil.example/phish)')
            ->assertSeeInHtml('&lt;script&gt;alert("xss")&lt;/script&gt;', false)
            ->assertDontSeeInHtml('src="https://evil.example', false)
            ->assertDontSeeInHtml('href="https://evil.example', false)
            ->assertDontSeeInHtml('<h1>Umetnuti naslov</h1>', false)
            ->assertDontSeeInHtml('<h2>Lažni naslov', false)
            ->assertDontSeeInHtml('<script>', false)
            ->assertSeeInText('![tracking-pixel](https://evil.example/pixel.png)')
            ->assertSeeInText('[Klikni ovdje](https://evil.example/phish)')
            ->assertSeeInText('<script>alert("xss")</script>');
    }

    public function test_admin_mail_uses_form_specific_labels_subjects_and_admin_destinations(): void
    {
        $cases = [
            ContactMessage::FORM_TYPE_CONTACT => [
                'label' => 'Kontakt',
                'route' => 'admin.messages.contact.index',
            ],
            ContactMessage::FORM_TYPE_SERVICE_CONTACT => [
                'label' => 'Upit za uslugu',
                'route' => 'admin.messages.contact.index',
            ],
            ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT => [
                'label' => 'Procjena suradnje',
                'route' => 'admin.messages.collaboration-assessment.index',
            ],
            ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE => [
                'label' => 'EU fondovi',
                'route' => 'admin.messages.eu-funds-questionnaire.index',
            ],
        ];

        foreach ($cases as $formType => $expected) {
            $message = $this->createMessage([
                'name' => 'Test Pošiljatelj',
                'subject' => 'Testni predmet',
                'payload' => [
                    'form_type' => $formType,
                    'locale' => 'hr',
                ],
            ]);
            $mail = new ContactAdminMail($message);

            $mail->assertHasSubject(sprintf(
                '[%s] Testni predmet — Test Pošiljatelj',
                $expected['label']
            ))
                ->assertSeeInHtml($expected['label'])
                ->assertSeeInHtml(route($expected['route']));
        }
    }

    public function test_admin_mail_uses_the_submission_locale_for_english_inquiries(): void
    {
        $message = $this->createMessage([
            'name' => 'Jane Doe',
            'subject' => 'Contact form inquiry',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_SERVICE_CONTACT,
                'locale' => 'en',
                'source_page' => '/audit#contact',
            ],
        ]);
        $mail = new ContactAdminMail($message);

        $mail->assertHasSubject('[Service inquiry] Contact form inquiry — Jane Doe')
            ->assertSeeInHtml('WEBSITE INQUIRY')
            ->assertSeeInHtml('Submission details')
            ->assertSeeInText('Form language: EN');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createMessage(array $overrides = []): ContactMessage
    {
        $payload = array_replace_recursive([
            'form_type' => ContactMessage::FORM_TYPE_CONTACT,
            'locale' => 'hr',
            'url' => 'https://info.test/kontakt',
            'source_page' => '/kontakt',
        ], (array) ($overrides['payload'] ?? []));

        unset($overrides['payload']);

        return ContactMessage::query()->create(array_merge([
            'name' => 'Test Pošiljatelj',
            'email' => 'sender@example.test',
            'phone' => null,
            'subject' => 'Testni predmet',
            'message' => 'Cijeli sadržaj testne poruke.',
            'status' => ContactMessage::STATUS_NEW,
            'payload' => $payload,
        ], $overrides));
    }
}
