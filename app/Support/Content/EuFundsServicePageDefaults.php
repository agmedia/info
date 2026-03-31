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
                'subtitle_lead' => 'Savjetnici za',
                'subtitle_accent' => 'EU fondove',
                'intro' => 'Aktivno pratimo natječaje, povezujemo investicijske planove s pravim izvorima financiranja i preuzimamo operativni dio pripreme i provedbe projekta kako biste se vi mogli usredotočiti na razvoj poslovanja.',
                'cta_label' => 'Pregledajte natječaje',
                'cta_url' => '#eu-funds-calls',
            ],
            'about' => [
                'kicker' => 'EU ODJEL',
                'title' => 'UPOZNAJTE NAS!',
                'body' => [
                    'Kroz naš odjel za EU fondove aktivno pratimo natječaje te klijentima nudimo jasne, pravovremene i konkretne informacije o mogućnostima financiranja. Ako imate ideju, investicijski plan ili tek osjećaj da "postoji nešto za Vas", mi ćemo ga pretvoriti u projekt koji ima realne šanse za prolaz.',
                    'S Vama smo u svakoj fazi projekta; od oblikovanja i prilagodbe projektne ideje natječaju, izrade kompletne dokumentacije i prijave, do provedbe projekta nakon odobrenja sredstava. U fazi implementacije preuzimamo administrativno praćenje, komunikaciju s nadležnim tijelima i projektnu koordinaciju, kako biste se Vi mogli fokusirati na ono što Vam je najvažnije - razvoj poslovanja.',
                    'Naš tim čine iskusni, stručni i pristupačni ljudi s bogatim iskustvom u projektima financiranim iz EU fondova. Radimo na infrastrukturnim, razvojno-istraživačkim i inovacijskim projektima, projektima digitalne i zelene tranzicije, internacionalizacije i sajamskih nastupa, uvođenja ICT rješenja, ISO normi i certifikata, kao i projektima održivosti i klimatske neutralnosti.',
                    'Vjerujemo u partnerski odnos, jasnu komunikaciju i rješenja prilagođena svakom klijentu. Naš cilj nije samo uspješna prijava na natječaj, već dugoročna vrijednost i stabilan rast Vašeg projekta.',
                ],
                'box_title' => 'Područja u kojima najčešće vodimo projekte',
                'box_items' => [
                    'infrastrukturni, razvojno-istraživački i inovacijski projekti',
                    'digitalna i zelena tranzicija poduzetnika',
                    'internacionalizacija i sajamski nastupi',
                    'uvođenje ICT rješenja, ISO normi i certifikata',
                    'održivost i klimatska neutralnost',
                ],
            ],
            'overview' => [
                'kicker' => 'EU FONDOVI',
                'title' => 'VFO, Mehanizam oporavka i otpornosti te investicijski poticaji',
                'intro' => 'Sredstva koja su državama članicama dostupna u financijskom razdoblju 2021.-2027. dolaze iz više izvora, a poduzetnicima su uz EU programe na raspolaganju i nacionalni instrumenti potpore.',
                'body' => [
                    'Višegodišnji financijski okvir (VFO) predstavlja dugoročni sedmogodišnji proračun EU-a kojim se definiraju ulaganja u područjima poput energetike, prometa, informacijskih i komunikacijskih tehnologija, klimatskih promjena i istraživanja.',
                    'Instrument Europske unije za oporavak osmišljen je kako bi se ublažile gospodarske i socijalne posljedice pandemije te kako bi gospodarstva i društva postala održivija, otpornija i spremnija za zelenu i digitalnu tranziciju.',
                    'U sklopu spomenutog Mehanizma izrađen je i Nacionalni plan oporavka i otpornosti, dok je poduzetnicima dodatno na raspolaganju korištenje poreznih olakšica i bespovratnih potpora temeljem Zakona o poticanju ulaganja, Uredbe o poticanju ulaganja u sektoru turizma i Zakona o državnoj potpori za istraživačko-razvojne projekte.',
                ],
            ],
            'chart' => [
                'kicker' => 'OKVIR FINANCIRANJA',
                'title' => 'Hrvatskoj je za razvoj i ulaganja na raspolaganju snažan EU paket',
                'intro' => 'Brojke ispod prikazuju glavne financijske okvire navedene u dostavljenom materijalu i daju kontekst poduzetnicima koji planiraju prijavu projekta u 2026. godini.',
                'stats' => [
                    [
                        'label' => 'VFO 2021.-2027.',
                        'value' => '14+ mlrd EUR',
                        'share' => 100,
                        'description' => 'na raspolaganju Republici Hrvatskoj',
                    ],
                    [
                        'label' => 'Instrument EU za oporavak',
                        'value' => '11+ mlrd EUR',
                        'share' => 79,
                        'description' => 'bespovratna sredstva i najpovoljniji zajmovi',
                    ],
                    [
                        'label' => 'NPOO',
                        'value' => '6,3 mlrd EUR',
                        'share' => 45,
                        'description' => 'bespovratna sredstva kroz plan oporavka',
                    ],
                ],
                'footnote' => 'Rok za provedbu svih reformi i investicija iz NPOO-a je 31. kolovoza 2026. godine.',
            ],
            'process' => [
                'kicker' => 'KAKO RADIMO',
                'title' => 'Od projektne ideje do administrativno uredne provedbe',
                'intro' => 'Naš angažman nije samo prijava projekta. Klijentima pomažemo prepoznati pravu priliku, pripremiti uvjerljivu dokumentaciju i osigurati urednu provedbu nakon odobrenja sredstava.',
                'items' => [
                    [
                        'title' => '1. Uskladivanje ideje s natječajem',
                        'text' => 'Analiziramo investicijske i razvojne prioritete društva te ih povezujemo s natječajima, kreditnim linijama i drugim dostupnim instrumentima potpore.',
                    ],
                    [
                        'title' => '2. Izrada prijave i dokumentacije',
                        'text' => 'Pripremamo projektnu logiku, proračun, obrasce i svu prateću dokumentaciju potrebnu za kvalitetnu i pravodobnu prijavu.',
                    ],
                    [
                        'title' => '3. Provedba i koordinacija',
                        'text' => 'Nakon odobrenja sredstava vodimo administrativno praćenje, komunikaciju s tijelima, izvještavanje i projektnu koordinaciju.',
                    ],
                ],
            ],
            'calls' => [
                'kicker' => 'PREGLED NATJEČAJA',
                'title' => 'Natječaji, pozivi i programi za 2026.',
                'intro' => 'Donosimo pregled natječaja kojima poduzetnici mogu ostvariti EU bespovratna sredstva u 2026. godini, uz napomenu da su poveznice postavljene samo tamo gdje već postoji lokalni blog zapis ili lokalni dokument.',
                'download_link' => [
                    'label' => 'Preuzmite pregled natječaja',
                    'type' => 'pdf',
                    'path' => 'front-theme/documents/eu-fondovi/eu-fondovi-pregled-natjecaja-2026.pdf',
                ],
                'groups' => [
                    [
                        'title' => 'Pozivi u najavi',
                        'tone' => 'pending',
                        'items' => [
                            ['title' => 'POZIV U NAJAVI: Dokazivanje inovativnog koncepta - Prvi poziv', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Izgradnja i opremanje postrojenja za obradu reciklabilnog otpada', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Ulaganja malih, srednjih i velikih poduzetnika u prelazak na kružno gospodarstvo', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Jačanje strateških partnerstava za inovacije u procesu industrijske tranzicije (SPIN - STEP)', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Start-up/spin off poduzeća mladih istraživača', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Inovacije u S3', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Digitalni vaučer', 'link' => ['type' => 'none']],
                            ['title' => 'POZIV U NAJAVI: Dokazivanje inovativnog koncepta - Drugi poziv', 'link' => ['type' => 'none']],
                        ],
                    ],
                    [
                        'title' => 'Otvoreni pozivi',
                        'tone' => 'open',
                        'items' => [
                            ['title' => 'Integrator', 'link' => ['type' => 'none']],
                            ['title' => 'Postrojenja za skladištenje vlastite proizvodnje električne energije za potrebe odgođene isporuke energije u mrežu', 'link' => ['type' => 'none']],
                            ['title' => 'Inovacijski vaučeri', 'link' => ['type' => 'blog', 'slug' => 'inovacijski-vauceri-2']],
                        ],
                    ],
                    [
                        'title' => 'Zatvoreni pozivi',
                        'tone' => 'closed',
                        'items' => [
                            ['title' => 'IRI S3', 'link' => ['type' => 'none']],
                            ['title' => 'Kolaborativna znanstvena istraživanja', 'link' => ['type' => 'none']],
                            ['title' => 'Potpora MSP-ovima Istarske županije u zelenoj tranziciji putem proizvodnih inovacija', 'link' => ['type' => 'none']],
                            ['title' => 'Potpora MSP-ovima za internacionalizaciju', 'link' => ['type' => 'none']],
                            ['title' => 'Potpora poduzećima za certifikaciju proizvoda i uvođenje sustava', 'link' => ['type' => 'none']],
                            ['title' => 'Inovacije novoosnovanih MSP-ova', 'link' => ['type' => 'blog', 'slug' => 'inovacije-novoosnovanih-msp']],
                            ['title' => 'Ulaganje u učinkovitu upotrebu resursa i potpora prelasku na kružno gospodarstvo', 'link' => ['type' => 'blog', 'slug' => 'blogulaganje-u-ucinkovitu-upotrebu-resursa-i-potpora-prelasku-na-kruzno-gospodarstvo']],
                            ['title' => 'Dokazivanje inovativnog koncepta - 3. poziv', 'link' => ['type' => 'none']],
                            ['title' => 'MODERNIZACIJSKI FOND: Ulaganje u mjere energetske učinkovitosti i visokoučinkovitu kogeneraciju u prerađivačkoj industriji', 'link' => ['type' => 'none']],
                            ['title' => 'MODERNIZACIJSKI FOND: Proizvodnja električne energije iz obnovljivih izvora u prerađivačkoj industriji i toplinarstvu', 'link' => ['type' => 'blog', 'slug' => 'objavljen-poziv-proizvodnja-elektricne-energije-iz-obnovljivih-izvora-u-preradivackoj-industriji-i-toplinarstvu-referentni-broj-mf-2023-1-1']],
                            ['title' => 'Transformacija i jačanje konkurentnosti kulturnih i kreativnih industrija', 'link' => ['type' => 'blog', 'slug' => 'transformacija-i-jacanje-konkurentnosti-kulturnih-i-kreativnih-industrija']],
                            ['title' => 'Jačanje strateških partnerstava za inovacije u procesu industrijske tranzicije', 'link' => ['type' => 'blog', 'slug' => 'objavljen-poziv-jacanje-strateskih-partnerstva-za-inovacije-u-procesu-industrijske-tranzicije']],
                            ['title' => 'Vaučeri za digitalizaciju', 'link' => ['type' => 'blog', 'slug' => 'bespovratne-potpore-za-digitalizaciju']],
                            ['title' => 'Jačanje održivosti te poticanje zelene i digitalne tranzicije poduzetnika u sektoru turizma', 'link' => ['type' => 'blog', 'slug' => 'razvoj-turistickih-proizvoda-prihvatljivih-za-okolis-ucinkovitost-resursa-te-zelenu-i-digitalnu-tranziciju-2']],
                            ['title' => 'Razvoj turističkih proizvoda prihvatljivih za okoliš, učinkovitost resursa te zelenu i digitalnu tranziciju', 'link' => ['type' => 'blog', 'slug' => 'razvoj-turistickih-proizvoda-prihvatljivih-za-okolis-ucinkovitost-resursa-te-zelenu-i-digitalnu-tranziciju-2']],
                            ['title' => 'Potpora poduzećima za tranziciju na energetski i resursno učinkovito gospodarstvo', 'link' => ['type' => 'blog', 'slug' => 'objavljen-je-poziv-potpora-poduzecima-za-tranziciju-na-energetski-i-resursno-ucinkovito-gospodarstvo']],
                            ['title' => 'Potpora poduzećima za tranziciju na energetski i resursno učinkovito gospodarstvo (2. krug Poziva)', 'link' => ['type' => 'blog', 'slug' => 'potpora-poduzecima-za-tranziciju-na-energetski-i-resursno-ucinkovito-gospodarstvo-za-2024-godinu']],
                            ['title' => 'Bespovratne potpore za digitalizaciju', 'link' => ['type' => 'blog', 'slug' => 'bespovratne-potpore-za-digitalizaciju']],
                            ['title' => 'Bespovratne potpore za inovacije novoosnovanih poduzeća', 'link' => ['type' => 'blog', 'slug' => 'bespovratne-potpore-za-novoosnovana-poduzeca']],
                            ['title' => 'Bespovratne potpore za komercijalizaciju inovacija', 'link' => ['type' => 'none']],
                        ],
                    ],
                ],
            ],
            'resources' => [
                'kicker' => 'PROGRAMI PODRŠKE',
                'title' => 'Financijski instrumenti i prvi koraci prije prijave',
                'intro' => 'Uz bespovratna sredstva poduzetnicima su dostupni i krediti, zajmovi te instrumenti potpore koji mogu biti komplementarni vašem investicijskom planu.',
                'cards' => [
                    [
                        'eyebrow' => 'ALPHA CAPITALIS',
                        'title' => 'EU fondovi',
                        'body' => [
                            'Prvi korak za utvrđivanje investicijskih i razvojnih prioriteta te njihovo povezivanje s natječajima za bespovratna sredstva je ispunjavanje upitnika.',
                            'Nakon obrade upitnika savjetujemo Vas pri odabiru najboljeg rješenja za Vaše društvo i procjenjujemo koji model potpore ima najviše smisla za planirano ulaganje.',
                        ],
                        'primary_link' => [
                            'label' => 'Ispuni upitnik',
                            'type' => 'external',
                            'url' => '/eu-fondovi/upitnik',
                        ],
                    ],
                    [
                        'eyebrow' => 'HBOR KREDITI',
                        'title' => 'Financijski instrumenti za konkurentnost i koheziju',
                        'body' => [
                            'Ministarstvo regionalnoga razvoja i fondova Europske unije u svojstvu Upravljačkog tijela za program Konkurentnost i kohezija 2021. - 2027. povjerilo je HBOR-u provedbu financijskih instrumenata koje sufinanciraju EFRR i HBOR.',
                        ],
                        'groups' => [
                            [
                                'label' => 'U najavi',
                                'items' => [
                                    ['title' => 'Najavljen novi HBOR-ov program energetske učinkovitosti poduzetnika!', 'link' => ['type' => 'none']],
                                ],
                            ],
                            [
                                'label' => 'Otvoreno',
                                'items' => [
                                    ['title' => 'HBOR krediti za modernizaciju proizvodnje', 'link' => ['type' => 'none']],
                                    ['title' => 'HBOR krediti za održivi turizam', 'link' => ['type' => 'none']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'eyebrow' => 'HAMAG BICRO - ZAJMOVI',
                        'title' => 'Zajmovi za industrijsku tranziciju',
                        'body' => [
                            'HAMAG-BICRO instrumenti mogu biti zanimljiva dopuna investicijskom modelu, posebno za poduzetnike koji uz bespovratna sredstva planiraju i dodatno financiranje provedbe ulaganja.',
                        ],
                        'groups' => [
                            [
                                'label' => 'U najavi',
                                'items' => [
                                    ['title' => 'Mali zajmovi za industrijsku tranziciju', 'link' => ['type' => 'none']],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'laws' => [
                'kicker' => 'ZAKONSKI OKVIR',
                'title' => 'Poticaji, uredbe i porezne olakšice',
                'intro' => 'Osim sredstava EU, poduzetnicima su na raspolaganju i kontinuirani programi potpore kroz porezne olakšice, sektorske uredbe i državne potpore za istraživanje i razvoj.',
                'cards' => [
                    [
                        'title' => 'Zakon o poticanju ulaganja',
                        'summary' => 'Tijekom cijele godine poduzetnicima koji planiraju nove investicije na raspolaganju stoji Zakon o poticanju ulaganja.',
                        'lists' => [
                            [
                                'label' => 'Prihvatljivi prijavitelji',
                                'items' => [
                                    'mikro, mali, srednji i veliki poduzetnici',
                                    'obveznici poreza na dobit',
                                ],
                            ],
                            [
                                'label' => 'Maksimalno razdoblje za korištenje poticaja',
                                'items' => [
                                    '5 godina (>= 50.000 EUR, samo za mikro poduzetnike)',
                                    '10 godina (150.000 EUR - 3.000.000 EUR)',
                                ],
                            ],
                            [
                                'label' => 'Vrste ulaganja i prihvatljivi troškovi',
                                'items' => [
                                    'proizvodno-prerađivačke aktivnosti',
                                    'razvojno-inovacijske aktivnosti',
                                    'aktivnosti poslovne podrške i usluga visoke dodane vrijednosti',
                                    'početno ulaganje u materijalnu i nematerijalnu imovinu',
                                ],
                            ],
                        ],
                        'primary_link' => [
                            'label' => 'Više informacija',
                            'type' => 'blog',
                            'slug' => 'zakon-o-poticanju-ulaganja-2',
                        ],
                        'secondary_link' => [
                            'label' => 'Brošura - Zakon o poticanju ulaganja',
                            'type' => 'pdf',
                            'path' => 'front-theme/documents/eu-fondovi/zakon-o-poticanju-ulaganja-brosura.pdf',
                        ],
                    ],
                    [
                        'title' => 'Uredba o poticanju ulaganja u sektoru turizma',
                        'summary' => 'Prihvatljivi korisnici iz ove uredbe su pravne i fizičke osobe (obrtnici) koje su obveznici poreza na dobit i obavljaju djelatnosti u turizmu.',
                        'lists' => [
                            [
                                'label' => 'Prihvatljive djelatnosti',
                                'items' => [
                                    'NKD 55: Smještaj',
                                    'NKD 56: Djelatnost pripreme i usluživanja hrane i pića',
                                    'NKD 79: Putničke agencije, organizatori putovanja i ostale rezervacijske usluge',
                                    'NKD 93: Sportske, zabavne i rekreacijske djelatnosti',
                                    'NKD 77.34: Iznajmljivanje i davanje u zakup plovnih prijevoznih sredstava',
                                ],
                            ],
                            [
                                'label' => 'Fokus potpore',
                                'items' => [
                                    'održivi, inovativni i otporni turizam',
                                    'zelena i digitalna tranzicija hrvatskog turizma',
                                    'regionalna diverzifikacija i specijalizacija',
                                    'razvoj turističkog proizvoda visoke dodane vrijednosti',
                                ],
                            ],
                        ],
                        'note' => 'Najviši godišnji iznos državne potpore koji korisnik potpore za ulaganje može koristiti na temelju ove Uredbe ne može biti veći od 7.000.000,00 EUR.',
                        'primary_link' => [
                            'label' => 'Više informacija',
                            'type' => 'external',
                            'url' => 'https://narodne-novine.nn.hr/clanci/sluzbeni/2024_04_39_678.html',
                        ],
                    ],
                    [
                        'title' => 'Zakon o državnoj potpori za istraživačko-razvojne projekte',
                        'summary' => 'Zakon definira sustav poticanja privatnih ulaganja u istraživanje i razvoj kroz korištenje poreznih olakšica za mala, srednja i velika poduzeća koja provode istraživačko-razvojne aktivnosti.',
                        'lists' => [
                            [
                                'label' => 'Ukupni najviši intenzitet potpore',
                                'items' => [
                                    '100% prihvatljivih troškova za temeljna istraživanja',
                                    '50% prihvatljivih troškova za industrijska istraživanja',
                                    '25% prihvatljivih troškova za eksperimentalni razvoj',
                                    '50% prihvatljivih troškova za studije izvedivosti',
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
                'intro' => 'Izdvojili smo nekoliko preporuka klijenata koji su s nama surađivali na različitim projektima rasta, financiranja i operativne pripreme ulaganja.',
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o Vašem projektu',
                'intro' => 'Ako planirate ulaganje, prijavu projekta ili želite procijeniti koje potpore imaju najviše smisla za Vaše društvo, javite nam se. Zajedno možemo definirati sljedeće korake.',
                'visit_title' => 'Posjetite nas',
                'visit_lines' => [
                    'Ul. Roberta Frangeša Mihanovića 9,',
                    '10110 Zagreb / Sky Office, 19. kat',
                ],
                'contact_title' => 'Kontaktirajte nas',
                'direct_phone_label' => 'Telefon',
                'direct_email_label' => 'Email',
                'form_labels' => [
                    'first_name' => 'Ime',
                    'last_name' => 'Prezime',
                    'company' => 'Tvrtka',
                    'phone' => 'Broj telefona',
                    'email' => 'Email',
                    'subject' => 'Naslov poruke',
                    'message' => 'Poruka',
                ],
                'submit' => 'Pošalji upit',
            ],
            'blog_section' => [
                'title' => 'Novosti iz kategorije :category',
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
