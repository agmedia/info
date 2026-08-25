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
        $servicePageId = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode($templateKey)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $servicePageId) {
            return;
        }

        $copyByLocale = [
            'hr' => [
                'hero_intro' => 'Povjerenje u financijske informacije počinje neovisnom i stručnom revizijom.',
                'overview_title' => 'Zašto Vam je revizija bitna?',
                'overview_body' => [
                    'Revizija pruža neovisnu i objektivnu procjenu financijskih informacija, povećava transparentnost i pouzdanost poslovanja te pomaže u prepoznavanju potencijalnih rizika.',
                    'Neovisna revizija daje Vam sigurnost da odluke donosite na temelju pouzdanih informacija. Uz stručan i objektivan pristup, Vaše poslovanje sagledavamo šire od samih brojki.',
                ],
            ],
            'en' => [
                'hero_intro' => 'Trust in financial information begins with an independent and expert audit.',
                'overview_title' => 'Why does audit matter to you?',
                'overview_body' => [
                    'Audit provides an independent and objective assessment of financial information, increases transparency and reliability, and helps identify potential risks.',
                    'An independent audit gives you confidence that your decisions are based on reliable information. Through an expert and objective approach, we look beyond the numbers to understand the wider context of your business.',
                ],
            ],
        ];

        foreach ($copyByLocale as $locale => $copy) {
            $translation = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);

            // An existing CMS translation is authoritative even when its payload
            // is intentionally empty. This migration may seed a missing locale,
            // but it must never re-populate an editor-cleared value.
            if ($translation) {
                continue;
            }

            $slug = ServicePageTemplateRegistry::canonicalStructuralSlug($templateKey, $locale);
            if (! is_string($slug) || $slug === '') {
                continue;
            }

            DB::table('content_service_page_translations')->insert([
                'service_page_id' => $servicePageId,
                'locale' => $locale,
                'title' => $locale === 'hr' ? 'Revizija' : 'Audit',
                'slug' => $slug,
                'meta_title' => null,
                'meta_description' => null,
                'payload' => json_encode([
                    'hero' => ['intro' => $copy['hero_intro']],
                    'overview' => [
                        'title' => $copy['overview_title'],
                        'highlight_title' => $copy['overview_title'],
                        'body' => $copy['overview_body'],
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // User-managed CMS content is intentionally preserved on rollback.
    }
};
