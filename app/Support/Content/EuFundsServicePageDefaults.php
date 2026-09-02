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
                'image_alt' => 'Savjetovanje i podrška za EU fondove',
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
                'intro' => 'Donosimo pregled natječaja kojima poduzetnici mogu ostvariti EU bespovratna sredstva u 2026. godini.',
                'view_all_label' => 'Pogledaj sve natječaje',
                'download_link' => [
                    'label' => 'PREGLED NATJEČAJA',
                    'type' => 'pdf',
                    'locale' => 'hr',
                    'path' => 'front-theme/documents/eu-fondovi/eu-fondovi-pregled-natjecaja-2026.pdf',
                ],
                'other_calls' => [
                    'title' => 'Ostale vrste poziva za trgovačka društva',
                    'intro' => 'Programi i izvori financiranja koje vrijedi pratiti neovisno o trenutačnom statusu pojedinog poziva.',
                    'items' => [
                        [
                            'key' => 'eurostars',
                            'title' => 'EUROSTARS',
                            'link' => [
                                'type' => 'blog',
                                'slug' => 'eurostars-prilika-za-pokretanje-istrazivacko-razvojnih-ir-aktivnosti',
                            ],
                        ],
                        [
                            'key' => 'poc9',
                            'title' => 'POC9 – Državne potpore za inovacije',
                            'link' => [
                                'type' => 'call',
                                'slug' => 'dokazivanje-inovativnog-koncepta-3-poziv',
                            ],
                        ],
                        [
                            'key' => 'horizon-europe',
                            'title' => 'Program Obzor Europa',
                            'link' => [
                                'type' => 'blog',
                                'slug' => 'programi-unije',
                            ],
                        ],
                        [
                            'key' => 'eic-accelerator',
                            'title' => 'EIC Accelerator',
                            'link' => [
                                'type' => 'blog',
                                'slug' => 'podrska-eu-za-startup-ove-i-digitalne-inovacije-u-rh',
                            ],
                        ],
                        [
                            'key' => 'eureka',
                            'title' => 'EUREKA',
                            'link' => [
                                'type' => 'blog',
                                'slug' => 'eurostars-prilika-za-pokretanje-istrazivacko-razvojnih-ir-aktivnosti',
                            ],
                        ],
                        [
                            'key' => 'less-developed-areas',
                            'title' => 'Bespovratne potpore za poduzeća u manje razvijenim područjima',
                            'link' => [
                                'type' => 'blog',
                                'slug' => 'nova-karta-regionalnih-potpora-i-vaznost-regionalne-konkurentnosti',
                            ],
                        ],
                    ],
                ],
                'groups' => self::croatianCallGroups(),
            ],
            'resources' => [
                'kicker' => 'PROGRAMI I INSTRUMENTI',
                'title' => 'HBOR, HAMAG i ostali izvori potpore',
                'intro' => 'Uz bespovratna sredstva poduzetnicima su dostupni i krediti, zajmovi te drugi instrumenti koji mogu biti komplementarni investicijskom planu.',
                'cards' => [
                    [
                        'key' => 'questionnaire',
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
                        'key' => 'hbor',
                        'eyebrow' => 'HBOR',
                        'title' => 'HBOR krediti',
                        'body' => [
                            'HBOR kreditni programi dostupni su poduzetnicima za ulaganja u energetsku učinkovitost, modernizaciju proizvodnje, održivi turizam te digitalnu, zelenu i obrambeno-sigurnosnu tranziciju.',
                        ],
                        'groups' => [
                            [
                                'label' => 'Otvoreni programi',
                                'items' => [
                                    [
                                        'title' => 'Krediti za energetsku učinkovitost poduzetnika',
                                        'link' => ['type' => 'blog', 'slug' => 'najavljen-novi-hbor-ov-program-energetske-ucinkovitosti-poduzetnika'],
                                    ],
                                    [
                                        'title' => 'Krediti za modernizaciju proizvodnje',
                                        'link' => ['type' => 'blog', 'slug' => 'hbor-krediti-za-modernizaciju-proizvodnje'],
                                    ],
                                    [
                                        'title' => 'Krediti za održivi turizam',
                                        'link' => ['type' => 'blog', 'slug' => 'hbor-krediti-za-odrzivi-turizam'],
                                    ],
                                    [
                                        'title' => 'Ulaganja u digitalnu tranziciju NPOO',
                                        'link' => ['type' => 'blog', 'slug' => 'hbor-kredit-ulaganja-u-digitalnu-tranziciju'],
                                    ],
                                    [
                                        'title' => 'Ulaganja u zelenu tranziciju NPOO',
                                        'link' => ['type' => 'blog', 'slug' => 'hbor-kredit-ulaganja-u-zelenu-tranziciju'],
                                    ],
                                    [
                                        'title' => 'Ulaganja za obranu i sigurnost NPOO',
                                        'link' => ['type' => 'blog', 'slug' => 'hbor-kredit-ulaganja-za-obranu-i-sigurnost'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'hamag-bicro',
                        'eyebrow' => 'HAMAG-BICRO',
                        'title' => 'HAMAG zajmovi',
                        'body' => [
                            'HAMAG-BICRO zajmovi namijenjeni su financiranju poduzetničkih investicija, posebno malih i srednjih poduzetnika.',
                        ],
                        'groups' => [
                            [
                                'label' => 'Otvoreno',
                                'items' => [
                                    [
                                        'title' => 'Mali zajmovi za industrijsku tranziciju',
                                        'link' => ['type' => 'blog', 'slug' => 'mali-zajmovi-za-industrijsku-tranziciju'],
                                    ],
                                ],
                            ],
                            [
                                'label' => 'Zatvoreno',
                                'items' => [
                                    [
                                        'title' => 'Investicijski zajam iz NPOO',
                                        'link' => ['type' => 'blog', 'slug' => 'investicijski-zajam-iz-npoo'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'modernisation-fund',
                        'eyebrow' => 'MODERNIZACIJSKI FOND',
                        'title' => 'Modernizacijski fond',
                        'body' => [
                            'Poziv za dekarbonizaciju i modernizaciju toplinskih sustava otvoren je do 9. studenoga 2026. godine.',
                        ],
                        'groups' => [
                            [
                                'label' => 'Otvoreno',
                                'items' => [
                                    [
                                        'title' => 'Dekarbonizacija i modernizacija toplinskih sustava za grijanje i/ili hlađenje',
                                        'link' => ['type' => 'blog', 'slug' => 'dekarbonizacija-i-modernizacija-toplinskih-sustava-za-grijanje-i-ili-hladenje'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'rural-development',
                        'eyebrow' => 'STRATEŠKI PLAN ZPP',
                        'title' => 'Program ruralnog razvoja',
                        'body' => [
                            'Pregled relevantnih intervencija za ulaganja u poljoprivrednu proizvodnju, preradu, obnovljive izvore energije i razvoj poslovanja u ruralnim područjima.',
                        ],
                        'groups' => [
                            [
                                'label' => 'U najavi',
                                'items' => [
                                    [
                                        'title' => '73.03. Ulaganja – Korištenje obnovljivih izvora energije',
                                        'link' => ['type' => 'blog', 'slug' => '73-03-ulaganja-koristenje-obnovljivih-izvora-energije'],
                                    ],
                                    [
                                        'title' => '73.10. Ulaganja – Potpora za ulaganja u primarnu poljoprivrednu proizvodnju',
                                        'link' => ['type' => 'blog', 'slug' => '73-10-ulaganja-potpora-za-ulaganja-u-primarnu-poljoprivrednu-proizvodnju'],
                                    ],
                                    [
                                        'title' => '73.11. Ulaganja – Potpora za ulaganja u preradu poljoprivrednih proizvoda',
                                        'link' => ['type' => 'blog', 'slug' => '73-11-ulaganja-potpora-za-ulaganja-u-preradu-poljoprivrednih-proizvoda'],
                                    ],
                                    [
                                        'title' => '73.14. Ulaganja – Razvoj poslovanja u ruralnim područjima',
                                        'link' => ['type' => 'blog', 'slug' => '73-14-ulaganja-razvoj-poslovanja-u-ruralnim-podrucjima'],
                                    ],
                                    [
                                        'title' => '75.02. Diverzifikacija dohotka poljoprivrednih gospodarstava na nepoljoprivredne aktivnosti',
                                        'link' => ['type' => 'blog', 'slug' => '75-02-diverzifikacija-dohotka-poljoprivrednih-gospodarstava-na-nepoljoprivredne-aktivnosti'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'investment-promotion-act',
                        'eyebrow' => 'ZAKON O POTICANJU ULAGANJA',
                        'title' => 'Poticaji za nova ulaganja',
                        'body' => [
                            'Tijekom cijele godine poduzetnicima koji planiraju nove investicije na raspolaganju su porezne olakšice i bespovratne potpore, uz ispunjenje propisanih uvjeta.',
                        ],
                        'primary_link' => [
                            'label' => 'Saznaj više',
                            'type' => 'external',
                            'url' => '/eu-fondovi#eu-funds-laws',
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
                        'key' => 'investment-promotion-act',
                        'title' => 'Zakon o poticanju ulaganja',
                        'summary' => 'Tijekom cijele godine poduzetnicima koji planiraju nove investicije na raspolaganju stoji Zakon o poticanju ulaganja temeljem kojeg je, uz određene uvjete, omogućeno podnošenje prijava za ostvarivanje potpore.',
                        'lists' => [
                            [
                                'label' => 'Mogući oblici potpore',
                                'items' => [
                                    'Umanjenja poreza na dobit (za 50% / 75% / 100%, ovisno o visini investicije, na razdoblje od 3 do 10 godina)',
                                    'Bespovratnih potpora za troškove plaća i troškove usavršavanja zaposlenika',
                                ],
                            ],
                        ],
                        'note' => 'Planirate li realizirati investiciju u visini od minimalno 50.000,00 EUR koja će rezultirati otvaranjem novih radnih mjesta, javite nam se kako bismo napravili temeljitu procjenu Vaše ideje kao i cjelokupnu pripremu za prijavu za korištenje potpora putem Zakona o poticanju ulaganja.',
                        'primary_link' => [
                            'label' => 'Želite saznati više informacija?',
                            'type' => 'blog',
                            'slug' => 'zakon-o-poticanju-ulaganja-2',
                        ],
                        'secondary_link' => [
                            'label' => 'Brošura – Zakon o poticanju ulaganja',
                            'type' => 'pdf',
                            'locale' => 'hr',
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
                'button_label' => 'Dogovorite sastanak',
                'status' => 'Termin razgovora prilagođavamo vama.',
            ],
            'blog_section' => [
                'title' => 'Objave iz kategorije :category',
                'intro' => 'Aktualni natječaji, programske novosti i korisni savjeti za pripremu, prijavu i provedbu projekata financiranih iz EU fondova.',
                'all_posts_label' => 'Pogledaj sve objave',
                'post_action_label' => 'Opširnije',
            ],
        ];
    }

    /**
     * Curated call blueprint used by the WXR re-importer. The public page still
     * prefers the database-backed call categories whenever they are available.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function croatianCallGroups(): array
    {
        return [
            [
                'title' => 'Pozivi u najavi',
                'tone' => 'pending',
                'status_label' => 'U NAJAVI',
                'items' => [
                    self::callItem('Ulaganja malih, srednjih i velikih poduzetnika u prelazak na kružno gospodarstvo', 'poziv-u-najavi-ulaganja-malih-srednjih-i-velikih-poduzetnika-u-prelazak-na-kruzno-gospodarstvo'),
                    self::callItem('Jačanje strateških partnerstava za inovacije u procesu industrijske tranzicije (SPIN – STEP)', 'poziv-u-najavi-jacanje-strateskih-partnerstva-za-inovacije-u-procesu-industrijske-tranzicije-faza-ii-spin'),
                    self::callItem('Start up / spin off poduzeća mladih istraživača', 'poziv-u-najavi-start-up-spin-off-poduzeca-mladih-istrazivaca'),
                    self::callItem('Digitalni vaučer', 'poziv-u-najavi-digitalni-vaucer'),
                    self::callItem('Dokazivanje inovativnog koncepta – Drugi poziv', 'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv'),
                ],
            ],
            [
                'title' => 'Otvoreni pozivi',
                'tone' => 'open',
                'status_label' => 'OTVORENO',
                'items' => [
                    self::callItem('Inovacije procesa u S3 područjima', 'inovacije-procesa-u-s3-podrucjima'),
                    self::callItem('Izgradnja i opremanje postrojenja za obradu reciklabilnog otpada', 'izgradnja-i-opremanje-postrojenja-za-obradu-reciklabilnog-otpada-2'),
                    self::callItem('Dokazivanje inovativnog koncepta – Prvi poziv', 'poziv-u-najavi-dokazivanje-inovativnog-koncepta-prvi-poziv'),
                    self::callItem('Program poticanja poduzetništva u kulturnim i kreativnim industrijama u 2026. godini', 'program-poticanja-poduzetnistva-u-kulturnim-i-kreativnim-industrijama-u-2026-godini'),
                    self::callItem('Postrojenja za skladištenje vlastite proizvodnje električne energije za potrebe odgođene isporuke energije u mrežu', 'postrojenja-za-skladistenje-vlastite-proizvodnje-elektricne-energije-za-potrebe-odgodene-isporuke-energije-u-mrezu'),
                    self::callItem('Inovacijski vaučeri', 'inovacijski-vauceri-2'),
                    self::callItem('Podrška uključivanju MSP-ova u lance vrijednosti (Integrator)', 'integrator'),
                ],
            ],
            [
                'title' => 'Zatvoreni pozivi',
                'tone' => 'closed',
                'status_label' => 'ZATVORENO',
                'items' => [
                    self::callItem('IRI S3 – Povećanje razvoja novih proizvoda i usluga koji proizlaze iz aktivnosti istraživanja i razvoja', 'iri-s3-povecanje-razvoja-novih-proizvoda-i-usluga-koji-proizlaze-iz-aktivnosti-istrazivanja-i-razvoja'),
                    self::callItem('Kolaborativna znanstvena istraživanja', 'kolaborativna-znanstvena-istrazivanja'),
                    self::callItem('Potpora MSP-ovima Istarske županije u zelenoj tranziciji putem proizvodnih inovacija', 'potpora-msp-ovima-istarske-zupanije-u-zelenoj-tranziciji-putem-proizvodnih-inovacija'),
                    self::callItem('Potpora MSP-ovima za internacionalizaciju', 'potpora-msp-ovima-za-internacionalizaciju'),
                    self::callItem('Potpora poduzećima za certifikaciju proizvoda i uvođenje sustava upravljanja', 'objavljen-natjecaj-potpora-poduzecima-za-certifikaciju-proizvoda-i-uvodenje-sustava-upravljanja'),
                    self::callItem('Inovacije novoosnovanih MSP', 'inovacije-novoosnovanih-msp'),
                    self::callItem('Ulaganje u učinkovitu upotrebu resursa i potpora prelasku na kružno gospodarstvo', 'ulaganje-u-ucinkovitu-upotrebu-resursa-i-potpora-prelasku-na-kruzno-gospodarstvo'),
                    self::callItem('Dokazivanje inovativnog koncepta – 3. poziv', 'dokazivanje-inovativnog-koncepta-3-poziv'),
                    self::callItem('Modernizacijski fond: Ulaganje u mjere energetske učinkovitosti i visokoučinkovitu kogeneraciju u prerađivačkoj industriji (MF-2024-2-1)', 'modernizacijski-fond-ulaganje-u-mjere-energetske-ucinkovitosti-i-visokoucinkovitu-kogeneraciju-u-preradivackoj-industriji-ref-broj-mf-2024-2-1'),
                    self::callItem('Proizvodnja električne energije iz obnovljivih izvora u prerađivačkoj industriji i toplinarstvu (MF-2023-1-1)', 'objavljen-poziv-proizvodnja-elektricne-energije-iz-obnovljivih-izvora-u-preradivackoj-industriji-i-toplinarstvu-referentni-broj-mf-2023-1-1'),
                    self::callItem('Transformacija i jačanje konkurentnosti kulturnih i kreativnih industrija', 'transformacija-i-jacanje-konkurentnosti-kulturnih-i-kreativnih-industrija'),
                    self::callItem('Jačanje strateških partnerstava za inovacije u procesu industrijske tranzicije', 'objavljen-poziv-jacanje-strateskih-partnerstva-za-inovacije-u-procesu-industrijske-tranzicije'),
                    self::callItem('Vaučeri za digitalizaciju', 'vauceri-za-digitalizaciju'),
                    self::callItem('Jačanje održivosti te poticanje zelene i digitalne tranzicije poduzetnika u sektoru turizma', 'jacanje-odrzivosti-te-poticanje-zelene-i-digitalne-tranzicije-poduzetnika-u-sektoru-turizma-referentni-broj-npoo-c1-6-r1-i2-01'),
                    self::callItem('Razvoj turističkih proizvoda prihvatljivih za okoliš, učinkovitost resursa te zelenu i digitalnu tranziciju', 'razvoj-turistickih-proizvoda-prihvatljivih-za-okolis-ucinkovitost-resursa-te-zelenu-i-digitalnu-tranziciju-2'),
                    self::callItem('Potpora poduzećima za tranziciju na energetski i resursno učinkovito gospodarstvo', 'objavljen-je-poziv-potpora-poduzecima-za-tranziciju-na-energetski-i-resursno-ucinkovito-gospodarstvo'),
                    self::callItem('Potpora poduzećima za tranziciju na energetski i resursno učinkovito gospodarstvo za 2024. godinu', 'potpora-poduzecima-za-tranziciju-na-energetski-i-resursno-ucinkovito-gospodarstvo-za-2024-godinu'),
                    self::callItem('Bespovratne potpore za digitalizaciju', 'bespovratne-potpore-za-digitalizaciju'),
                    self::callItem('Bespovratne potpore za novoosnovana poduzeća', 'bespovratne-potpore-za-novoosnovana-poduzeca'),
                    self::callItem('Komercijalizacija inovacija', 'komercijalizacija-inovacija'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function callItem(string $title, string $sourceSlug): array
    {
        return [
            'title' => $title,
            'preferred_title' => $title,
            'link' => [
                'type' => 'blog',
                'slug' => $sourceSlug,
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
