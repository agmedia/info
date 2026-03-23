<?php

namespace App\Mail;

use App\Models\Content\Resource\ResourceDownloadRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResourceDownloadLinkMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ResourceDownloadRequest $downloadRequest
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('resources.mail.subject')
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.resource-download-link'
        );
    }
}
