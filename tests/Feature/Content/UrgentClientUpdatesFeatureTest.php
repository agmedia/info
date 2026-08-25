<?php

namespace Tests\Feature\Content;

use Database\Seeders\ContentBlockSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UrgentClientUpdatesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_block_seeder_contains_complete_localized_contact_payload(): void
    {
        $this->seed(ContentBlockSeeder::class);

        $statsBlockId = DB::table('content_blocks')->where('code', 'home-alpha-stats')->value('id');
        $expectedOfficeLabels = [
            'hr' => [
                'alpha-capitalis' => 'Ured Zagreb',
                'alpha-capitalis-east' => 'Ured Vinkovci',
                'alpha-capitalis-timia' => 'Ured Rijeka',
            ],
            'en' => [
                'alpha-capitalis' => 'Zagreb Office',
                'alpha-capitalis-east' => 'Vinkovci Office',
                'alpha-capitalis-timia' => 'Rijeka Office',
            ],
        ];
        $expectedPhones = [
            'alpha-capitalis' => '+385 (1) 580 6656',
            'alpha-capitalis-east' => '+385 (1) 580 6656',
            'alpha-capitalis-timia' => '+385 (0) 51 301 503',
        ];

        foreach (['hr', 'en'] as $locale) {
            $payload = $this->decodePayload(DB::table('content_block_translations')
                ->where('content_block_id', $statsBlockId)
                ->where('locale', $locale)
                ->value('payload'));

            $this->assertCount(3, $payload['stats']);
            $this->assertSame(['300+', '700', '75'], array_map(
                static fn (array $stat): string => $stat['value'].$stat['suffix'],
                $payload['stats'],
            ));
            $this->assertSame($payload['stats'], $payload['contact_stats']);
            $this->assertSame('latest', $payload['blog_source']);
            $this->assertSame(6, $payload['items_limit']);

            foreach ([
                'title',
                'intro_lead',
                'intro_text',
                'email_label',
                'phone_label',
                'region_label',
                'map_image_alt',
                'map_aria_label',
                'map_link_label',
                'hero_aria_label',
                'stats_aria_label',
                'items',
            ] as $locationKey) {
                $this->assertArrayHasKey($locationKey, $payload['locations']);
            }

            $offices = collect($payload['locations']['items'])->keyBy('entity_key');
            $this->assertCount(3, $offices);
            foreach ($expectedPhones as $entityKey => $phone) {
                foreach ([
                    'city',
                    'email',
                    'phone',
                    'number',
                    'address',
                    'company',
                    'map_query',
                    'entity_key',
                    'short_city',
                    'office_label',
                    'coordinates_label',
                    'marker_aria_label',
                ] as $officeKey) {
                    $this->assertArrayHasKey($officeKey, $offices[$entityKey]);
                }

                $this->assertSame($phone, data_get($offices, $entityKey.'.phone'));
                $this->assertSame(
                    $expectedOfficeLabels[$locale][$entityKey],
                    data_get($offices, $entityKey.'.office_label'),
                );
            }

            foreach ([
                'page_title',
                'intro',
                'form_title',
                'form_intro',
                'name_label',
                'email_label',
                'phone_label',
                'subject_label',
                'message_label',
                'consent_label',
                'submit_label',
                'direct_title',
                'direct_body',
                'direct_email',
                'direct_phone',
                'direct_email_label',
                'direct_phone_label',
                'direct_response_time_label',
                'direct_response_fallback',
                'help_title',
                'help_body',
                'sent_status',
            ] as $contactKey) {
                $this->assertArrayHasKey($contactKey, $payload['contact_page']);
            }

            $this->assertSame('info@alphacapitalis.com', data_get($payload, 'contact_page.direct_email'));
            $this->assertSame('+385 (1) 580 6656', data_get($payload, 'contact_page.direct_phone'));
        }
    }

    public function test_urgent_contact_stats_and_service_updates_are_complete_and_idempotent(): void
    {
        $this->seed(ContentBlockSeeder::class);
        $this->restoreLegacyHomepageData();
        $this->restoreLegacyServiceData();

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'store_home_hero_subtitle'],
            [
                'value' => json_encode('Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.', JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        $migration = require database_path('migrations/2026_08_25_150000_apply_urgent_contact_and_service_updates.php');
        $migration->up();
        $migration->up();

        $this->assertHomepageAndContactData();
        $this->assertServiceData();
    }

    private function restoreLegacyHomepageData(): void
    {
        $statsBlockId = DB::table('content_blocks')->where('code', 'home-alpha-stats')->value('id');

        foreach (['hr', 'en'] as $locale) {
            $translation = DB::table('content_block_translations')
                ->where('content_block_id', $statsBlockId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);
            $payload = $this->decodePayload($translation?->payload);
            $payload['stats'][] = [
                'label' => $locale === 'hr' ? 'Godina iskustva' : 'Years of experience',
                'value' => '20',
                'suffix' => '+',
            ];
            $payload['contact_stats'] = [
                ['label' => $locale === 'hr' ? 'Odrađenih projekata' : 'Completed projects', 'value' => '300', 'suffix' => '+'],
                ['label' => $locale === 'hr' ? 'Redovnih klijenata' : 'Regular clients', 'value' => '600', 'suffix' => '+'],
                ['label' => $locale === 'hr' ? 'Kvalificiranih stručnjaka' : 'Qualified professionals', 'value' => '60', 'suffix' => '+'],
                ['label' => $locale === 'hr' ? 'Godina iskustva' : 'Years of experience', 'value' => '20', 'suffix' => '+'],
            ];
            $payload['locations']['items'] = [
                ['entity_key' => 'alpha-capitalis', 'office_label' => 'Legacy Zagreb', 'phone' => '+385 1 000 0000'],
                ['entity_key' => 'alpha-capitalis-east', 'office_label' => 'Legacy Vinkovci', 'phone' => '+385 1 000 0001'],
                ['entity_key' => 'alpha-capitalis-timia', 'office_label' => 'Legacy Rijeka', 'phone' => '+385 51 000 000'],
            ];
            $payload['contact_page']['direct_phone'] = '+385 51 000 000';

            DB::table('content_block_translations')->where('id', $translation?->id)->update([
                'payload' => $this->encodePayload($payload),
            ]);
        }
    }

    private function restoreLegacyServiceData(): void
    {
        $accountingPageId = DB::table('content_service_pages')->where('code', 'racunovodstvo')->value('id');
        $advisoryPageId = DB::table('content_service_pages')->where('code', 'advisory')->value('id');

        foreach (['hr', 'en'] as $locale) {
            $accounting = DB::table('content_service_page_translations')
                ->where('service_page_id', $accountingPageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);
            $accountingPayload = $this->decodePayload($accounting?->payload);
            $items = array_values(data_get($accountingPayload, 'services.items', []));
            $items[0]['text'] = 'Editor financial copy '.$locale;
            $items[0]['editor_note'] = 'keep-financial-'.$locale;
            $items[1]['text'] = 'Editor tax copy '.$locale;
            $items[1]['editor_note'] = 'keep-tax-'.$locale;
            $items[] = [
                'title' => $locale === 'hr' ? 'Konsolidacija' : 'Consolidation',
                'text' => 'Remove this card',
                'editor_note' => 'remove-consolidation-'.$locale,
            ];
            $items[] = [
                'title' => 'Editor duplicate tax '.$locale,
                'url' => $locale === 'hr'
                    ? '/savjetovanje/porezno-savjetovanje'
                    : '/advisory/tax-advisory',
                'editor_note' => 'remove-duplicate-'.$locale,
            ];
            data_set($accountingPayload, 'services.items', $items);

            DB::table('content_service_page_translations')->where('id', $accounting?->id)->update([
                'title' => $locale === 'hr' ? 'Računovodstvo' : 'Accounting',
                'payload' => $this->encodePayload($accountingPayload),
            ]);

            $advisory = DB::table('content_service_page_translations')
                ->where('service_page_id', $advisoryPageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);
            $advisoryPayload = $this->decodePayload($advisory?->payload);
            $advisoryPayload['service_cards'][] = [
                'title' => $locale === 'hr' ? 'Porezno savjetovanje' : 'Tax Advisory',
                'url' => $locale === 'hr'
                    ? '/savjetovanje/porezno-savjetovanje'
                    : '/advisory/tax-advisory',
            ];
            $advisoryPayload['service_cards'][] = [
                'title' => 'Editor duplicate tax '.$locale,
                'url' => $locale === 'hr'
                    ? '/savjetovanje/porezno-savjetovanje/custom'
                    : '/advisory/tax-advisory/custom',
            ];
            if ($locale === 'hr') {
                data_set($advisoryPayload, 'overview.body.0', 'Financijske, porezne i strateške odluke traže stručnu perspektivu.');
            } else {
                data_set($advisoryPayload, 'hero.intro', 'Financial, tax, and investment advisory copy.');
            }

            DB::table('content_service_page_translations')->where('id', $advisory?->id)->update([
                'payload' => $this->encodePayload($advisoryPayload),
            ]);
        }
    }

    private function assertHomepageAndContactData(): void
    {
        $statsBlockId = DB::table('content_blocks')->where('code', 'home-alpha-stats')->value('id');

        foreach (['hr', 'en'] as $locale) {
            $payload = $this->decodePayload(DB::table('content_block_translations')
                ->where('content_block_id', $statsBlockId)
                ->where('locale', $locale)
                ->value('payload'));

            $this->assertCount(3, $payload['stats']);
            $this->assertCount(3, $payload['contact_stats']);
            $this->assertSame(['300+', '700', '75'], array_map(
                static fn (array $stat): string => $stat['value'].$stat['suffix'],
                $payload['stats'],
            ));
            $this->assertSame($payload['stats'], $payload['contact_stats']);
            $this->assertSame('+385 (1) 580 6656', data_get($payload, 'contact_page.direct_phone'));

            $offices = collect(data_get($payload, 'locations.items', []))->keyBy('entity_key');
            $this->assertSame('+385 (1) 580 6656', data_get($offices, 'alpha-capitalis.phone'));
            $this->assertSame('+385 (1) 580 6656', data_get($offices, 'alpha-capitalis-east.phone'));
            $this->assertSame('+385 (0) 51 301 503', data_get($offices, 'alpha-capitalis-timia.phone'));
        }

        $this->assertSame(
            '"Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu."',
            DB::table('system_settings')->where('key', 'store_home_hero_subtitle')->value('value'),
        );
    }

    private function assertServiceData(): void
    {
        $accountingPageId = DB::table('content_service_pages')->where('code', 'racunovodstvo')->value('id');
        $advisoryPageId = DB::table('content_service_pages')->where('code', 'advisory')->value('id');
        $servicesPageId = DB::table('content_service_pages')->where('code', 'services')->value('id');

        $expectedAccountingItems = [
            'hr' => [
                'Financijsko računovodstvo',
                'Porezno savjetovanje',
                'Obračun plaća',
                'Porezne prijave',
                'Upravljačko izvještavanje',
                'Osnivanje i registracija',
            ],
            'en' => [
                'Financial Accounting',
                'Tax Advisory',
                'Payroll',
                'Tax Returns',
                'Management Reporting',
                'Company Formation & Registration',
            ],
        ];

        foreach (['hr', 'en'] as $locale) {
            $accounting = DB::table('content_service_page_translations')
                ->where('service_page_id', $accountingPageId)
                ->where('locale', $locale)
                ->first(['title', 'payload']);
            $accountingPayload = $this->decodePayload($accounting?->payload);
            $this->assertSame(
                $locale === 'hr' ? 'Računovodstvo i porezi' : 'Accounting and Tax Advisory',
                $accounting?->title,
            );
            $this->assertSame(
                $expectedAccountingItems[$locale],
                array_column(data_get($accountingPayload, 'services.items', []), 'title'),
            );
            $this->assertSame(
                'keep-financial-'.$locale,
                data_get($accountingPayload, 'services.items.0.editor_note'),
            );
            $this->assertSame(
                'keep-tax-'.$locale,
                data_get($accountingPayload, 'services.items.1.editor_note'),
            );
            $this->assertSame(
                'Editor tax copy '.$locale,
                data_get($accountingPayload, 'services.items.1.text'),
            );

            $advisoryPayload = $this->decodePayload(DB::table('content_service_page_translations')
                ->where('service_page_id', $advisoryPageId)
                ->where('locale', $locale)
                ->value('payload'));
            $this->assertCount(4, $advisoryPayload['service_cards']);
            $this->assertNotContains(
                $locale === 'hr' ? 'Porezno savjetovanje' : 'Tax Advisory',
                array_column($advisoryPayload['service_cards'], 'title'),
            );
            $this->assertStringNotContainsString(
                $locale === 'hr' ? 'porezne i strateške' : 'tax',
                strtolower((string) ($locale === 'hr'
                    ? data_get($advisoryPayload, 'overview.body.0')
                    : data_get($advisoryPayload, 'hero.intro'))),
            );

            $servicesPayload = $this->decodePayload(DB::table('content_service_page_translations')
                ->where('service_page_id', $servicesPageId)
                ->where('locale', $locale)
                ->value('payload'));
            $accountingPillar = collect($servicesPayload['primary_pillars'])->firstWhere('key', 'accounting');
            $this->assertSame(
                $locale === 'hr' ? 'Računovodstvo i porezi' : 'Accounting and Tax Advisory',
                $accountingPillar['title'] ?? null,
            );
        }
    }

    /** @return array<string, mixed> */
    private function decodePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        $decoded = is_string($payload) ? json_decode($payload, true) : null;

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $payload */
    private function encodePayload(array $payload): string
    {
        return (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
