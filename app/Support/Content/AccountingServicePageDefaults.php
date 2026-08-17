<?php

namespace App\Support\Content;

class AccountingServicePageDefaults
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
                'subtitle_lead' => 'Računovodstvo',
                'subtitle_accent' => '',
                'intro' => 'Vi vodite poslovanje. Mi brinemo da Vaše brojke budu točne, pravovremene i spremne za svaku odluku.',
                'image_alt' => 'Računovodstvene i financijske usluge',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'RAČUNOVODSTVO',
                'title' => 'Zašto Vam je računovodstvo bitno?',
                'intro' => '',
                'body' => [
                    'Mirnije poslovanje počinje jasnim i pouzdanim brojkama. Ažurne financijske informacije daju Vam kontrolu nad poslovanjem, pomažu prepoznati prilike i rizike te donijeti sigurnije odluke.',
                    'Uz ALPHA CAPITALIS ne dobivate samo računovodstvenu uslugu, već pouzdanog partnera koji razumije Vaše poslovanje i prati Vas kroz svakodnevne izazove i planove rasta.',
                ],
            ],
            'services' => [
                'kicker' => 'USLUGE',
                'title' => 'Naše računovodstvene usluge',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Financijsko računovodstvo',
                        'text' => 'Vođenje poslovnih knjiga, financijski izvještaji i godišnji obračuni prema hrvatskim propisima i MSFI.',
                    ],
                    [
                        'title' => 'Obračun plaća',
                        'text' => 'Kompletan obračun plaća, naknada i putnih troškova. Prijave poreza i doprinosa. JOPPD obrasci.',
                    ],
                    [
                        'title' => 'Porezne prijave',
                        'text' => 'Izrada i predaja svih poreznih prijava: PDV, porez na dobit i godišnji obračun. Pravovremeno i bez propusta.',
                    ],
                    [
                        'title' => 'Upravljačko izvještavanje',
                        'text' => 'Prilagođeni financijski izvještaji za menadžment - jasni, vizualni i fokusirani na bitne pokazatelje.',
                    ],
                    [
                        'title' => 'Osnivanje i registracija',
                        'text' => 'Podrška pri osnivanju društva, izboru pravnog oblika i uspostavi računovodstvenog sustava od prvog dana.',
                    ],
                    [
                        'title' => 'Konsolidacija',
                        'text' => 'Izrada konsolidiranih financijskih izvještaja za grupe društava. Usklađivanje internih transakcija i eliminacija.',
                    ],
                ],
            ],
            'approach' => [
                'kicker' => 'PRISTUP',
                'title' => 'Naš pristup',
                'intro' => 'Nismo samo servis za vođenje knjiga. Razumijemo vaše poslovanje i proaktivno upozoravamo na porezne prilike, regulatorne promjene i financijske rizike. Vaš računovođa dostupan je kada trebate - ne samo u sezoni godišnjih obračuna.',
                'body' => [
                    'Nismo samo servis za vođenje knjiga. Razumijemo vaše poslovanje i proaktivno upozoravamo na porezne prilike, regulatorne promjene i financijske rizike. Vaš računovođa dostupan je kada trebate - ne samo u sezoni godišnjih obračuna.',
                ],
            ],
            'intro_section' => [
                'kicker' => 'RAČUNOVODSTVO',
                'title' => 'Usluge Računovodstva',
                'body' => [
                    'Isplata i obračun plaća stvaraju Vam administrativne probleme? Sumnjate u ispravnost financijskih izvještaja ili pak smatrate da ste bili podložni nekom obliku računovodstvene prijevare? Potrebna Vam je kvalitetna priprema i kontrola financijskih izvještaja? Tim ALPHA CAPITALIS ima rješenje za Vaše probleme.',
                    'Usluge Računovodstva:',
                ],
                'items' => [
                    'Vođenje poslovnih knjiga',
                    'Obračun plaća',
                    'Izvještavanje',
                    'Rent-a-računovođa',
                    'Računovodstvo za strane investitore',
                    'Registracija trgovačkih društava',
                    'Forenzičko računovodstvo',
                ],
                'video_title' => '',
                'video_url' => '',
            ],
            'editorial_section' => [
                'eyebrow' => 'ALPHA CAPITALIS',
                'title' => 'Prepustite nam računovodstvo',
                'subtitle' => 'Vi se fokusirajte na kvalitetno i predano vođenje poslovanja, ostvarenje dobiti i stvaranje nove ekonomske vrijednosti.',
                'cards' => [
                    [
                        'title' => 'Vodimo brigu o točnosti Vaših financijskih izvještaja',
                        'icon' => 'file-lines',
                        'body' => 'U potpunosti razumijemo Vašu brigu o netočnosti financijskih izvještaja. Osim što taj problem zadaje uznemirenost među ljudima te manjak povjerenja, financijska šteta koju može prouzročiti je i više nego očita. Naši računovodstveni stručnjaci s dugogodišnjom ekspertizom u tom području rado će preuzeti izradu Vaših financijskih izvještaja te Vam omogućiti fokus na primarne aktivnosti poslovanja.',
                    ],
                    [
                        'title' => 'Pratimo računovodstvene standarde, zakonske propise i standarde financijskog izvještavanja',
                        'icon' => 'scale-balanced',
                        'body' => 'Sve naše usluge računovodstva provodimo u skladu s računovodstvenim politikama društva, hrvatskim i međunarodnim računovodstvenim standardima (HSFI i MSFI) te poreznim i drugim zakonima.',
                    ],
                    [
                        'title' => 'Pružamo podršku kod donošenja financijskih odluka',
                        'icon' => 'chart-line',
                        'body' => 'Naša usluga usmjerena je Vama kao podrška u financijskim aktivnostima Vašeg društva. Od samog osnivanja društva uz Vas smo kako bismo Vam pomogli u donošenju financijskih odluka na temelju točnih i ažurnih podataka, prilagođenih računovodstvenim standardima i zakonskim zahtjevima.',
                    ],
                    [
                        'title' => 'Kroz kontinuirani razvoj osiguravamo kvalitetu usluge',
                        'icon' => 'book-open',
                        'body' => 'Djelujemo kao Vaši financijski savjetnici te u skladu sa specifičnim financijskim potrebama prilagođavamo svoju ulogu u cilju zadovoljavanja istih. U nastojanju da osiguramo kvalitetnu uslugu i zadovoljstvo klijenta, uveli smo ISO standard upravljanja kvalitetom 9001:2015. Nadalje, naš tim posjeduje stručne certifikate i kontinuirano se educira kroz vanjske i interne edukacije kako bi za naše klijente bio kompas kroz svijet financija.',
                    ],
                ],
            ],
            'bookkeeping_section' => [
                'slug' => 'vodenje-poslovnih-knjiga',
                'title' => 'Vođenje poslovnih knjiga',
                'intro' => 'Kroz uredno i pravodobno vođenje poslovnih knjiga osiguravamo pouzdanu evidenciju poslovnih promjena, pregled obveza i kvalitetnu podlogu za financijsko odlučivanje.',
                'list_title' => 'Vođenje poslovnih knjiga odnosi se na sljedeće:',
                'items' => [
                    'Evidentiranje knjigovodstvene dokumentacije u poslovne knjige: glavne knjige, knjige ulaznih i izlaznih računa, blagajne i izvoda.',
                    'Analitičko praćenje poslovnih promjena na salda kontima dobavljača, kupaca, mjestima troška te profitnim centrima.',
                    'Izrada i obračun PDV prijava (mjesečno / kvartalno) te izvještavanje za potrebe porezne uprave preko sustava e-Porezna.',
                    'Dodatne usluge uključuju izdavanje računa, plaćanje računa internet bankarstvom, izradu kompenzacija i cesija te pripremu dokumentacije za natječaje, poticaje, kredite i leasing.',
                ],
                'cta_text' => 'Za više informacija o vođenju poslovnih knjiga i modelu suradnje, slobodno nas kontaktirajte.',
                'cta_label' => 'Pošaljite upit',
                'cta_url' => '#accounting-sastanak',
            ],
            'detail_sections' => [
                [
                    'slug' => 'vodenje-poslovnih-knjiga',
                    'icon' => 'book-open',
                    'title' => 'Vođenje poslovnih knjiga',
                    'intro' => 'Kroz uredno i pravodobno vođenje poslovnih knjiga osiguravamo pouzdanu evidenciju poslovnih promjena, pregled obveza i kvalitetnu podlogu za financijsko odlučivanje.',
                    'list_title' => 'Vođenje poslovnih knjiga odnosi se na sljedeće:',
                    'items' => [
                        'Evidentiranje knjigovodstvene dokumentacije u poslovne knjige: glavne knjige, knjige ulaznih i izlaznih računa, blagajne i izvoda.',
                        'Analitičko praćenje poslovnih promjena na salda kontima dobavljača, kupaca, mjestima troška te profitnim centrima.',
                        'Izrada i obračun PDV prijava (mjesečno / kvartalno) te izvještavanje za potrebe porezne uprave preko sustava e-Porezna.',
                        'Dodatne usluge uključuju izdavanje računa, plaćanje računa internet bankarstvom, izradu kompenzacija i cesija te pripremu dokumentacije za natječaje, poticaje, kredite i leasing.',
                    ],
                    'quote' => 'Pouzdano vođene poslovne knjige daju jasnu sliku poslovanja, obveza i novčanih tokova.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'obracun-placa',
                    'icon' => 'file-lines',
                    'title' => 'Obračun plaća',
                    'intro' => 'Ovom uslugom prepuštate brigu oko plaća našim stručnim ljudima koji ažurno i aktivno prate aktualne promjene zakona poreza na dohodak i zakona o radu, za koje vas pravodobno informiramo.',
                    'items' => [
                        'Pružamo potpuno individualno savjetovanje o obračunu plaća, prikupljanje i obradu podataka s naglaskom na čuvanju povjerljivih osobnih podataka.',
                        'Obračunavamo neoporezive isplate (božićnice, uskrsnice, regresi i otpremnine), plaću u naravi, bolovanje na teret društva i HZZO-a, honorare vanjskih suradnika te putne naloge.',
                        'Izrada poreznih i statističkih izvještaja o isplatama plaća i drugih primitaka (JOPPD, IP).',
                        'Prijava i odjava zaposlenika na mirovinsko i zdravstveno osiguranje.',
                    ],
                    'quote' => 'Točan i pravodoban obračun plaća smanjuje rizik pogrešaka i administrativno opterećenje.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'izvjestavanje',
                    'icon' => 'chart-line',
                    'title' => 'Izvještavanje',
                    'intro' => 'Pripremamo obvezne i upravljačke izvještaje koji osiguravaju zakonsku usklađenost, bolji pregled poslovanja i kvalitetnu podlogu za donošenje odluka.',
                    'items' => [
                        'Na kraju poslovne godine potrebno je napraviti obvezno izvještavanje prema državi, koje se odnosi na godišnje izvještaje, a obvezni su po zakonu o računovodstvu, porezu na dobit, dohodak i dodanu vrijednost.',
                        'Obvezni godišnji izvještaji su: godišnja prijava poreza na dobit (PD), bilanca stanja i statistički izvještaj (GFI-POD), račun dobiti i gubitka (RDG) i popratni izvještaj (šuma i turističke zajednice).',
                        'Za donošenje važnih poslovnih odluka potrebne su točne i pravodobne informacije analizirane u izvještajima. Za potrebe naših klijenata kreiramo tjedne, mjesečne, tromjesečne i godišnje izvještaje u standardnim formatima.',
                        'Za upravljanje likvidnošću radimo tjedni i mjesečni izvještaj plaćenih računa (otvorene stavke kupaca i dobavljača), te praćenje strukture troškova na mjesečnoj razini.',
                        'Za potrebe izvještavanja moguće je uspostaviti praćenje profitabilnosti po projektima, odjelima, centrima ili praćenje troškova po mjestima.',
                    ],
                    'downloads' => [
                        [
                            'title' => 'Manipulacija financijskim izvještajima',
                            'url' => 'https://alphacapitalis.com/wp-content/uploads/2019/07/manipulacija-financijskim-izvjestajima-alpha-capitalis-akademija-predavanje-18-06-2019.pdf',
                            'label' => 'Preuzmi',
                        ],
                        [
                            'title' => 'Analiza financijskih izvještaja',
                            'url' => 'https://alphacapitalis.com/wp-content/uploads/2019/07/analiza-financijskih-izvjestaja-alpha-capitalis-akademija-predavanje-18-06-2019.pdf',
                            'label' => 'Preuzmi',
                        ],
                    ],
                    'quote' => 'Kvalitetno izvještavanje pretvara zakonsku obvezu u alat za upravljanje poslovanjem.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'rent-a-racunovoda',
                    'icon' => 'briefcase',
                    'title' => 'Rent-a-računovođa',
                    'intro' => 'Kupili ste novi računovodstveni sustav te želite prenijeti poslovanje pouzdano i sigurno? Želite dodatno obučiti zaposlenike kako bi bili sigurni da mogu samostalno rješavati sve zadatke? Poslovanje vam raste i trebate računovođu na određeni kratki period?',
                    'items' => [
                        'Ova usluga omogućuje upravo to, unajmljivanje računovođe s ciljem rješavanja tih kratkoročnih problema.',
                        'Naši ljudi su stručnjaci s dugogodišnjim iskustvom u različitim sustavima, te organiziranjem i uspostavljanjem sustava u nekoliko cijenjenih društava.',
                        'U slučaju manjka računovodstvenog kadra nudimo mogućnost „najma“ računovodstvenog djelatnika kako bi društvo nastavilo poslovati do uspostavljanja stabilnog poslovanja.',
                    ],
                    'quote' => 'Kada vam treba brzo računovodstveno pojačanje, uključujemo se bez prekida poslovanja.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'racunovodstvo-za-strane-investitore',
                    'icon' => 'user-group',
                    'title' => 'Računovodstvo za strane investitore',
                    'intro' => 'Strani ste investitor s kapitalom i idejom, ali niste u mogućnosti obavljati sve procese nužne za vođenje poslovanja u Hrvatskoj? Naše računovodstvo će vam pomoći i s ovim problemom kroz sljedeće usluge:',
                    'items' => [
                        'Osnivanje trgovačkog društva.',
                        'Vođenje svih poslovnih knjiga trgovačkog društva.',
                        'Upravljanje društvom.',
                        'Zaprimanje pošte i pružanje adrese sjedišta društva.',
                        'Zastupanje društva pred poreznom upravom.',
                        'Redovito izvještavanje menadžmenta o stanju poslovanja.',
                    ],
                    'quote' => 'Stranim investitorima omogućujemo stabilan lokalni operativni i računovodstveni oslonac.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'registracija-trgovackih-drustava',
                    'icon' => 'building-columns',
                    'title' => 'Registracija trgovačkih društava',
                    'intro' => 'Cjelokupni proces registracije društva još uvijek nije toliko jednostavan koliko se teži da bude. Pružamo vam podršku i vodimo vas kroz cijeli proces formiranja bilo kojeg pravnog oblika za obnašanje vaše buduće poslovne djelatnosti.',
                    'list_title' => 'Navodimo ključne korake kod registracije trgovačkog društva:',
                    'items' => [
                        'Prvi korak pri registraciji trgovačkog društva je davanje imena društvu. Ime društva ne smije već biti korišteno od strane postojećih trgovačkih društava. Stoga, potrebno je imati unaprijed pripremljenu listu prihvatljivih imena ako je primarno ime već korišteno.',
                        'Posjet javnom bilježniku. Prijavu za upis u sudski registar i niz dodatnih dokumenata potrebno je ovjeriti kod javnog bilježnika.',
                        'Osim dokumentacije ovjerene kod javnoga bilježnika, u FINI-i će trebati dostaviti još nekoliko dokumenata poput popisa članova društva, popisa članova ovlaštenih za zastupanje društva i potvrde o uplati osnivačkog pologa.',
                        'Preuzimanje dodatne dokumentacije u FINI, nakon čega se mora izraditi pečat i otvoriti račun u banci.',
                        'Prijava društva u poreznoj upravi sa svom pripadajućom dokumentacijom.',
                        'Najkasnije 15 dana od početka obavljanja djelatnosti društvo treba prijaviti u HZZO i HZMO.',
                    ],
                    'quote' => 'Vodimo vas kroz registraciju društva korak po korak, uz jasnu pripremu dokumentacije i procesa.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'forenzicko-racunovodstvo',
                    'icon' => 'magnifying-glass',
                    'title' => 'Forenzičko računovodstvo',
                    'intro' => 'Poslovanje se brzo mijenja, a prijetnje u obliku prijevare, korupcije i pranja novca te njihove moguće posljedice za ugled i financije, ali i pravne posljedice, dio su svakodnevice.',
                    'list_title' => 'Kada vam je potrebno forenzičko računovodstvo?',
                    'items' => [
                        'Imali ste krađu, nestala je ključna računovodstvena dokumentacija, menadžment manipulira financijskim izvještajima ili osjećate da ste žrtva korporativne prijevare.',
                        'Član ste nadzornog odbora i vjerujete da menadžment manipulira financijskim izvještajima.',
                        'Preuzeli ste poslovanje i želite napraviti primopredaju i utvrditi početno stanje.',
                        'U procesu ste arbitraže ili sudskog spora i potrebno je neovisno vještačenje financijske i računovodstvene dokumentacije.',
                    ],
                    'after_list' => [
                        'Naše forenzičko računovodstvo pomaže u rješavanju gore navedenih problema. Ključ uspješnog provođenja forenzičkog računovodstva leži u eksternom pogledu na cijelu situaciju. U prijevodu, zaposlenici organizacije često neće potpuno racionalno i objektivno sagledati sve aspekte poslovanja te je nužan neovisan pogled „izvana“.',
                    ],
                    'quote' => 'Neovisan pogled izvana ključan je za objektivno sagledavanje sumnji, rizika i financijskih nepravilnosti.',
                    'cta_text' => 'Za više informacija, slobodno nas kontaktirajte',
                    'cta_label' => 'Pošaljite upit',
                    'cta_url' => '#accounting-sastanak',
                ],
            ],
            'video_section' => [
                'title' => '',
                'intro' => '',
            ],
            'videos' => [
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašem računovodstvu',
                'intro' => 'Svaki klijent dobiva računovođu koji poznaje vaš sektor i veličinu poslovanja. Javite se za besplatnu procjenu.',
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
                'submit' => 'Pošalji',
                'button_label' => 'Dogovorite sastanak',
                'status' => 'Termin razgovora prilagođavamo vama.',
            ],
            'blog_section' => [
                'kicker' => 'NOVOSTI',
                'title' => 'Stručni uvidi u računovodstvo, izvještavanje i poslovne brojke',
                'intro' => 'Pratite novosti, stručne članke i praktične uvide vezane uz računovodstvo, izvještavanje i svakodnevno financijsko upravljanje.',
                'empty' => 'Novosti iz ove kategorije uskoro će biti dostupne.',
                'all_posts_label' => 'Pogledaj sve objave',
                'post_action_label' => 'Opširnije',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function englishDefaults(): array
    {
        return [
            'hero' => [
                'brand_title' => 'ALPHA CAPITALIS',
                'subtitle_lead' => 'Accounting',
                'subtitle_accent' => '',
                'intro' => 'You run the business. We make sure your numbers are accurate, timely, and ready for every decision.',
                'image_alt' => 'Accounting and financial services',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'ACCOUNTING',
                'title' => 'Why does accounting matter to you?',
                'intro' => '',
                'body' => [
                    'Calmer business operations begin with clear and reliable numbers. Up-to-date financial information gives you control over your business, helps you identify opportunities and risks, and supports more confident decisions.',
                    'With ALPHA CAPITALIS, you get more than an accounting service - you get a reliable partner who understands your business and supports you through everyday challenges and growth plans.',
                ],
            ],
            'services' => [
                'kicker' => 'SERVICES',
                'title' => 'Our accounting services',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Financial accounting',
                        'text' => 'Bookkeeping, financial statements, and annual accounts prepared under Croatian regulations and IFRS.',
                    ],
                    [
                        'title' => 'Payroll processing',
                        'text' => 'Complete payroll, compensation, and travel expense processing, including tax and contribution filings and JOPPD forms.',
                    ],
                    [
                        'title' => 'Tax filings',
                        'text' => 'Preparation and submission of VAT, corporate income tax, and annual tax filings on time and without omissions.',
                    ],
                    [
                        'title' => 'Management reporting',
                        'text' => 'Tailored financial reports for management - clear, visual, and focused on the key performance indicators that matter.',
                    ],
                    [
                        'title' => 'Company formation and registration',
                        'text' => 'Support with company formation, legal form selection, and accounting system setup from day one.',
                    ],
                    [
                        'title' => 'Consolidation',
                        'text' => 'Preparation of consolidated financial statements for groups, including intercompany alignment and eliminations.',
                    ],
                ],
            ],
            'approach' => [
                'kicker' => 'APPROACH',
                'title' => 'Our approach',
                'intro' => 'We are not only a bookkeeping service. We understand your business and proactively point out tax opportunities, regulatory changes, and financial risks. Your accountant is available when you need them - not only during annual closing season.',
                'body' => [
                    'We are not only a bookkeeping service. We understand your business and proactively point out tax opportunities, regulatory changes, and financial risks. Your accountant is available when you need them - not only during annual closing season.',
                ],
            ],
            'intro_section' => [
                'kicker' => 'ACCOUNTING',
                'title' => 'Accounting services',
                'body' => [
                    'Are payroll processing and salary calculations creating administrative pressure? Do you question the accuracy of your financial statements, or suspect exposure to some form of accounting irregularity or fraud? Do you need reliable preparation and control of your financial reports? The ALPHA CAPITALIS team has a solution.',
                    'Accounting services:',
                ],
                'items' => [
                    'Bookkeeping',
                    'Payroll processing',
                    'Reporting',
                    'Rent-an-accountant',
                    'Accounting for foreign investors',
                    'Company registration support',
                    'Forensic accounting',
                ],
                'video_title' => '',
                'video_url' => '',
            ],
            'editorial_section' => [
                'eyebrow' => 'ALPHA CAPITALIS',
                'title' => 'Leave your accounting to us',
                'subtitle' => 'Stay focused on leading the business with quality, commitment, profitability, and long-term value creation.',
                'cards' => [
                    [
                        'title' => 'We safeguard the accuracy of your financial statements',
                        'icon' => 'file-lines',
                        'body' => 'We fully understand the concern caused by inaccurate financial reporting. Beyond the loss of trust and internal uncertainty it creates, the financial damage can be significant. Our accounting specialists with years of experience are ready to take over the preparation of your financial statements so you can remain focused on your core business activities.',
                    ],
                    [
                        'title' => 'We follow accounting standards, legal requirements, and financial reporting rules',
                        'icon' => 'scale-balanced',
                        'body' => 'All of our accounting services are delivered in line with the company’s accounting policies, Croatian and international accounting standards (HSFI and IFRS), and applicable tax and other legal requirements.',
                    ],
                    [
                        'title' => 'We support your financial decision-making',
                        'icon' => 'chart-line',
                        'body' => 'Our service is designed as direct support for your company’s financial activities. From the very beginning of your business, we help you make financial decisions based on accurate and timely data aligned with accounting standards and legal obligations.',
                    ],
                    [
                        'title' => 'Continuous development helps us ensure service quality',
                        'icon' => 'book-open',
                        'body' => 'We act as your financial advisors and adapt our role to your specific financial needs. To ensure service quality and client satisfaction, we introduced the ISO 9001:2015 quality management standard. Our team also holds professional certifications and continuously develops through internal and external education so we can remain a reliable guide through the world of finance.',
                    ],
                ],
            ],
            'bookkeeping_section' => [
                'slug' => 'bookkeeping',
                'title' => 'Bookkeeping',
                'intro' => 'With timely and structured bookkeeping, we provide reliable records of business changes, better visibility into obligations, and a stronger basis for financial decision-making.',
                'list_title' => 'Bookkeeping covers the following activities:',
                'items' => [
                    'Recording accounting documentation in the core books: general ledger, incoming and outgoing invoice ledgers, cash records, and bank statements.',
                    'Analytical monitoring of business changes across supplier and customer balances, cost centers, and profit centers.',
                    'Preparing and filing VAT returns (monthly / quarterly), including reporting through the e-Tax administration system.',
                    'Additional services include invoice issuance, internet banking payment support, compensation and assignment documentation, and preparation of documents for grants, incentives, loans, and leasing.',
                ],
                'cta_text' => 'For more information about bookkeeping support and the right collaboration model, feel free to contact us.',
                'cta_label' => 'Send an inquiry',
                'cta_url' => '#accounting-sastanak',
            ],
            'detail_sections' => [
                [
                    'slug' => 'bookkeeping',
                    'icon' => 'book-open',
                    'title' => 'Bookkeeping',
                    'intro' => 'With timely and structured bookkeeping, we provide reliable records of business changes, better visibility into obligations, and a stronger basis for financial decision-making.',
                    'list_title' => 'Bookkeeping covers the following activities:',
                    'items' => [
                        'Recording accounting documentation in the core books: general ledger, incoming and outgoing invoice ledgers, cash records, and bank statements.',
                        'Analytical monitoring of business changes across supplier and customer balances, cost centers, and profit centers.',
                        'Preparing and filing VAT returns (monthly / quarterly), including reporting through the e-Tax administration system.',
                        'Additional services include invoice issuance, internet banking payment support, compensation and assignment documentation, and preparation of documents for grants, incentives, loans, and leasing.',
                    ],
                    'quote' => 'Reliable bookkeeping gives management a clearer view of operations, obligations, and cash flow.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'payroll-processing',
                    'icon' => 'file-lines',
                    'title' => 'Payroll processing',
                    'intro' => 'By using this service, you hand over payroll administration to our experts who continuously monitor legislative changes related to personal income tax and labor law and keep you informed on time.',
                    'items' => [
                        'We provide fully individualized payroll advisory, data collection, and processing with a strong focus on safeguarding confidential personal data.',
                        'We calculate non-taxable payments, benefits in kind, sick leave charged to the company and the public health fund, external contractor fees, and travel expenses.',
                        'Preparation of tax and statistical payroll reports and other payment-related filings.',
                        'Registration and deregistration of employees with pension and health insurance authorities.',
                    ],
                    'quote' => 'Accurate and timely payroll processing reduces risk, protects confidentiality, and eases the administrative burden.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'reporting',
                    'icon' => 'chart-line',
                    'title' => 'Reporting',
                    'intro' => 'We prepare statutory and management reports that support compliance, provide better visibility into business performance, and create a strong basis for decision-making.',
                    'items' => [
                        'Year-end statutory reporting required by accounting, profit tax, income tax, and VAT regulations.',
                        'Mandatory annual reports including the corporate income tax return, balance sheet, statistical report, profit and loss statement, and related accompanying reports.',
                        'Weekly, monthly, quarterly, and annual reports tailored to client needs and prepared in standard formats.',
                        'Liquidity reporting through weekly and monthly overviews of paid invoices, open customer and supplier items, and monthly cost tracking.',
                        'Profitability tracking by project, department, center, or cost location where needed for reporting purposes.',
                    ],
                    'downloads' => [
                        [
                            'title' => 'Financial statement manipulation',
                            'url' => 'https://alphacapitalis.com/wp-content/uploads/2019/07/manipulacija-financijskim-izvjestajima-alpha-capitalis-akademija-predavanje-18-06-2019.pdf',
                            'label' => 'Download',
                        ],
                        [
                            'title' => 'Financial statement analysis',
                            'url' => 'https://alphacapitalis.com/wp-content/uploads/2019/07/analiza-financijskih-izvjestaja-alpha-capitalis-akademija-predavanje-18-06-2019.pdf',
                            'label' => 'Download',
                        ],
                    ],
                    'quote' => 'High-quality reporting turns a legal obligation into a practical management tool.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'rent-an-accountant',
                    'icon' => 'briefcase',
                    'title' => 'Rent-an-accountant',
                    'intro' => 'Have you introduced a new accounting system, need temporary support during growth, or want to train your staff for more independent day-to-day work?',
                    'items' => [
                        'This service gives you access to a temporary accountant to solve short-term operational challenges.',
                        'Our experts have extensive experience across different systems and in setting up accounting processes in established companies.',
                        'Where there is a shortage of accounting staff, we provide interim support so the company can continue operating until a stable solution is in place.',
                    ],
                    'quote' => 'When you need quick and reliable accounting reinforcement, we step in without disrupting operations.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'accounting-for-foreign-investors',
                    'icon' => 'user-group',
                    'title' => 'Accounting for foreign investors',
                    'intro' => 'If you are a foreign investor with capital and an idea but cannot independently manage all the processes required to run a business in Croatia, our accounting team can support you through the following services:',
                    'items' => [
                        'Company formation support.',
                        'Full bookkeeping for the company.',
                        'Company management support.',
                        'Mail handling and registered office address services.',
                        'Representation before the tax authorities.',
                        'Regular management reporting on business performance.',
                    ],
                    'quote' => 'We provide foreign investors with stable local operational and accounting support from setup onward.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'company-registration-support',
                    'icon' => 'building-columns',
                    'title' => 'Company registration support',
                    'intro' => 'The company registration process is still not as simple as it aims to be. We support you through the entire setup process of the legal form best suited to your future business activity.',
                    'list_title' => 'Key steps in company registration include:',
                    'items' => [
                        'Choosing a company name that is available and compliant, with alternative acceptable names prepared in advance.',
                        'Visiting a notary public to certify the court registry application and related documents.',
                        'Submitting additional required documents to FINA, including member lists, authorized representatives, and proof of initial capital payment.',
                        'Collecting additional documentation through FINA, making a company seal, and opening a bank account.',
                        'Registering the company with the tax authority and filing all required documentation.',
                        'Registering with health and pension insurance institutions within the legally prescribed deadline after business commencement.',
                    ],
                    'quote' => 'We guide you through registration step by step, with a clear process and properly prepared documentation.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
                [
                    'slug' => 'forensic-accounting',
                    'icon' => 'magnifying-glass',
                    'title' => 'Forensic accounting',
                    'intro' => 'Business environments change quickly, and threats such as fraud, corruption, and money laundering, together with their financial, legal, and reputational consequences, are part of today’s reality.',
                    'list_title' => 'When might you need forensic accounting?',
                    'items' => [
                        'You experienced theft, missing accounting records, management manipulation of financial statements, or suspect corporate fraud.',
                        'You are a supervisory board member and believe management is manipulating financial statements.',
                        'You have taken over a business and want a proper handover and a verified opening position.',
                        'You are involved in arbitration or litigation and require an independent expert review of financial and accounting documentation.',
                    ],
                    'after_list' => [
                        'Our forensic accounting services help resolve the issues described above. Successful forensic accounting depends on an independent external view, since internal employees often cannot assess every aspect of the situation with full objectivity.',
                    ],
                    'quote' => 'An external, independent perspective is essential for an objective review of risks, suspicions, and irregularities.',
                    'cta_text' => 'For more information, feel free to contact us.',
                    'cta_label' => 'Send an inquiry',
                    'cta_url' => '#accounting-sastanak',
                ],
            ],
            'video_section' => [
                'title' => '',
                'intro' => '',
            ],
            'videos' => [
            ],
            'meeting' => [
                'kicker' => 'CONTACT',
                'title' => 'Let’s discuss your accounting',
                'intro' => 'Every client gets an accountant who understands their sector and company size. Reach out for a free assessment.',
                'visit_title' => 'Visit us',
                'visit_lines' => [
                    'Ul. Roberta Frangeša Mihanovića 9,',
                    '10110 Zagreb / Sky Office, 19th floor',
                ],
                'contact_title' => 'Contact us',
                'direct_phone_label' => 'Phone',
                'direct_email_label' => 'Email',
                'form_labels' => [
                    'first_name' => 'First name',
                    'last_name' => 'Last name',
                    'company' => 'Company',
                    'phone' => 'Phone number',
                    'email' => 'Email',
                    'subject' => 'Subject',
                    'message' => 'Message',
                ],
                'submit' => 'Send',
                'button_label' => 'Schedule a meeting',
                'status' => 'We arrange the meeting around your schedule.',
            ],
            'blog_section' => [
                'kicker' => 'INSIGHTS',
                'title' => 'Expert insights into accounting, reporting and business figures',
                'intro' => 'Explore updates, articles, and practical insights related to accounting, reporting, and day-to-day financial operations.',
                'empty' => 'Posts from this category will be available soon.',
                'all_posts_label' => 'View all posts',
                'post_action_label' => 'Read more',
            ],
        ];
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
