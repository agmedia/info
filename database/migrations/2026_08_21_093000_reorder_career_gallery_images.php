<?php

use App\Models\Content\Page\InfoPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('media')) {
            return;
        }

        $page = InfoPage::query()
            ->where(function ($query): void {
                $query->where('code', 'career')->orWhere('layout', 'career');
            })
            ->orderByRaw("CASE WHEN code = 'career' THEN 0 ELSE 1 END")
            ->first();

        if (! $page) {
            return;
        }

        $mediaByFileName = $page->getMedia('career_gallery_images')->keyBy('file_name');
        $fileNames = [
            'career-office-detail.jpg',
            'career-office.jpg',
            'career-team-collaboration.jpg',
        ];

        foreach ($fileNames as $index => $fileName) {
            $media = $mediaByFileName->get($fileName);

            if ($media) {
                $media->forceFill(['order_column' => $index + 1])->saveQuietly();
            }
        }
    }

    public function down(): void
    {
        // Editorial media order is intentionally preserved on rollback.
    }
};
