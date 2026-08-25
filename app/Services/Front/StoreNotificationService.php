<?php

namespace App\Services\Front;

use App\Mail\ContactAdminMail;
use App\Mail\ResourceDownloadLinkMail;
use App\Models\Content\Resource\ResourceDownloadRequest;
use App\Models\Content\Support\ContactMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreNotificationService
{
    public function __construct(
        private readonly StoreSettingsService $storeSettings
    ) {}

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

        try {
            Mail::to($to)->send(new ContactAdminMail($message));
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
