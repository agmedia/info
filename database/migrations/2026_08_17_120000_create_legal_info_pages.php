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

        $pages = [
            [
                'code' => 'privacy-policy',
                'title' => 'Politika privatnosti',
                'slug' => 'politika-privatnosti',
                'excerpt' => 'Kako Alpha Capitalis prikuplja, koristi, čuva i štiti osobne podatke.',
                'body_html' => $this->legalPageHtml('politika-privatnosti.html'),
                'meta_title' => 'Politika privatnosti | Alpha Capitalis',
                'meta_description' => 'Saznajte koje osobne podatke Alpha Capitalis obrađuje, u koje svrhe, na kojim pravnim osnovama i koja prava imate.',
                'sort_order' => 90,
            ],
            [
                'code' => 'terms-of-use',
                'title' => 'Uvjeti korištenja',
                'slug' => 'uvjeti-koristenja',
                'excerpt' => 'Pravila pristupa i korištenja internetske stranice Alpha Capitalis.',
                'body_html' => $this->legalPageHtml('uvjeti-koristenja.html'),
                'meta_title' => 'Uvjeti korištenja | Alpha Capitalis',
                'meta_description' => 'Uvjeti pristupa i korištenja sadržaja, obrazaca i drugih funkcionalnosti internetske stranice Alpha Capitalis.',
                'sort_order' => 91,
            ],
        ];

        foreach ($pages as $page) {
            $this->upsertLegalPage($page);
        }
    }

    public function down(): void
    {
        // Legal CMS content is intentionally preserved on rollback to avoid deleting
        // administrator edits or a page that existed before this migration.
    }

    /**
     * @param array{
     *     code: string,
     *     title: string,
     *     slug: string,
     *     excerpt: string,
     *     body_html: string,
     *     meta_title: string,
     *     meta_description: string,
     *     sort_order: int
     * } $data
     */
    private function upsertLegalPage(array $data): void
    {
        $now = now();
        $pageId = DB::table('content_info_pages')
            ->where('code', $data['code'])
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', $data['slug'])
                ->value('page_id');
        }

        $pagePayload = [
            'code' => $data['code'],
            'layout' => 'legal',
            'is_active' => true,
            'show_in_footer' => true,
            'published_at' => $now,
            'sort_order' => $data['sort_order'],
            'payload' => null,
            'updated_at' => $now,
        ];

        if (! $pageId) {
            $pageId = DB::table('content_info_pages')->insertGetId($pagePayload + [
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
            ]);
        }

        $translationPayload = [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'],
            'body_html' => $data['body_html'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'payload' => null,
            'updated_at' => $now,
        ];

        $translation = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', 'hr');

        if ($translation->exists()) {
            return;
        }

        DB::table('content_info_page_translations')->insert($translationPayload + [
            'page_id' => $pageId,
            'locale' => 'hr',
            'created_at' => $now,
        ]);
    }

    private function legalPageHtml(string $filename): string
    {
        $contents = file_get_contents(resource_path('content/legal/'.$filename));

        if ($contents === false) {
            throw new RuntimeException('Legal page content could not be read: '.$filename);
        }

        return trim($contents);
    }
};
