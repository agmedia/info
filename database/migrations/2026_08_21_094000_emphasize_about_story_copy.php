<?php

use App\Support\Content\AboutPageDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('content_info_page_translations')) {
            return;
        }

        $pageId = DB::table('content_info_pages')
            ->where(function ($query): void {
                $query->where('code', 'about-us')->orWhere('layout', 'about');
            })
            ->orderByRaw("CASE WHEN code = 'about-us' THEN 0 ELSE 1 END")
            ->value('id');

        if (! $pageId) {
            return;
        }

        $translation = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', 'hr')
            ->first();

        if (! $translation) {
            return;
        }

        $payload = json_decode((string) $translation->payload, true);
        $payload = is_array($payload) ? $payload : [];
        $payload['about_page']['story']['body_html'] = (string) data_get(
            AboutPageDefaults::merge(null, 'hr'),
            'story.body_html',
            '',
        );

        DB::table('content_info_page_translations')
            ->where('id', $translation->id)
            ->update([
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Editorial emphasis is intentionally preserved on rollback.
    }
};
