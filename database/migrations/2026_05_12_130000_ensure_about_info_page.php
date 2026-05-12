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
            ->where('code', 'about-us')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'o-nama')
                ->value('page_id');
        }

        $pageData = [
            'code' => 'about-us',
            'layout' => 'default',
            'is_active' => true,
            'published_at' => $now,
            'sort_order' => 20,
            'payload' => null,
            'updated_by' => null,
            'updated_at' => $now,
        ];

        if ($pageId) {
            DB::table('content_info_pages')
                ->where('id', $pageId)
                ->update($pageData);
        } else {
            $pageId = DB::table('content_info_pages')->insertGetId($pageData + [
                'show_in_footer' => false,
                'created_by' => null,
                'created_at' => $now,
            ]);
        }

        $this->upsertTranslation((int) $pageId, 'hr', 'O nama', 'o-nama');
        $this->upsertTranslation((int) $pageId, 'en', 'About Us', 'about-us');

        if (Schema::hasTable('categories') && Schema::hasTable('content_info_page_category')) {
            $aboutCategoryId = DB::table('categories')
                ->where('scope', 'page')
                ->where('code', 'about')
                ->value('id');

            if ($aboutCategoryId) {
                $exists = DB::table('content_info_page_category')
                    ->where('page_id', $pageId)
                    ->where('category_id', $aboutCategoryId)
                    ->exists();

                if (! $exists) {
                    DB::table('content_info_page_category')->insert([
                        'page_id' => $pageId,
                        'category_id' => $aboutCategoryId,
                        'sort_order' => 0,
                        'is_primary' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $page = DB::table('content_info_pages')
            ->where('code', 'about-us')
            ->where('layout', 'default')
            ->first(['id']);

        if (! $page) {
            return;
        }

        if (Schema::hasTable('content_info_page_category')) {
            DB::table('content_info_page_category')
                ->where('page_id', $page->id)
                ->delete();
        }

        DB::table('content_info_page_translations')
            ->where('page_id', $page->id)
            ->delete();

        DB::table('content_info_pages')
            ->where('id', $page->id)
            ->delete();
    }

    private function upsertTranslation(int $pageId, string $locale, string $title, string $slug): void
    {
        $now = now();

        $payload = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => null,
            'body_html' => null,
            'meta_title' => $title.' | Alpha Capitalis',
            'meta_description' => null,
            'payload' => null,
            'updated_at' => $now,
        ];

        $query = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', $locale);

        if ($query->exists()) {
            $query->update($payload);

            return;
        }

        DB::table('content_info_page_translations')->insert($payload + [
            'page_id' => $pageId,
            'locale' => $locale,
            'created_at' => $now,
        ]);
    }
};
