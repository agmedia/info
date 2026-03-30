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

        $templateKey = ServicePageTemplateRegistry::AUDIT;
        $code = ServicePageTemplateRegistry::defaultCode($templateKey);
        $now = now();

        $translationDefaults = [
            'hr' => [
                'title' => 'Revizija',
                'slug' => 'revizija',
                'meta_title' => 'Revizija',
                'meta_description' => 'Revizija financijskih izvještaja, revizorski uvidi i posebni revizorski angažmani.',
            ],
            'en' => [
                'title' => 'Audit',
                'slug' => 'audit',
                'meta_title' => 'Audit',
                'meta_description' => 'Audit of financial statements, review engagements, and special audit services.',
            ],
        ];

        $servicePages = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orWhere('code', $code)
            ->get(['id', 'payload']);

        foreach ($servicePages as $servicePage) {
            $pagePayload = json_decode((string) ($servicePage->payload ?? ''), true);
            $pagePayload = ServicePageTemplateRegistry::mergePagePayload(
                $templateKey,
                is_array($pagePayload) ? $pagePayload : []
            );

            DB::table('content_service_pages')
                ->where('id', $servicePage->id)
                ->update([
                    'payload' => json_encode($pagePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ]);

            $translations = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePage->id)
                ->get(['id', 'locale', 'title', 'slug', 'meta_title', 'meta_description', 'payload'])
                ->keyBy('locale');

            foreach ($translationDefaults as $locale => $defaults) {
                $existing = $translations->get($locale);
                $payload = json_decode((string) ($existing?->payload ?? ''), true);
                $payload = ServicePageTemplateRegistry::mergeTranslationPayload(
                    $templateKey,
                    is_array($payload) ? $payload : [],
                    $locale
                );

                $translationData = [
                    'title' => trim((string) ($existing?->title ?? '')) !== '' ? $existing->title : $defaults['title'],
                    'slug' => trim((string) ($existing?->slug ?? '')) !== '' ? $existing->slug : $defaults['slug'],
                    'meta_title' => trim((string) ($existing?->meta_title ?? '')) !== '' ? $existing->meta_title : $defaults['meta_title'],
                    'meta_description' => trim((string) ($existing?->meta_description ?? '')) !== '' ? $existing->meta_description : $defaults['meta_description'],
                    'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ];

                if ($existing) {
                    DB::table('content_service_page_translations')
                        ->where('id', $existing->id)
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
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never removes user-managed content.
    }
};
