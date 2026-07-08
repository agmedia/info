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
                        'subtitle' => 'YOUR COMPASS THROUGH THE WORLD OF FINANCE',
                        'cta_label' => 'Our services',
                        'cta_url' => '/usluge',
                        'payload' => [
                            'secondary_cta_label' => 'Book a meeting',
                            'secondary_cta_url' => '/contact',
                        ],
                    ],
                    'hr' => [
                        'title' => 'ALPHA CAPITALIS',
                        'subtitle' => 'VAŠ KOMPAS KROZ SVIJET FINANCIJA',
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
                        'payload' => [
                            'stats' => [
                                ['value' => '300', 'suffix' => '+', 'label' => 'Completed projects'],
                                ['value' => '600', 'suffix' => '+', 'label' => 'Recurring clients'],
                                ['value' => '60', 'suffix' => '+', 'label' => 'Qualified experts'],
                            ],
                        ],
                    ],
                    'hr' => [
                        'title' => 'Home statistike',
                        'subtitle' => null,
                        'cta_label' => null,
                        'cta_url' => null,
                        'payload' => [
                            'stats' => [
                                ['value' => '300', 'suffix' => '+', 'label' => 'Odrađenih projekata'],
                                ['value' => '600', 'suffix' => '+', 'label' => 'Redovnih klijenata'],
                                ['value' => '60', 'suffix' => '+', 'label' => 'Kvalificiranih stručnjaka'],
                            ],
                        ],
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
                        'subtitle' => 'ALPHA CAPITALIS brings together experts in audit, accounting and financial advisory to support companies, investors and entrepreneurs through every stage of growth.',
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
                                    'title' => 'Accounting',
                                    'subtitle' => 'control and clarity of operations',
                                    'text' => 'Precise bookkeeping and timely reporting that frees management for strategic decisions.',
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
                                    'text' => 'Financial and tax advisory plus capital raising - all in one place.',
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
                        'subtitle' => 'ALPHA CAPITALIS čini tim stručnjaka iz područja revizije, računovodstva i financijskog savjetovanja. Kroz zajedničko djelovanje pružamo cjelovita rješenja poduzećima, investitorima i poduzetnicima koji žele sigurno rasti.',
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
                                    'title' => 'Računovodstvo',
                                    'subtitle' => 'kontrola i jasnoća poslovanja',
                                    'text' => 'Precizno vođenje knjiga i pravovremeno izvještavanje koje oslobađa menadžment za strateške odluke.',
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
                                    'text' => 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
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
}
