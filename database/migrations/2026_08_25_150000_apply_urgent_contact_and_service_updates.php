<?php

use App\Services\Content\ContentBlockResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function up(): void
    {
        $this->updateStatsAndContacts();
        $this->updateHomeHero();
        $this->updateHomeServices();
        $this->updateServicesIndex();
        $this->updateAccounting();
        $this->updateAdvisory();
        $this->updateHomeHeroSetting();

        ContentBlockResolver::bumpCacheVersion();
    }

    public function down(): void
    {
        // Client-approved public copy and contact details are intentionally preserved.
    }

    private function updateStatsAndContacts(): void
    {
        $this->mutateContentBlockPayload('home-alpha-stats', function (array $payload, string $locale): array {
            $isCroatian = str_starts_with(strtolower($locale), 'hr');
            $payload['stats'] = $payload['contact_stats'] = $isCroatian
                ? [
                    ['label' => 'Odrađenih projekata', 'value' => '300', 'suffix' => '+'],
                    ['label' => 'Redovnih klijenata', 'value' => '700', 'suffix' => ''],
                    ['label' => 'Kvalificiranih stručnjaka', 'value' => '75', 'suffix' => ''],
                ]
                : [
                    ['label' => 'Completed projects', 'value' => '300', 'suffix' => '+'],
                    ['label' => 'Regular clients', 'value' => '700', 'suffix' => ''],
                    ['label' => 'Qualified professionals', 'value' => '75', 'suffix' => ''],
                ];

            $offices = [
                'alpha-capitalis' => [
                    'phone' => '+385 (1) 580 6656',
                    'hr' => 'Ured Zagreb',
                    'en' => 'Zagreb Office',
                ],
                'alpha-capitalis-east' => [
                    'phone' => '+385 (1) 580 6656',
                    'hr' => 'Ured Vinkovci',
                    'en' => 'Vinkovci Office',
                ],
                'alpha-capitalis-timia' => [
                    'phone' => '+385 (0) 51 301 503',
                    'hr' => 'Ured Rijeka',
                    'en' => 'Rijeka Office',
                ],
            ];
            $locations = is_array(data_get($payload, 'locations.items'))
                ? data_get($payload, 'locations.items')
                : [];

            foreach ($locations as $index => $location) {
                if (! is_array($location)) {
                    continue;
                }

                $entityKey = trim((string) ($location['entity_key'] ?? ''));
                if (! isset($offices[$entityKey])) {
                    continue;
                }

                $locations[$index]['phone'] = $offices[$entityKey]['phone'];
                $locations[$index]['office_label'] = $offices[$entityKey][$isCroatian ? 'hr' : 'en'];
            }

            data_set($payload, 'locations.items', array_values($locations));
            data_set($payload, 'contact_page.direct_phone', '+385 (1) 580 6656');

            return $payload;
        });
    }

    private function updateHomeHero(): void
    {
        if (! Schema::hasTable('content_blocks') || ! Schema::hasTable('content_block_translations')) {
            return;
        }

        $blockId = DB::table('content_blocks')->where('code', 'home-alpha-hero')->value('id');
        if (! $blockId) {
            return;
        }

        DB::table('content_block_translations')
            ->where('content_block_id', $blockId)
            ->where('locale', 'hr')
            ->update([
                'subtitle' => 'Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.',
                'updated_at' => now(),
            ]);

        DB::table('content_block_translations')
            ->where('content_block_id', $blockId)
            ->where('locale', 'en')
            ->update([
                'subtitle' => 'Accounting and Tax Advisory, Audit and Advisory — all in one place.',
                'updated_at' => now(),
            ]);
    }

    private function updateHomeServices(): void
    {
        $this->mutateContentBlockPayload('home-alpha-services', function (array $payload, string $locale): array {
            $isCroatian = str_starts_with(strtolower($locale), 'hr');
            $services = is_array($payload['services'] ?? null) ? $payload['services'] : [];

            foreach ($services as $index => $service) {
                if (! is_array($service)) {
                    continue;
                }

                $key = trim((string) ($service['key'] ?? ''));
                if ($key === 'accounting' || ($key === '' && $index === 1)) {
                    $services[$index]['title'] = $isCroatian
                        ? 'Računovodstvo i porezi'
                        : 'Accounting and Tax Advisory';
                    $services[$index]['subtitle'] = $isCroatian
                        ? 'kontrola, jasnoća i porezna sigurnost'
                        : 'control, clarity and tax confidence';
                    $services[$index]['text'] = $isCroatian
                        ? 'Precizno vođenje knjiga, pravovremeno izvještavanje i porezno savjetovanje za sigurnije poslovne odluke.'
                        : 'Accurate bookkeeping, timely reporting and tax advisory for more confident business decisions.';
                }

                if ($key === 'advisory' || ($key === '' && $index === 2)) {
                    $services[$index]['text'] = $isCroatian
                        ? 'Financijsko i strateško savjetovanje te pribavljanje kapitala - sve na jednom mjestu.'
                        : 'Financial and strategic advisory, along with capital raising — all in one place.';
                }
            }

            $payload['services'] = array_values($services);

            return $payload;
        });
    }

    private function updateServicesIndex(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        $pageId = DB::table('content_service_pages')
            ->where(fn ($query) => $query->where('code', 'services')->orWhere('template_key', 'services_index'))
            ->orderByRaw("CASE WHEN code = 'services' THEN 0 ELSE 1 END")
            ->value('id');
        if (! $pageId) {
            return;
        }

        $translations = DB::table('content_service_page_translations')
            ->where('service_page_id', $pageId)
            ->whereIn('locale', ['hr', 'en'])
            ->get(['id', 'locale', 'payload']);

        foreach ($translations as $translation) {
            $isCroatian = str_starts_with(strtolower((string) $translation->locale), 'hr');
            $payload = $this->decodePayload($translation->payload);
            $pillars = is_array($payload['primary_pillars'] ?? null) ? $payload['primary_pillars'] : [];

            foreach ($pillars as $index => $pillar) {
                if (! is_array($pillar)) {
                    continue;
                }

                $key = trim((string) ($pillar['key'] ?? ''));
                if ($key === 'accounting' || ($key === '' && $index === 1)) {
                    $pillars[$index]['title'] = $isCroatian
                        ? 'Računovodstvo i porezi'
                        : 'Accounting and Tax Advisory';
                    $pillars[$index]['subtitle'] = $isCroatian
                        ? 'kontrola, jasnoća i porezna sigurnost'
                        : 'control, clarity and tax confidence';
                    $pillars[$index]['text'] = $isCroatian
                        ? 'Precizno vođenje knjiga, pravovremeno izvještavanje i porezno savjetovanje za sigurnije poslovne odluke.'
                        : 'Accurate bookkeeping, timely reporting and tax advisory for more confident business decisions.';
                }

                if ($key === 'advisory' || ($key === '' && $index === 2)) {
                    $pillars[$index]['text'] = $isCroatian
                        ? 'Financijsko i strateško savjetovanje te pribavljanje kapitala - sve na jednom mjestu.'
                        : 'Financial and strategic advisory, along with capital raising — all in one place.';
                }
            }

            $payload['primary_pillars'] = array_values($pillars);
            DB::table('content_service_page_translations')
                ->where('id', $translation->id)
                ->update([
                    'meta_description' => $isCroatian
                        ? 'Pregled usluga ALPHA CAPITALISA: revizija, računovodstvo i porezi te poslovno savjetovanje.'
                        : 'Overview of ALPHA CAPITALIS services: audit, accounting and tax advisory, and business advisory.',
                    'payload' => $this->encodePayload($payload),
                    'updated_at' => now(),
                ]);
        }
    }

    private function updateAccounting(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        $pageId = DB::table('content_service_pages')
            ->where(fn ($query) => $query->where('code', 'racunovodstvo')->orWhere('template_key', 'accounting'))
            ->orderByRaw("CASE WHEN code = 'racunovodstvo' THEN 0 ELSE 1 END")
            ->value('id');
        if (! $pageId) {
            return;
        }

        $translations = DB::table('content_service_page_translations')
            ->where('service_page_id', $pageId)
            ->whereIn('locale', ['hr', 'en'])
            ->get(['id', 'locale', 'payload']);

        foreach ($translations as $translation) {
            $isCroatian = str_starts_with(strtolower((string) $translation->locale), 'hr');
            $serviceName = $isCroatian ? 'Računovodstvo i porezi' : 'Accounting and Tax Advisory';
            $payload = $this->decodePayload($translation->payload);

            data_set($payload, 'hero.subtitle_lead', $serviceName);
            data_set(
                $payload,
                'hero.image_alt',
                $isCroatian ? 'Usluge računovodstva i poreznog savjetovanja' : 'Accounting and tax advisory services',
            );
            data_set($payload, 'overview.kicker', mb_strtoupper($serviceName));
            data_set(
                $payload,
                'services.title',
                $isCroatian ? 'Naše usluge računovodstva i poreza' : 'Our Accounting and Tax Advisory Services',
            );
            data_set($payload, 'intro_section.kicker', mb_strtoupper($serviceName));
            data_set(
                $payload,
                'intro_section.title',
                $isCroatian ? 'Usluge računovodstva i poreza' : 'Accounting and Tax Advisory Services',
            );

            $introBody = data_get($payload, 'intro_section.body');
            if (is_array($introBody) && isset($introBody[1])) {
                $introBody[1] = $isCroatian
                    ? 'Usluge računovodstva i poreznog savjetovanja:'
                    : 'Accounting and Tax Advisory services:';
                data_set($payload, 'intro_section.body', array_values($introBody));
            }

            $items = is_array(data_get($payload, 'services.items'))
                ? array_values(data_get($payload, 'services.items'))
                : [];
            data_set($payload, 'services.items', $this->accountingServiceItems($items, $isCroatian));

            DB::table('content_service_page_translations')
                ->where('id', $translation->id)
                ->update([
                    'title' => $serviceName,
                    'meta_title' => $serviceName,
                    'meta_description' => $isCroatian
                        ? 'Računovodstvena i porezna podrška, vođenje poslovnih knjiga, obračun plaća, porezno savjetovanje i izvještavanje za svakodnevno poslovanje.'
                        : 'Accounting and tax advisory support, bookkeeping, payroll, tax compliance, and reporting for day-to-day business operations.',
                    'payload' => $this->encodePayload($payload),
                    'updated_at' => now(),
                ]);
        }
    }

    private function updateAdvisory(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        $pageId = DB::table('content_service_pages')
            ->where(fn ($query) => $query->where('code', 'advisory')->orWhere('template_key', 'advisory'))
            ->orderByRaw("CASE WHEN code = 'advisory' THEN 0 ELSE 1 END")
            ->value('id');
        if (! $pageId) {
            return;
        }

        $translations = DB::table('content_service_page_translations')
            ->where('service_page_id', $pageId)
            ->whereIn('locale', ['hr', 'en'])
            ->get(['id', 'locale', 'payload']);

        foreach ($translations as $translation) {
            $isCroatian = str_starts_with(strtolower((string) $translation->locale), 'hr');
            $payload = $this->decodePayload($translation->payload);
            $cards = is_array($payload['service_cards'] ?? null) ? $payload['service_cards'] : [];
            $payload['service_cards'] = array_values(array_filter(
                $cards,
                fn ($card): bool => ! is_array($card) || ! $this->isTaxAdvisoryItem($card),
            ));

            if ($isCroatian) {
                $overviewBody = data_get($payload, 'overview.body');
                if (is_array($overviewBody)) {
                    $overviewBody = array_map(
                        static fn ($paragraph) => is_string($paragraph)
                            ? str_replace(
                                ['Financijske, porezne i strateške', 'financijske, porezne i strateške'],
                                ['Financijske i strateške', 'financijske i strateške'],
                                $paragraph,
                            )
                            : $paragraph,
                        $overviewBody,
                    );
                    data_set($payload, 'overview.body', array_values($overviewBody));
                }
            }

            if (! $isCroatian && str_contains(
                strtolower((string) data_get($payload, 'hero.intro', '')),
                'tax',
            )) {
                data_set(
                    $payload,
                    'hero.intro',
                    'Advisory provides expert support in financial, strategic, and investment matters, helping companies, investors, and entrepreneurs make quality decisions, manage risk, and create long-term value.',
                );
            }

            DB::table('content_service_page_translations')
                ->where('id', $translation->id)
                ->update([
                    'meta_description' => $isCroatian
                        ? 'Financijsko i strateško savjetovanje, pribavljanje financiranja, due diligence, procjene vrijednosti i M&A savjetovanje.'
                        : 'Financial and strategic advisory, financing, due diligence, valuations, and M&A advisory.',
                    'payload' => $this->encodePayload($payload),
                    'updated_at' => now(),
                ]);
        }
    }

    private function updateHomeHeroSetting(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        DB::table('system_settings')
            ->where('key', 'store_home_hero_subtitle')
            ->update([
                'value' => json_encode(
                    'Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.',
                    self::JSON_OPTIONS,
                ),
                'updated_at' => now(),
            ]);
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, mixed>
     */
    private function accountingServiceItems(array $items, bool $isCroatian): array
    {
        $taxItem = null;
        $filtered = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                $filtered[] = $item;

                continue;
            }

            if ($this->isTaxAdvisoryItem($item)) {
                $taxItem ??= $item;

                continue;
            }

            $title = mb_strtolower(trim((string) ($item['title'] ?? '')));
            if (in_array($title, ['konsolidacija', 'consolidation'], true)) {
                continue;
            }

            $filtered[] = $item;
        }

        $taxItem ??= $isCroatian
            ? [
                'title' => 'Porezno savjetovanje',
                'text' => 'Podrška u poreznom planiranju, usklađenosti, poreznim pregledima, transfernim cijenama, poreznim nadzorima i transakcijama.',
            ]
            : [
                'title' => 'Tax Advisory',
                'text' => 'Support with tax planning, compliance, tax reviews, transfer pricing, tax audits and transactions.',
            ];

        $financialIndex = null;
        foreach ($filtered as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = mb_strtolower(trim((string) ($item['title'] ?? '')));
            if (in_array($title, ['financijsko računovodstvo', 'financial accounting'], true)) {
                $financialIndex = $index;

                break;
            }
        }

        array_splice($filtered, $financialIndex === null ? min(1, count($filtered)) : $financialIndex + 1, 0, [$taxItem]);

        return array_values($filtered);
    }

    /** @param array<string, mixed> $item */
    private function isTaxAdvisoryItem(array $item): bool
    {
        $title = mb_strtolower(trim((string) ($item['title'] ?? '')));
        $url = strtolower(trim((string) ($item['url'] ?? '')));

        return in_array($title, ['porezno savjetovanje', 'tax advisory'], true)
            || str_contains($url, 'porezno-savjetovanje')
            || str_contains($url, 'tax-advisory');
    }

    private function mutateContentBlockPayload(string $code, callable $mutator): void
    {
        if (! Schema::hasTable('content_blocks') || ! Schema::hasTable('content_block_translations')) {
            return;
        }

        $blockId = DB::table('content_blocks')->where('code', $code)->value('id');
        if (! $blockId) {
            return;
        }

        $translations = DB::table('content_block_translations')
            ->where('content_block_id', $blockId)
            ->whereIn('locale', ['hr', 'en'])
            ->get(['id', 'locale', 'payload']);

        foreach ($translations as $translation) {
            $payload = $mutator(
                $this->decodePayload($translation->payload),
                (string) $translation->locale,
            );

            DB::table('content_block_translations')
                ->where('id', $translation->id)
                ->update([
                    'payload' => $this->encodePayload($payload),
                    'updated_at' => now(),
                ]);
        }
    }

    /** @return array<string, mixed> */
    private function decodePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (! is_string($payload) || trim($payload) === '') {
            return [];
        }

        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $payload */
    private function encodePayload(array $payload): string
    {
        $encoded = json_encode($payload, self::JSON_OPTIONS);

        return is_string($encoded) ? $encoded : '{}';
    }
};
