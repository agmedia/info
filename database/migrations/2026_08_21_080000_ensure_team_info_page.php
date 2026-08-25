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

        $now = now();
        $pageId = DB::table('content_info_pages')
            ->where('code', 'team-page')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'alpha-capitalis-tim')
                ->value('page_id');
        }

        $pageData = [
            'code' => 'team-page',
            'layout' => 'team',
            'is_active' => true,
            'show_in_footer' => false,
            'published_at' => $now,
            'sort_order' => 30,
            'payload' => null,
            'updated_by' => null,
            'updated_at' => $now,
        ];

        if (! $pageId) {
            $pageId = DB::table('content_info_pages')->insertGetId($pageData + [
                'created_by' => null,
                'created_at' => $now,
            ]);
        }

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'hr',
            title: 'ALPHA CAPITALIS Tim',
            slug: 'alpha-capitalis-tim',
            intro: 'Upoznajte stručnjake koji povezuju znanja iz različitih područja kako bi klijentima pružili podršku tamo gdje im je najpotrebnija.',
            metaTitle: 'ALPHA CAPITALIS Tim',
        );

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'en',
            title: 'ALPHA CAPITALIS Team',
            slug: 'alpha-capitalis-team',
            intro: 'Meet the experts who connect knowledge across different fields to support clients where they need it most.',
            metaTitle: 'ALPHA CAPITALIS Team',
        );
    }

    public function down(): void
    {
        // Keep the CMS record so a rollback cannot remove administrator edits.
    }

    private function upsertTranslation(
        int $pageId,
        string $locale,
        string $title,
        string $slug,
        string $intro,
        string $metaTitle,
    ): void {
        $now = now();
        $translation = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', $locale);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $intro,
            'meta_title' => $metaTitle,
            'meta_description' => $intro,
            'updated_at' => $now,
        ];

        if ($translation->exists()) {
            return;
        }

        DB::table('content_info_page_translations')->insert($data + [
            'page_id' => $pageId,
            'locale' => $locale,
            'body_html' => null,
            'payload' => null,
            'created_at' => $now,
        ]);
    }
};
