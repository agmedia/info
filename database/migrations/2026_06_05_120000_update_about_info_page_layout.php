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
            'layout' => 'about',
            'is_active' => true,
            'show_in_footer' => true,
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
                'created_by' => null,
                'created_at' => $now,
            ]);
        }

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'hr',
            title: 'O nama',
            slug: 'o-nama',
            excerpt: 'Naša priča, vrijednosti, tim i povjerenje koje gradimo s klijentima.',
            metaTitle: 'O nama | Alpha Capitalis',
            metaDescription: 'Upoznajte ALPHA CAPITALIS, našu priču, vrijednosti, tim, društveno odgovorno poslovanje i reference.'
        );

        $this->upsertTranslation(
            pageId: (int) $pageId,
            locale: 'en',
            title: 'About Us',
            slug: 'about-us',
            excerpt: 'Our story, values, team and the trust we build with clients.',
            metaTitle: 'About Us | Alpha Capitalis',
            metaDescription: 'Learn more about ALPHA CAPITALIS, our story, values, team, social responsibility and references.'
        );

        if (Schema::hasTable('categories') && Schema::hasTable('content_info_page_category')) {
            $aboutCategoryId = DB::table('categories')
                ->where('scope', 'page')
                ->where('code', 'about')
                ->value('id');

            if ($aboutCategoryId) {
                DB::table('content_info_page_category')->updateOrInsert(
                    [
                        'page_id' => $pageId,
                        'category_id' => $aboutCategoryId,
                    ],
                    [
                        'sort_order' => 0,
                        'is_primary' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('content_info_page_translations')) {
            return;
        }

        $pageId = DB::table('content_info_pages')
            ->where('code', 'about-us')
            ->where('layout', 'about')
            ->value('id');

        if (! $pageId) {
            return;
        }

        $now = now();

        DB::table('content_info_pages')
            ->where('id', $pageId)
            ->update([
                'layout' => 'default',
                'updated_at' => $now,
            ]);

        DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', 'hr')
            ->update([
                'excerpt' => null,
                'body_html' => null,
                'meta_title' => 'O nama | Alpha Capitalis',
                'meta_description' => null,
                'payload' => null,
                'updated_at' => $now,
            ]);

        DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', 'en')
            ->update([
                'excerpt' => null,
                'body_html' => null,
                'meta_title' => 'About Us | Alpha Capitalis',
                'meta_description' => null,
                'payload' => null,
                'updated_at' => $now,
            ]);
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

        $payload = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body_html' => null,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
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
