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
                'legacy_hero_intro' => 'Precizno, pravovremeno i transparentno - preuzimamo vođenje vaših poslovnih knjiga kako biste se fokusirali na ono što zaista donosi rast.',
                'legacy_overview_title' => 'Što je računovodstvo?',
                'legacy_overview_body' => [
                    'Računovodstvo je sustavan zapis poslovnih transakcija koji osigurava točan prikaz financijskog stanja društva. Dobro računovodstvo nije samo zakonska obveza - to je temelj za donošenje kvalitetnih poslovnih odluka.',
                ],
                'hero_intro' => 'Vi vodite poslovanje. Mi brinemo da Vaše brojke budu točne, pravovremene i spremne za svaku odluku.',
                'overview_title' => 'Zašto Vam je računovodstvo bitno?',
                'overview_body' => [
                    'Mirnije poslovanje počinje jasnim i pouzdanim brojkama. Ažurne financijske informacije daju Vam kontrolu nad poslovanjem, pomažu prepoznati prilike i rizike te donijeti sigurnije odluke.',
                    'Uz ALPHA CAPITALIS ne dobivate samo računovodstvenu uslugu, već pouzdanog partnera koji razumije Vaše poslovanje i prati Vas kroz svakodnevne izazove i planove rasta.',
                ],
            ],
            'en' => [
                'legacy_hero_intro' => 'Precise, timely, and transparent accounting - we take over your books so you can stay focused on what truly drives growth.',
                'legacy_overview_title' => 'What is accounting?',
                'legacy_overview_body' => [
                    'Accounting is the systematic recording of business transactions that provides an accurate view of a company’s financial position. Good accounting is not only a legal obligation - it is the foundation for sound business decisions.',
                ],
                'hero_intro' => 'You run the business. We make sure your numbers are accurate, timely, and ready for every decision.',
                'overview_title' => 'Why does accounting matter to you?',
                'overview_body' => [
                    'Calmer business operations begin with clear and reliable numbers. Up-to-date financial information gives you control over your business, helps you identify opportunities and risks, and supports more confident decisions.',
                    'With ALPHA CAPITALIS, you get more than an accounting service - you get a reliable partner who understands your business and supports you through everyday challenges and growth plans.',
                ],
            ],
        ];

        foreach ($copyByLocale as $locale => $copy) {
            $translation = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);

            // Existing CMS rows are authoritative, including intentionally blank
            // payloads. Only a completely missing locale may be seeded here.
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
                'title' => $locale === 'hr' ? 'Računovodstvo' : 'Accounting',
                'slug' => $slug,
                'meta_title' => null,
                'meta_description' => null,
                'payload' => json_encode([
                    'hero' => ['intro' => $copy['hero_intro']],
                    'overview' => [
                        'title' => $copy['overview_title'],
                        'highlight_title' => $copy['overview_title'],
                        'intro' => '',
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
