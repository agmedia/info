<?php

use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        $templateKey = ServicePageTemplateRegistry::FAMILY_BUSINESS;
        $code = ServicePageTemplateRegistry::defaultCode($templateKey);
        $now = now();
        $userId = DB::table('users')->orderBy('id')->value('id');

        $servicePageId = DB::table('content_service_pages')
            ->where('code', $code)
            ->value('id');

        if (! $servicePageId) {
            $servicePageId = DB::table('content_service_pages')->insertGetId([
                'code' => $code,
                'template_key' => $templateKey,
                'is_active' => true,
                'published_at' => $now,
                'sort_order' => 10,
                'payload' => json_encode(
                    ServicePageTemplateRegistry::defaultPagePayload($templateKey),
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $translations = [
            'hr' => [
                'title' => 'Obiteljski biznis',
                'slug' => 'obiteljski-biznis',
                'meta_title' => 'Obiteljski biznis',
                'meta_description' => 'Savjetodavna podrška za obiteljska poduzeća kroz teme upravljanja, tranzicije i odnosa.',
            ],
            'en' => [
                'title' => 'Family Business',
                'slug' => 'family-business',
                'meta_title' => 'Family Business',
                'meta_description' => 'Advisory support for family businesses across governance, succession, and relationship dynamics.',
            ],
        ];

        foreach ($translations as $locale => $translation) {
            $exists = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePageId)
                ->where('locale', $locale)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('content_service_page_translations')->insert([
                'service_page_id' => $servicePageId,
                'locale' => $locale,
                'title' => $translation['title'],
                'slug' => $translation['slug'],
                'meta_title' => $translation['meta_title'],
                'meta_description' => $translation['meta_description'],
                'payload' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never removes user-managed content.
    }
};
