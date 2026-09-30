<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content\Support\ContactMessage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CollaborationAssessmentAttachmentController extends Controller
{
    public function __invoke(ContactMessage $contactMessage, int $attachment): StreamedResponse
    {
        abort_unless(
            $contactMessage->form_type === ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
            404
        );

        $attachments = (array) data_get($contactMessage->payload, 'attachments', []);
        $metadata = $attachments[$attachment] ?? null;

        abort_unless(is_array($metadata), 404);

        $disk = trim((string) ($metadata['disk'] ?? ''));
        $path = trim((string) ($metadata['path'] ?? ''));
        $name = basename(trim((string) ($metadata['name'] ?? '')));

        abort_unless(
            $disk === 'local'
            && str_starts_with($path, 'contact-message-attachments/')
            && Storage::disk($disk)->exists($path),
            404
        );

        return Storage::disk($disk)->download(
            $path,
            $name !== '' ? $name : basename($path)
        );
    }
}
