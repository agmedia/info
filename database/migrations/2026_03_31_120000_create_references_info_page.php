<?php

use App\Models\Content\Page\InfoPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $pageId = DB::table('content_info_pages')
            ->where('code', 'references')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'reference')
                ->value('page_id');
        }

        $pageData = [
            'code' => 'references',
            'layout' => 'references',
            'is_active' => true,
            'show_in_footer' => false,
            'published_at' => $now,
            'sort_order' => 26,
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
            title: 'Reference',
            slug: 'reference',
            excerpt: 'Odabrani klijenti i partneri koji su nam ukazali povjerenje.',
            metaTitle: 'Reference | Alpha Capitalis',
            metaDescription: 'Pregled odabranih klijenata i partnera Alpha Capitalisa.'
        );

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'en',
            title: 'References',
            slug: 'reference',
            excerpt: 'Selected clients and partners who trust Alpha Capitalis.',
            metaTitle: 'References | Alpha Capitalis',
            metaDescription: 'Browse selected Alpha Capitalis clients and partners.'
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
            ->where('code', 'references')
            ->where('layout', 'references')
            ->first(['id']);

        if (! $page) {
            return;
        }

        InfoPage::query()->find($page->id)?->clearMediaCollection('reference_logos');

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
