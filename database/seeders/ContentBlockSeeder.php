<?php

namespace Database\Seeders;

use App\Models\Content\ContentBlock;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentBlockSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        $records = [
            [
                'code' => 'home-alpha-hero',
                'name' => 'Home Alpha Hero',
                'type' => 'home_hero',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'ALPHA CAPITALIS',
                        'subtitle' => 'Accounting and Tax Advisory, Audit and Advisory — all in one place.',
                        'cta_label' => 'Our services',
                        'cta_url' => '/usluge',
                        'payload' => [
                            'secondary_cta_label' => 'Book a meeting',
                            'secondary_cta_url' => '/contact',
                        ],
                    ],
                    'hr' => [
                        'title' => 'ALPHA CAPITALIS',
                        'subtitle' => 'Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.',
                        'cta_label' => 'Naše usluge',
                        'cta_url' => '/usluge',
                        'payload' => [
                            'secondary_cta_label' => 'Ugovori sastanak',
                            'secondary_cta_url' => '/contact',
                        ],
                    ],
                ],
            ],
            [
                'code' => 'home-alpha-stats',
                'name' => 'Home Alpha Stats',
                'type' => 'home_stats',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Home statistics',
                        'subtitle' => null,
                        'cta_label' => null,
                        'cta_url' => null,
                        'payload' => $this->homeStatsPayload('en'),
                    ],
                    'hr' => [
                        'title' => 'Home statistike',
                        'subtitle' => null,
                        'cta_label' => null,
                        'cta_url' => null,
                        'payload' => $this->homeStatsPayload('hr'),
                    ],
                ],
            ],
            [
                'code' => 'home-alpha-services',
                'name' => 'Home Alpha Services',
                'type' => 'home_services',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'We create value for our clients in',
                        'subtitle' => 'ALPHA CAPITALIS brings together experts in audit, accounting and tax advisory, and business advisory to support companies, investors and entrepreneurs through every stage of growth.',
                        'cta_label' => null,
                        'cta_url' => null,
                        'payload' => [
                            'title_accent' => 'every stage of business growth',
                            'services' => [
                                [
                                    'title' => 'Audit',
                                    'subtitle' => 'assurance and confidence in the numbers',
                                    'text' => 'Independent review of financial statements that increases confidence for owners, investors and partners.',
                                    'bullets' => [
                                        'We help owners, investors and management gain confidence in financial statements.',
                                        'Audit reduces the risk of wrong decisions by confirming that data is accurate, complete and compliant.',
                                        'Through independent review you gain a clear view of the company financial position, strengthening trust with banks, partners and regulators.',
                                    ],
                                    'url' => '/revizija',
                                    'action_label' => 'Learn more',
                                ],
                                [
                                    'title' => 'Accounting and Tax Advisory',
                                    'subtitle' => 'control, clarity and tax confidence',
                                    'text' => 'Accurate bookkeeping, timely reporting, and tax advisory for more confident business decisions.',
                                    'bullets' => [
                                        'We help keep your business financially organized, transparent and ready for decisions.',
                                        'That means accurate data on revenue, costs and results at any moment, without delays or uncertainty.',
                                        'Instead of reacting to problems, you can manage the business based on reliable information.',
                                    ],
                                    'url' => '/racunovodstvo',
                                    'action_label' => 'Learn more',
                                ],
                                [
                                    'title' => 'Advisory',
                                    'subtitle' => 'growth, optimization and better financial choices',
                                    'text' => 'Financial and strategic advisory plus capital raising - all in one place.',
                                    'bullets' => [
                                        'We help companies, investors and entrepreneurs make better decisions, manage risk and create long-term value.',
                                        'We support valuations, due diligence, M&A processes and financing structuring.',
                                        'EU funds, bank loans and tax incentives are connected within the capital raising framework.',
                                    ],
                                    'url' => '/savjetovanje',
                                    'action_label' => 'Learn more',
                                ],
                            ],
                        ],
                    ],
                    'hr' => [
                        'title' => 'Stvaramo vrijednost za naše klijente u',
                        'subtitle' => 'ALPHA CAPITALIS čini tim stručnjaka iz područja revizije, računovodstva i poreza te financijskog savjetovanja. Kroz zajedničko djelovanje pružamo cjelovita rješenja poduzećima, investitorima i poduzetnicima koji žele sigurno rasti.',
                        'cta_label' => null,
                        'cta_url' => null,
                        'payload' => [
                            'title_accent' => 'svim fazama razvoja poslovanja',
                            'services' => [
                                [
                                    'title' => 'Revizija',
                                    'subtitle' => 'sigurnost i povjerenje u brojke',
                                    'text' => 'Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.',
                                    'bullets' => [
                                        'Pomažemo vlasnicima, investitorima i upravi da imaju potpunu sigurnost u financijske izvještaje.',
                                        'Revizija smanjuje rizik pogrešnih odluka jer potvrđuje da su podaci točni, potpuni i u skladu s propisima.',
                                        'Kroz neovisnu provjeru dobivate jasnu sliku stvarnog financijskog stanja poduzeća, što jača povjerenje banaka, partnera i regulatora.',
                                    ],
                                    'url' => '/revizija',
                                    'action_label' => 'Detaljnije',
                                ],
                                [
                                    'title' => 'Računovodstvo i porezi',
                                    'subtitle' => 'kontrola, jasnoća i porezna sigurnost',
                                    'text' => 'Precizno vođenje knjiga, pravovremeno izvještavanje i porezno savjetovanje za sigurnije poslovne odluke.',
                                    'bullets' => [
                                        'Omogućujemo da vaše poslovanje bude financijski uredno, pregledno i uvijek spremno za odluke.',
                                        'To znači da u svakom trenutku imate točne podatke o prihodima, troškovima i rezultatu, bez kašnjenja i nejasnoća.',
                                        'Umjesto da reagirate na probleme, možete upravljati poslovanjem na temelju pouzdanih informacija.',
                                    ],
                                    'url' => '/racunovodstvo',
                                    'action_label' => 'Detaljnije',
                                ],
                                [
                                    'title' => 'Savjetovanje',
                                    'subtitle' => 'rast, optimizacija i bolji financijski izbor',
                                    'text' => 'Financijsko i strateško savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
                                    'bullets' => [
                                        'Pomažemo društvima, investitorima i poduzetnicima u donošenju kvalitetnih odluka, upravljanju rizicima i stvaranju dugoročne vrijednosti.',
                                        'Pružamo podršku u procjenama vrijednosti, due diligence postupcima, M&A procesima i strukturiranju financiranja.',
                                        'EU fondovi, bankovni krediti i porezne olakšice povezani su u okviru pribavljanja financiranja.',
                                    ],
                                    'url' => '/savjetovanje',
                                    'action_label' => 'Detaljnije',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'home-hero-main',
                'name' => 'Home Hero Main',
                'type' => 'desktop_hero_banner',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Modern essentials, built for everyday carry.',
                        'subtitle' => 'AGinfo blends practical design and clean structure for fast browsing.',
                        'cta_label' => 'Explore content',
                        'cta_url' => '/blog',
                    ],
                    'hr' => [
                        'title' => 'Modern essentials, built for everyday carry.',
                        'subtitle' => 'AGinfo spaja praktican dizajn i cistu strukturu za brzo pregledavanje sadrzaja.',
                        'cta_label' => 'Istrazi sadrzaj',
                        'cta_url' => '/blog',
                    ],
                ],
            ],
            [
                'code' => 'home-hero-benefits',
                'name' => 'Home Hero Benefits',
                'type' => 'hero_highlights_strip',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Why teams use this stack',
                        'subtitle' => 'Fast updates, clear structure, and reusable blocks.',
                        'cta_label' => null,
                        'cta_url' => null,
                    ],
                    'hr' => [
                        'title' => 'Zasto timovi koriste ovaj stack',
                        'subtitle' => 'Brze izmjene, jasna struktura i ponovno upotrebljivi blokovi.',
                        'cta_label' => null,
                        'cta_url' => null,
                    ],
                ],
            ],
            [
                'code' => 'home-mobile-hero',
                'name' => 'Home Mobile Hero',
                'type' => 'mobile_hero_banner',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Build and publish faster',
                        'subtitle' => 'Mobile-first hero for campaign and landing navigation.',
                        'cta_label' => 'Open blog',
                        'cta_url' => '/blog',
                    ],
                    'hr' => [
                        'title' => 'Brze izgradi i objavi',
                        'subtitle' => 'Mobile-first hero za kampanje i navigaciju prema landing stranicama.',
                        'cta_label' => 'Otvori blog',
                        'cta_url' => '/blog',
                    ],
                ],
            ],
            [
                'code' => 'home-blog-grid',
                'name' => 'Home Blog Grid',
                'type' => 'blog_grid_3',
                'is_active' => true,
                'payload' => [
                    'limit' => 3,
                ],
                'translations' => [
                    'en' => [
                        'title' => 'Latest posts',
                        'subtitle' => 'Fresh updates and practical guides.',
                        'cta_label' => 'View all posts',
                        'cta_url' => '/blog',
                    ],
                    'hr' => [
                        'title' => 'Najnovije objave',
                        'subtitle' => 'Svjeze novosti i prakticni vodici.',
                        'cta_label' => 'Pogledaj sve objave',
                        'cta_url' => '/blog',
                    ],
                ],
            ],
            [
                'code' => 'page-contact-cta',
                'name' => 'Page Contact CTA',
                'type' => 'cta_banner',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Need help planning your next page?',
                        'subtitle' => 'Send a message and we will reply with a clear implementation path.',
                        'cta_label' => 'Contact us',
                        'cta_url' => '/contact',
                    ],
                    'hr' => [
                        'title' => 'Trebas pomoc oko iduce stranice?',
                        'subtitle' => 'Posalji poruku i vracamo se s jasnim planom implementacije.',
                        'cta_label' => 'Kontaktiraj nas',
                        'cta_url' => '/contact',
                    ],
                ],
            ],
        ];

        foreach ($records as $record) {
            $block = ContentBlock::query()->updateOrCreate(
                ['code' => $record['code']],
                [
                    'name' => $record['name'],
                    'type' => $record['type'],
                    'is_active' => (bool) $record['is_active'],
                    'payload' => $record['payload'] ?? null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            foreach ($record['translations'] as $locale => $translation) {
                $block->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $translation['title'] ?? null,
                        'subtitle' => $translation['subtitle'] ?? null,
                        'body_html' => null,
                        'cta_label' => $translation['cta_label'] ?? null,
                        'cta_url' => $translation['cta_url'] ?? null,
                        'payload' => $translation['payload'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function homeStatsPayload(string $locale): array
    {
        $isCroatian = $locale === 'hr';
        $stats = $isCroatian
            ? [
                ['value' => '300', 'suffix' => '+', 'label' => 'Odrađenih projekata'],
                ['value' => '700', 'suffix' => '', 'label' => 'Redovnih klijenata'],
                ['value' => '75', 'suffix' => '', 'label' => 'Kvalificiranih stručnjaka'],
            ]
            : [
                ['value' => '300', 'suffix' => '+', 'label' => 'Completed projects'],
                ['value' => '700', 'suffix' => '', 'label' => 'Regular clients'],
                ['value' => '75', 'suffix' => '', 'label' => 'Qualified professionals'],
            ];

        return [
            'stats' => $stats,
            'locations' => [
                'items' => [
                    [
                        'city' => $isCroatian ? 'Zagreb – HQ ured' : 'Zagreb — Headquarters',
                        'email' => 'info@alphacapitalis.com',
                        'phone' => '+385 (1) 580 6656',
                        'number' => '01 · HQ',
                        'address' => $isCroatian
                            ? 'Ulica R. F. Mihanovića 9, 10110 Zagreb, Sky Office / XIX. kat'
                            : 'R. F. Mihanovića Street 9, 10110 Zagreb, Sky Office / 19th floor',
                        'company' => 'ALPHA CAPITALIS d.o.o.',
                        'map_query' => $isCroatian
                            ? 'Ulica R. F. Mihanovića 9, 10110 Zagreb, Sky Office'
                            : 'R. F. Mihanovića Street 9, 10110 Zagreb, Sky Office',
                        'entity_key' => 'alpha-capitalis',
                        'short_city' => 'Zagreb',
                        'office_label' => $isCroatian ? 'Ured Zagreb' : 'Zagreb Office',
                        'coordinates_label' => 'ZAGREB · 45.80° N · 15.91° E',
                        'marker_aria_label' => $isCroatian
                            ? 'Prikaži kontaktne podatke za ured Zagreb'
                            : 'Show contact details for the Zagreb office',
                    ],
                    [
                        'city' => 'Rijeka',
                        'email' => 'info@alphacapitalis.com',
                        'phone' => '+385 (0) 51 301 503',
                        'number' => '02',
                        'address' => 'Korzo 30, 51000 Rijeka',
                        'company' => 'ALPHA CAPITALIS TIMIA d.o.o.',
                        'map_query' => 'Korzo 30, 51000 Rijeka',
                        'entity_key' => 'alpha-capitalis-timia',
                        'short_city' => 'Rijeka',
                        'office_label' => $isCroatian ? 'Ured Rijeka' : 'Rijeka Office',
                        'coordinates_label' => 'RIJEKA · 45.33° N · 14.44° E',
                        'marker_aria_label' => $isCroatian
                            ? 'Prikaži kontaktne podatke za ured Rijeka'
                            : 'Show contact details for the Rijeka office',
                    ],
                    [
                        'city' => 'Vinkovci',
                        'email' => 'info@alphacapitalis.com',
                        'phone' => '+385 (1) 580 6656',
                        'number' => '03',
                        'address' => $isCroatian
                            ? 'Duga ulica 67, 32100 Vinkovci'
                            : 'Duga Street 67, 32100 Vinkovci',
                        'company' => 'ALPHA CAPITALIS EAST d.o.o.',
                        'map_query' => $isCroatian
                            ? 'Duga ulica 67, 32100 Vinkovci'
                            : 'Duga Street 67, 32100 Vinkovci',
                        'entity_key' => 'alpha-capitalis-east',
                        'short_city' => 'Vinkovci',
                        'office_label' => $isCroatian ? 'Ured Vinkovci' : 'Vinkovci Office',
                        'coordinates_label' => 'VINKOVCI · 45.29° N · 18.80° E',
                        'marker_aria_label' => $isCroatian
                            ? 'Prikaži kontaktne podatke za ured Vinkovci'
                            : 'Show contact details for the Vinkovci office',
                    ],
                ],
                'title' => $isCroatian ? 'Prisutni na 3 lokacije' : 'Presence across 3 locations',
                'intro_lead' => $isCroatian
                    ? 'Zagreb, Rijeka i Vinkovci'
                    : 'Zagreb, Rijeka and Vinkovci',
                'intro_text' => $isCroatian
                    ? '— podrška klijentima diljem Hrvatske.'
                    : '— supporting clients across Croatia.',
                'email_label' => 'Email',
                'phone_label' => $isCroatian ? 'Telefon' : 'Phone',
                'region_label' => $isCroatian ? 'HR / 3 UREDA' : 'CROATIA / 3 OFFICES',
                'map_image_alt' => $isCroatian
                    ? 'Karta Hrvatske s uredima u Zagrebu, Rijeci i Vinkovcima'
                    : 'Map of Croatia showing our offices in Zagreb, Rijeka and Vinkovci',
                'map_aria_label' => $isCroatian
                    ? 'Karta lokacija u Hrvatskoj'
                    : 'Map of our locations in Croatia',
                'map_link_label' => $isCroatian ? 'Pogledaj na karti' : 'View on map',
                'hero_aria_label' => $isCroatian ? 'Naše lokacije' : 'Our locations',
                'stats_aria_label' => $isCroatian
                    ? 'Alpha Capitalis u brojkama'
                    : 'Alpha Capitalis in numbers',
            ],
            'blog_source' => 'latest',
            'items_limit' => 6,
            'contact_page' => $isCroatian
                ? [
                    'intro' => 'Stojimo vam na raspolaganju za financije, računovodstvo, reviziju, poreze i poslovno savjetovanje. Javite nam se putem obrasca ili direktno uredima u Zagrebu, Vinkovcima i Rijeci.',
                    'help_body' => 'U poruci navedite temu, tvrtku i područje interesa kako bismo vam se mogli javiti s konkretnijim prijedlogom sljedećeg koraka.',
                    'form_intro' => 'Ukratko opišite temu upita i ostavite svoje kontakt podatke. Javit ćemo vam se u najkraćem mogućem roku.',
                    'form_title' => 'Pošaljite nam poruku',
                    'help_title' => 'Prije slanja upita',
                    'name_label' => 'Ime i prezime',
                    'page_title' => 'Kontaktirajte nas',
                    'direct_body' => 'Za inicijalne informacije, dogovor oko sastanka ili brže usmjeravanje upita, javite nam se izravno.',
                    'email_label' => 'Email',
                    'phone_label' => 'Telefon (opcionalno)',
                    'sent_status' => 'Hvala. Vaša poruka je uspješno poslana.',
                    'direct_email' => 'info@alphacapitalis.com',
                    'direct_phone' => '+385 (1) 580 6656',
                    'direct_title' => 'Direktan kontakt',
                    'submit_label' => 'Pošalji poruku',
                    'consent_label' => 'Slažem se s GDPR privolom i obradom osobnih podataka.',
                    'message_label' => 'Poruka',
                    'subject_label' => 'Naslov',
                    'direct_email_label' => 'Email',
                    'direct_phone_label' => 'Telefon',
                    'direct_response_fallback' => 'Unutar radnog vremena',
                    'direct_response_time_label' => 'Vrijeme odgovora',
                ]
                : [
                    'intro' => 'We are here to support you with finance, accounting, audit, tax and business advisory. Get in touch using the contact form or contact our offices in Zagreb, Vinkovci and Rijeka directly.',
                    'help_body' => 'Include the topic, company name, and area of interest so we can reply with a more relevant next step.',
                    'form_intro' => 'Briefly describe the nature of your enquiry and leave your contact details. We will get back to you as soon as possible.',
                    'form_title' => 'Send us a message',
                    'help_title' => 'Before you send',
                    'name_label' => 'Full name',
                    'page_title' => 'Contact us',
                    'direct_body' => 'For initial information, arranging a meeting, or faster inquiry routing, contact us directly.',
                    'email_label' => 'Email',
                    'phone_label' => 'Phone (optional)',
                    'sent_status' => 'Thanks. Your message has been sent successfully.',
                    'direct_email' => 'info@alphacapitalis.com',
                    'direct_phone' => '+385 (1) 580 6656',
                    'direct_title' => 'Direct contact',
                    'submit_label' => 'Send message',
                    'consent_label' => 'I agree with GDPR consent and personal data processing.',
                    'message_label' => 'Message',
                    'subject_label' => 'Subject',
                    'direct_email_label' => 'Email',
                    'direct_phone_label' => 'Phone',
                    'direct_response_fallback' => 'Within business hours',
                    'direct_response_time_label' => 'Response time',
                ],
            'contact_stats' => $stats,
        ];
    }
}
