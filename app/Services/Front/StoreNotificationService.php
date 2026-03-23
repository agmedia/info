<?php

namespace App\Services\Front;

use App\Mail\ResourceDownloadLinkMail;
use App\Models\Content\Support\ContactMessage;
use App\Models\Content\Resource\ResourceDownloadRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreNotificationService
{
    public function __construct(
        private readonly StoreSettingsService $storeSettings
    ) {
    }

    public function sendContactNotification(ContactMessage $message): void
    {
        $emailSettings = $this->storeSettings->email();
        if (! (bool) ($emailSettings['enabled'] ?? false)) {
            return;
        }

        $to = trim((string) ($emailSettings['contact_to'] ?? ''));
        if ($to === '') {
            return;
        }

        $subject = '[Contact] '.($message->subject ?: 'New contact message');
        $body = implode("\n", [
            'Name: '.(string) $message->name,
            'Email: '.(string) $message->email,
            'Phone: '.(string) ($message->phone ?? ''),
            'Subject: '.(string) $message->subject,
            'Message:',
            (string) $message->message,
        ]);

        try {
            Mail::raw($body, static function ($mail) use ($to, $subject, $message): void {
                $mail->to($to)->subject($subject);
                if (filter_var($message->email, FILTER_VALIDATE_EMAIL)) {
                    $mail->replyTo($message->email, (string) $message->name);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('Store contact notification failed: '.$e->getMessage());
        }
    }

    public function sendResourceDownloadLink(ResourceDownloadRequest $downloadRequest): void
    {
        if (! filter_var($downloadRequest->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($downloadRequest->email, (string) $downloadRequest->name)
                ->send(new ResourceDownloadLinkMail($downloadRequest));
        } catch (\Throwable $e) {
            Log::warning('Resource download delivery failed: '.$e->getMessage(), [
                'request_id' => $downloadRequest->getKey(),
                'email' => $downloadRequest->email,
            ]);
        }
    }
}
