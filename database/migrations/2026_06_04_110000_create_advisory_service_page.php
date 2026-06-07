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

        $templateKey = ServicePageTemplateRegistry::ADVISORY;
        $code = ServicePageTemplateRegistry::defaultCode($templateKey);
        $now = now();
        $userId = DB::table('users')->orderBy('id')->value('id');

        $servicePage = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orWhere('code', $code)
            ->orderByRaw('case when code = ? then 0 else 1 end', [$code])
            ->orderBy('id')
            ->first(['id', 'payload']);

        $pagePayload = json_encode(
            ServicePageTemplateRegistry::defaultPagePayload($templateKey),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if (! $servicePage) {
            $servicePageId = DB::table('content_service_pages')->insertGetId([
                'code' => $code,
                'template_key' => $templateKey,
                'is_active' => true,
                'published_at' => $now,
                'sort_order' => 35,
                'payload' => $pagePayload,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $servicePageId = (int) $servicePage->id;
            $existingPayload = json_decode((string) ($servicePage->payload ?? ''), true);
            $existingPayload = is_array($existingPayload) ? $existingPayload : [];
            $mergedPayload = ServicePageTemplateRegistry::mergePagePayload($templateKey, $existingPayload);

            DB::table('content_service_pages')
                ->where('id', $servicePageId)
                ->update([
                    'code' => $code,
                    'template_key' => $templateKey,
                    'payload' => json_encode($mergedPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                    'updated_by' => $userId,
                ]);
        }

        $translations = [
            'hr' => [
                'title' => 'Savjetovanje',
                'slug' => 'savjetovanje',
                'meta_title' => 'Poslovno savjetovanje',
                'meta_description' => 'Financijsko i porezno savjetovanje, pribavljanje financiranja, due diligence, procjene vrijednosti i M&A savjetovanje.',
            ],
            'en' => [
                'title' => 'Advisory',
                'slug' => 'advisory',
                'meta_title' => 'Advisory',
                'meta_description' => 'Financial and tax advisory, financing, due diligence, valuations, and M&A advisory.',
            ],
        ];

        foreach ($translations as $locale => $translation) {
            $existingTranslation = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);

            $existingPayload = json_decode((string) ($existingTranslation?->payload ?? ''), true);
            $existingPayload = is_array($existingPayload) ? $existingPayload : [];
            $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload($templateKey, $existingPayload, $locale);

            $translationData = [
                'title' => $translation['title'],
                'slug' => $translation['slug'],
                'meta_title' => $translation['meta_title'],
                'meta_description' => $translation['meta_description'],
                'payload' => json_encode($translationPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
            ];

            if ($existingTranslation) {
                DB::table('content_service_page_translations')
                    ->where('id', $existingTranslation->id)
                    ->update($translationData);

                continue;
            }

            DB::table('content_service_page_translations')->insert([
                'service_page_id' => $servicePageId,
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
