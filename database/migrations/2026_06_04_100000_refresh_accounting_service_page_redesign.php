<?php

use App\Support\Content\AccountingServicePageDefaults;
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
        $servicePage = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode($templateKey)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first(['id', 'payload']);

        if (! $servicePage) {
            return;
        }

        $now = now();
        $pagePayload = json_decode((string) ($servicePage->payload ?? ''), true);
        $pagePayload = is_array($pagePayload) ? $pagePayload : [];
        $pagePayload = ServicePageTemplateRegistry::mergePagePayload($templateKey, $pagePayload);
        $pagePayload['video_source'] = ['items' => []];

        DB::table('content_service_pages')
            ->where('id', $servicePage->id)
            ->update([
                'payload' => json_encode($pagePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
            ]);

        $translationMeta = [
            'hr' => [
                'title' => 'Računovodstvo',
                'slug' => 'racunovodstvo',
                'meta_title' => 'Računovodstvo',
                'meta_description' => 'Precizno računovodstvo, obračun plaća, porezne prijave, upravljačko izvještavanje i konsolidacija.',
            ],
            'en' => [
                'title' => 'Accounting',
                'slug' => 'accounting',
                'meta_title' => 'Accounting',
                'meta_description' => 'Precise accounting, payroll processing, tax filings, management reporting, and consolidation support.',
            ],
        ];

        foreach ($translationMeta as $locale => $meta) {
            $translation = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePage->id)
                ->where('locale', $locale)
                ->first(['id', 'payload']);

            $payload = json_decode((string) ($translation?->payload ?? ''), true);
            $payload = is_array($payload) ? $payload : [];
            $defaults = AccountingServicePageDefaults::defaultsForLocale($locale);

            foreach (['hero', 'overview', 'services', 'approach', 'intro_section', 'video_section', 'videos', 'meeting', 'blog_section'] as $sectionKey) {
                if (array_key_exists($sectionKey, $defaults)) {
                    $payload[$sectionKey] = $defaults[$sectionKey];
                }
            }

            $translationData = [
                ...$meta,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
            ];

            if ($translation) {
                DB::table('content_service_page_translations')
                    ->where('id', $translation->id)
                    ->update($translationData);

                continue;
            }

            DB::table('content_service_page_translations')->insert([
                'service_page_id' => $servicePage->id,
                'locale' => $locale,
                ...$translationData,
                'created_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never removes user-managed content.
    }
};
