<?php

namespace App\Support\Content;

class EuFundsServicePageDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function defaultsForLocale(string $locale): array
    {
        return self::isCroatian($locale)
            ? self::croatianDefaults()
            : self::englishDefaults();
    }

    /**
     * @return array<string, mixed>
     */
    private static function croatianDefaults(): array
    {
        return [
            'hero' => [
                'brand_title' => 'ALPHA CAPITALIS',
                'subtitle_lead' => 'EU fondovi',
                'subtitle_accent' => '',
                'intro' => 'Pomažemo poduzetnicima u identifikaciji, prijavi i provedbi projekata financiranih iz EU i nacionalnih izvora — od odabira natječaja do ishođenja ugovora i administrativnog praćenja provedbe.',
            ],
            'overview' => [
                'kicker' => 'EU FONDOVI',
                'title' => 'Što su EU fondovi?',
                'intro' => '',
                'body' => [
                    'EU fondovi su instrumenti financiranja kojima Europska unija podupire razvoj poduzetništva, inovacija, digitalne i zelene tranzicije te infrastrukture. Poduzetnicima su na raspolaganju bespovratna sredstva iz Višegodišnjeg financijskog okvira 2021.–2027., a uz EU programe dostupni su i nacionalni instrumenti potpore — HBOR krediti, HAMAG zajmovi te porezne olakšice temeljem Zakona o poticanju ulaganja i istraživačko-razvojnim projektima.',
                ],
            ],
            'process' => [
                'kicker' => 'USLUGE',
                'title' => 'Naše usluge',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Analiza i odabir natječaja',
                        'text' => 'Analiziramo vaše investicijske i razvojne prioritete te ih povezujemo s odgovarajućim natječajima, kreditnim linijama i instrumentima potpore.',
                    ],
                    [
                        'title' => 'Izrada projektne prijave',
                        'text' => 'Pripremamo projektnu logiku, proračun, obrasce i svu prateću dokumentaciju potrebnu za kvalitetnu i pravodobnu prijavu.',
                    ],
                    [
                        'title' => 'Provedba i koordinacija projekta',
                        'text' => 'Nakon odobrenja sredstava preuzimamo administrativno praćenje, komunikaciju s tijelima, izvještavanje i projektnu koordinaciju.',
                    ],
                    [
                        'title' => 'Financijski instrumenti',
                        'text' => 'Savjetujemo o HBOR kreditima, HAMAG zajmovima i ostalim instrumentima koji mogu biti komplementarni bespovratnim sredstvima.',
                    ],
                    [
                        'title' => 'Zakonski poticaji i olakšice',
                        'text' => 'Pomažemo pri korištenju Zakona o poticanju ulaganja, Uredbe o turizmu i Zakona o državnoj potpori za istraživačko-razvojne projekte.',
                    ],
                    [
                        'title' => 'Praćenje natječaja',
                        'text' => 'Kontinuirano pratimo nove pozive i pravovremeno vas obavještavamo o prilikama relevantnim za vaše poslovanje.',
                    ],
                ],
            ],
            'approach' => [
                'kicker' => 'PRISTUP',
                'title' => 'Naš pristup',
                'body' => [
                    'Svaki angažman započinjemo razumijevanjem vaših investicijskih i razvojnih ciljeva. Analiziramo prihvatljivost projekta, dostupne izvore financiranja, rokove, uvjete natječaja i potrebnu dokumentaciju kako bismo procijenili realne mogućnosti prijave.',
                    'Nakon odabira odgovarajućeg izvora financiranja, vodimo klijenta kroz pripremu projektne prijave, komunikaciju s nadležnim tijelima i administrativno praćenje provedbe. Cilj je povećati kvalitetu prijave, smanjiti rizik pogrešaka i omogućiti klijentu jasniji, sigurniji proces financiranja.',
                ],
            ],
            'source_modules' => [
                'kicker' => 'IZVORI FINANCIRANJA',
                'title' => 'Dostupni izvori financiranja',
                'intro' => 'Natječaji, financijski instrumenti i porezne olakšice prikazani su odvojeno od opisa usluge kako biste lakše pronašli relevantan izvor financiranja.',
                'items' => [
                    [
                        'title' => 'Otvoreni natječaji',
                        'text' => 'Pregled trenutno otvorenih poziva s jasno označenim statusom i dostupnim detaljima.',
                        'url' => '#eu-funds-calls-open',
                    ],
                    [
                        'title' => 'Natječaji u najavi',
                        'text' => 'Pozivi koji su najavljeni i mogu biti relevantni za planiranje budućih ulaganja.',
                        'url' => '#eu-funds-calls-pending',
                    ],
                    [
                        'title' => 'Zatvoreni natječaji',
                        'text' => 'Arhivirani pozivi i programi koji mogu poslužiti kao orijentir za buduće cikluse financiranja.',
                        'url' => '#eu-funds-calls-closed',
                    ],
                    [
                        'title' => 'Financijski instrumenti',
                        'text' => 'HBOR, HAMAG i drugi instrumenti koji mogu biti dopuna bespovratnim sredstvima.',
                        'url' => '#eu-funds-programs',
                    ],
                    [
                        'title' => 'Porezne olakšice',
                        'text' => 'Poticaji, zakoni i uredbe koje mogu povećati isplativost planiranih ulaganja.',
                        'url' => '#eu-funds-laws',
                    ],
                    [
                        'title' => 'Bankovni krediti',
                        'text' => 'Kreditni izvori financiranja koji se mogu kombinirati s EU i nacionalnim potporama.',
                        'url' => '#eu-funds-programs',
                    ],
                ],
            ],
            'calls' => [
                'kicker' => 'NATJEČAJI',
                'title' => 'Natječaji prema statusu',
                'intro' => 'Aktualni pozivi povlače se iz postojeće baze natječaja i prikazuju odvojeno od opisa savjetodavne usluge.',
                'download_link' => [
                    'label' => '',
                    'type' => 'none',
                ],
                'groups' => [],
            ],
            'resources' => [
                'kicker' => 'PROGRAMI I INSTRUMENTI',
                'title' => 'HBOR, HAMAG i ostali izvori potpore',
                'intro' => 'Uz bespovratna sredstva poduzetnicima su dostupni i krediti, zajmovi te drugi instrumenti koji mogu biti komplementarni investicijskom planu.',
                'cards' => [
                    [
                        'eyebrow' => 'ALPHA CAPITALIS',
                        'title' => 'Projektni upitnik',
                        'body' => [
                            'Prvi korak je razumjeti investicijske i razvojne prioritete projekta. Upitnik nam pomaže procijeniti prihvatljivost ulaganja i povezati ga s dostupnim izvorima financiranja.',
                        ],
                        'primary_link' => [
                            'label' => 'Ispuni upitnik',
                            'type' => 'external',
                            'url' => '/eu-fondovi/upitnik',
                        ],
                    ],
                    [
                        'eyebrow' => 'HBOR',
                        'title' => 'HBOR krediti',
                        'body' => [
                            'HBOR kreditni programi mogu biti izvor financiranja za ulaganja, modernizaciju proizvodnje, energetsku učinkovitost, održivi turizam i druge razvojne projekte.',
                        ],
                        'groups' => [
                            [
                                'label' => 'Primjena',
                                'items' => [
                                    ['title' => 'kreditiranje investicija i modernizacije poslovanja', 'link' => ['type' => 'none']],
                                    ['title' => 'kombiniranje kredita s bespovratnim sredstvima kada je primjenjivo', 'link' => ['type' => 'none']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'eyebrow' => 'HAMAG-BICRO',
                        'title' => 'HAMAG zajmovi',
                        'body' => [
                            'HAMAG-BICRO zajmovi i jamstva mogu biti korisna dopuna financijskoj strukturi, posebno za male i srednje poduzetnike koji planiraju provedbu ulaganja.',
                        ],
                        'groups' => [
                            [
                                'label' => 'Primjena',
                                'items' => [
                                    ['title' => 'zajmovi i jamstva za poduzetničke investicije', 'link' => ['type' => 'none']],
                                    ['title' => 'dopunsko financiranje provedbe projekta', 'link' => ['type' => 'none']],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'laws' => [
                'kicker' => 'ZAKONI I UREDBE',
                'title' => 'Porezne olakšice, zakoni i uredbe',
                'intro' => 'Porezne olakšice i državne potpore mogu biti važan dio ukupne financijske konstrukcije investicijskog projekta.',
                'cards' => [
                    [
                        'title' => 'Zakon o poticanju ulaganja',
                        'summary' => 'Pomažemo pri analizi uvjeta za korištenje poreznih olakšica i potpora za nova ulaganja.',
                        'lists' => [
                            [
                                'label' => 'Najčešća područja provjere',
                                'items' => [
                                    'prihvatljivost prijavitelja i investicijskog projekta',
                                    'visina ulaganja i planirana nova radna mjesta',
                                    'razdoblje korištenja poreznih pogodnosti',
                                ],
                            ],
                        ],
                        'primary_link' => [
                            'label' => 'Više informacija',
                            'type' => 'blog',
                            'slug' => 'zakon-o-poticanju-ulaganja-2',
                        ],
                        'secondary_link' => [
                            'label' => 'Brošura',
                            'type' => 'pdf',
                            'path' => 'front-theme/documents/eu-fondovi/zakon-o-poticanju-ulaganja-brosura.pdf',
                        ],
                    ],
                    [
                        'title' => 'Uredba o turizmu',
                        'summary' => 'Analiziramo mogućnosti korištenja potpora za ulaganja u turističke djelatnosti kada su primjenjive na konkretan projekt.',
                        'lists' => [
                            [
                                'label' => 'Fokus potpore',
                                'items' => [
                                    'održivi, inovativni i otporni turizam',
                                    'zelena i digitalna tranzicija',
                                    'razvoj turističkog proizvoda visoke dodane vrijednosti',
                                ],
                            ],
                        ],
                        'primary_link' => [
                            'label' => 'Narodne novine',
                            'type' => 'external',
                            'url' => 'https://narodne-novine.nn.hr/clanci/sluzbeni/2024_04_39_678.html',
                        ],
                    ],
                    [
                        'title' => 'Istraživačko-razvojni projekti',
                        'summary' => 'Savjetujemo o korištenju državne potpore za istraživačko-razvojne projekte i povezanim poreznim olakšicama.',
                        'lists' => [
                            [
                                'label' => 'Područja analize',
                                'items' => [
                                    'vrsta istraživačko-razvojne aktivnosti',
                                    'prihvatljivi troškovi projekta',
                                    'dokumentacija potrebna za korištenje potpore',
                                ],
                            ],
                        ],
                        'primary_link' => [
                            'label' => 'Zakon o izmjenama i dopunama',
                            'type' => 'external',
                            'url' => 'https://narodne-novine.nn.hr/clanci/sluzbeni/2024_12_152_2519.html',
                        ],
                    ],
                ],
            ],
            'testimonials' => [
                'kicker' => 'PREPORUKE KLIJENATA',
                'title' => 'Povjerenje gradimo kroz konkretne rezultate',
                'intro' => '',
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašem projektu',
                'intro' => 'Javite nam se i zajedno ćemo procijeniti koji su izvori financiranja dostupni za vaš projekt te koji je najprikladniji put prijave i provedbe.',
                'contact_title' => 'Kontaktirajte nas',
            ],
            'blog_section' => [
                'title' => 'Objave iz kategorije :category',
                'intro' => 'Aktualni natječaji, programske novosti i korisni savjeti za pripremu, prijavu i provedbu projekata financiranih iz EU fondova.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function englishDefaults(): array
    {
        return self::croatianDefaults();
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
