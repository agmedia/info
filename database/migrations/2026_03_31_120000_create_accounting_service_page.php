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

        $templateKey = ServicePageTemplateRegistry::ACCOUNTING;
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
                'sort_order' => 6,
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
                'title' => 'Računovodstvo',
                'slug' => 'racunovodstvo',
                'meta_title' => 'Računovodstvo',
                'meta_description' => 'Računovodstvena podrška, vođenje poslovnih knjiga, obračun plaća i izvještavanje za svakodnevno poslovanje.',
            ],
            'en' => [
                'title' => 'Accounting',
                'slug' => 'accounting',
                'meta_title' => 'Accounting',
                'meta_description' => 'Accounting support, bookkeeping, payroll processing, and reporting for day-to-day business operations.',
            ],
        ];

        foreach ($translations as $locale => $translation) {
            $payload = ServicePageTemplateRegistry::defaultTranslationPayload($templateKey, $locale);

            DB::table('content_service_page_translations')->updateOrInsert(
                [
                    'service_page_id' => $servicePageId,
                    'locale' => $locale,
                ],
                [
                    'title' => $translation['title'],
                    'slug' => $translation['slug'],
                    'meta_title' => $translation['meta_title'],
                    'meta_description' => $translation['meta_description'],
                    'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never removes user-managed content.
    }
};
