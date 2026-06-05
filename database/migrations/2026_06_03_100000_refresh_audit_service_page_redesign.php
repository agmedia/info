<?php

use App\Support\Content\AuditServicePageDefaults;
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
        $servicePageId = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode($templateKey)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $servicePageId) {
            return;
        }

        $now = now();
        $translationMeta = [
            'hr' => [
                'title' => 'Revizija',
                'slug' => 'revizija',
                'meta_title' => 'Revizija',
                'meta_description' => 'Neovisna revizija financijskih izvještaja, dobrovoljna revizija, interna revizija i revizije posebne namjene.',
            ],
            'en' => [
                'title' => 'Audit',
                'slug' => 'audit',
                'meta_title' => 'Audit',
                'meta_description' => 'Independent audit of financial statements, voluntary audit, internal audit, and special-purpose audit engagements.',
            ],
        ];

        foreach ($translationMeta as $locale => $meta) {
            $translation = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);

            $payload = json_decode((string) ($translation?->payload ?? ''), true);
            $payload = is_array($payload) ? $payload : [];
            $defaults = AuditServicePageDefaults::defaultsForLocale($locale);

            foreach (['hero', 'overview', 'obligors', 'services', 'approach', 'meeting', 'blog_section'] as $sectionKey) {
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
