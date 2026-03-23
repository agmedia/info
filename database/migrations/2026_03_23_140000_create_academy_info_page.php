<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $pageId = DB::table('content_info_pages')
            ->where('code', 'academy')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'akademija')
                ->value('page_id');
        }

        $pageData = [
            'code' => 'academy',
            'layout' => 'academy',
            'is_active' => true,
            'show_in_footer' => false,
            'published_at' => $now,
            'sort_order' => 24,
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
                'created_by' => null,
                'created_at' => $now,
            ]);
        }

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'hr',
            title: 'ALPHA CAPITALIS AKADEMIJA',
            slug: 'akademija',
            excerpt: 'Predavanja i edukativni sadržaj na temu korporativnih financija',
            metaTitle: 'ALPHA CAPITALIS AKADEMIJA | Alpha Capitalis',
            metaDescription: 'Predavanja i edukativni sadržaj na temu korporativnih financija.'
        );

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'en',
            title: 'ALPHA CAPITALIS ACADEMY',
            slug: 'akademija',
            excerpt: 'Lectures and educational content on corporate finance',
            metaTitle: 'ALPHA CAPITALIS ACADEMY | Alpha Capitalis',
            metaDescription: 'Lectures and educational content on corporate finance.'
        );

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
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $page = DB::table('content_info_pages')
            ->where('code', 'academy')
            ->where('layout', 'academy')
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

    private function upsertTranslation(
        int $pageId,
        string $locale,
        string $title,
        string $slug,
        string $excerpt,
        string $metaTitle,
        string $metaDescription
    ): void {
        $now = now();

        $query = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', $locale);

        if ($query->exists()) {
            $query->update([
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body_html' => null,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'payload' => null,
                'updated_at' => $now,
            ]);

            return;
        }

        DB::table('content_info_page_translations')->insert([
            'page_id' => $pageId,
            'locale' => $locale,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body_html' => null,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'payload' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
};
