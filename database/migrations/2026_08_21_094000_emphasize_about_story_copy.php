<?php

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

        $translationExists = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', 'hr')
            ->exists();

        if ($translationExists) {
            // Existing CMS copy is authoritative even when the story body was
            // deliberately cleared by an editor.
            return;
        }

        // This migration does not have enough structural page data to safely
        // create a missing translation. It intentionally leaves it untranslated.
    }

    public function down(): void
    {
        // Editorial emphasis is intentionally preserved on rollback.
    }
};
