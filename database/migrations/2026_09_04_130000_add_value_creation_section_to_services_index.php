<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function up(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        foreach ($this->contentByLocale() as $locale => $content) {
            $translations = DB::table('content_service_page_translations as translations')
                ->join('content_service_pages as pages', 'pages.id', '=', 'translations.service_page_id')
                ->where('pages.template_key', 'services_index')
                ->where('translations.locale', $locale)
                ->select(['translations.id', 'translations.payload'])
                ->get();

            foreach ($translations as $translation) {
                $payload = $this->decodePayload($translation->payload);
                $showcase = is_array($payload['showcase'] ?? null) ? $payload['showcase'] : [];

                if (($showcase['intro'] ?? '') === $content['previous_intro']) {
                    $showcase['intro'] = $content['intro'];
                }

                if (! array_key_exists('value_cards', $showcase) || ! is_array($showcase['value_cards'])) {
                    $showcase['value_cards'] = $content['value_cards'];
                }

                $payload['showcase'] = $showcase;

                DB::table('content_service_page_translations')
                    ->where('id', $translation->id)
                    ->update([
                        'payload' => $this->encodePayload($payload),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        foreach ($this->contentByLocale() as $locale => $content) {
            $translations = DB::table('content_service_page_translations as translations')
                ->join('content_service_pages as pages', 'pages.id', '=', 'translations.service_page_id')
                ->where('pages.template_key', 'services_index')
                ->where('translations.locale', $locale)
                ->select(['translations.id', 'translations.payload'])
                ->get();

            foreach ($translations as $translation) {
                $payload = $this->decodePayload($translation->payload);
                $showcase = is_array($payload['showcase'] ?? null) ? $payload['showcase'] : [];

                if (($showcase['intro'] ?? '') === $content['intro']) {
                    $showcase['intro'] = $content['previous_intro'];
                }

                if (($showcase['value_cards'] ?? null) === $content['value_cards']) {
                    unset($showcase['value_cards']);
                }

                $payload['showcase'] = $showcase;

                DB::table('content_service_page_translations')
                    ->where('id', $translation->id)
                    ->update([
                        'payload' => $this->encodePayload($payload),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /** @return array<string, array{previous_intro:string,intro:string,value_cards:array<int, array<string, mixed>>}> */
    private function contentByLocale(): array
    {
        return [
            'hr' => [
                'previous_intro' => 'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost pomažući klijentima da posluju sigurnije, transparentnije i učinkovitije.',
                'intro' => 'Stvaramo vrijednost za naše klijente u svim fazama razvoja njihova poslovanja, pružajući im stručnost, pouzdanu podršku i jasnu perspektivu za sigurnije donošenje odluka, učinkovito upravljanje i ostvarivanje novih prilika za rast.',
                'value_cards' => [
                    [
                        'key' => 'how',
                        'title' => 'Kako stvaramo vrijednost',
                        'items' => [
                            ['title' => 'Sigurnost', 'text' => 'Pouzdana podrška za sigurnije poslovanje i donošenje odluka.'],
                            ['title' => 'Jasnoću', 'text' => 'Pretvaramo kompleksne financijske informacije u jasnu sliku poslovanja.'],
                            ['title' => 'Kontrolu', 'text' => 'Pomažemo klijentima bolje upravljati financijama, rizicima i poslovnim procesima.'],
                            ['title' => 'Prilike za rast', 'text' => 'Prepoznajemo mogućnosti i pomažemo ih pretvoriti u konkretne rezultate.'],
                            ['title' => 'Dugoročnu vrijednost', 'text' => 'Gradimo rješenja koja podržavaju stabilnost, razvoj i održiv rast.'],
                        ],
                    ],
                    [
                        'key' => 'audience',
                        'title' => 'Kome stvaramo vrijednost',
                        'items' => [
                            ['title' => 'Vlasnicima', 'text' => 'Bolji uvid u poslovanje i sigurnost pri donošenju strateških odluka.'],
                            ['title' => 'Upravama i menadžmentu', 'text' => 'Pouzdana podloga za svakodnevne i dugoročne poslovne odluke.'],
                            ['title' => 'Poduzetnicima i tvrtkama', 'text' => 'Stručna podrška u razvoju, rastu i upravljanju poslovanjem.'],
                            ['title' => 'Obiteljskim tvrtkama', 'text' => 'Podrška u očuvanju, razvoju i prijenosu vrijednosti kroz generacije.'],
                            ['title' => 'Investitorima i partnerima', 'text' => 'Pouzdane informacije i stručna perspektiva za sigurnije poslovne odnose.'],
                        ],
                    ],
                ],
            ],
            'en' => [
                'previous_intro' => 'Through an integrated approach to audit, accounting, and financial advisory, we create value by helping clients operate with more confidence, transparency, and efficiency.',
                'intro' => 'We create value for our clients at every stage of their business development by providing expertise, reliable support, and a clear perspective for more confident decision-making, effective management, and new growth opportunities.',
                'value_cards' => [
                    [
                        'key' => 'how',
                        'title' => 'How we create value',
                        'items' => [
                            ['title' => 'Confidence', 'text' => 'Reliable support for safer business operations and decision-making.'],
                            ['title' => 'Clarity', 'text' => 'We turn complex financial information into a clear view of the business.'],
                            ['title' => 'Control', 'text' => 'We help clients better manage finances, risks, and business processes.'],
                            ['title' => 'Growth opportunities', 'text' => 'We identify opportunities and help turn them into concrete results.'],
                            ['title' => 'Long-term value', 'text' => 'We build solutions that support stability, development, and sustainable growth.'],
                        ],
                    ],
                    [
                        'key' => 'audience',
                        'title' => 'Who we create value for',
                        'items' => [
                            ['title' => 'Owners', 'text' => 'Better insight into the business and greater confidence when making strategic decisions.'],
                            ['title' => 'Boards and management', 'text' => 'A reliable foundation for day-to-day and long-term business decisions.'],
                            ['title' => 'Entrepreneurs and companies', 'text' => 'Expert support in business development, growth, and management.'],
                            ['title' => 'Family businesses', 'text' => 'Support in preserving, developing, and transferring value across generations.'],
                            ['title' => 'Investors and partners', 'text' => 'Reliable information and an expert perspective for more confident business relationships.'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function decodePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        $decoded = json_decode((string) $payload, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $payload */
    private function encodePayload(array $payload): string
    {
        return (string) json_encode($payload, self::JSON_OPTIONS);
    }
};
