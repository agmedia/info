<?php

namespace App\Support\Content;

class AdvisoryServicePageDefaults
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
                'subtitle_lead' => 'Poslovno',
                'subtitle_accent' => 'savjetovanje',
                'intro' => 'Savjetovanje (Advisory) obuhvaća stručnu podršku u financijskim, poreznim i investicijskim pitanjima. Cilj je pomoći društvima, investitorima i poduzetnicima u donošenju kvalitetnih odluka, upravljanju rizicima te stvaranju dugoročne vrijednosti.',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'ŠTO JE POSLOVNO SAVJETOVANJE?',
                'title' => 'Što je poslovno savjetovanje?',
                'body' => [
                    'Financijsko savjetovanje obuhvaća sve strateške usluge vezane uz vrijednost, kapital i transakcije vašeg poslovanja — od procjene vrijednosti društva do strukturiranja financiranja i podrške u preuzimanjima i spajanjima.',
                ],
            ],
            'services_intro' => [
                'kicker' => 'USLUGE POSLOVNOG SAVJETOVANJA',
                'title' => 'Naše usluge',
                'intro' => 'Naš tim kombinira transakcijsko iskustvo, financijsku analizu i strukturiran proces rada kako bismo klijentima osigurali kvalitetnu pripremu, jasne materijale i podršku u donošenju odluka.',
            ],
            'service_cards' => [
                [
                    'title' => 'Pribavljanje financiranja',
                    'text' => 'Podrška u strukturiranju i pribavljanju bankovnog financiranja, EU bespovratnih sredstava, poticajnih kredita i private equity kapitala.',
                    'url' => '/savjetovanje/pribavljanje-financiranja',
                ],
                [
                    'title' => 'Due diligence',
                    'text' => 'Dubinsko financijsko snimanje ciljnog društva. Identifikacija rizika, prilagodbi i ključnih nalaza za odluku o transakciji.',
                    'url' => '#advisory-due-diligence',
                ],
                [
                    'title' => 'Procjene vrijednosti',
                    'text' => 'Procjena vrijednosti društva, imovine i udjela. Za transakcije, računovodstvene potrebe, sporove ili internu upotrebu menadžmenta.',
                    'url' => '#advisory-procjene-vrijednosti',
                ],
                [
                    'title' => 'M&A savjetovanje',
                    'text' => 'Podrška u procesima spajanja i preuzimanja — od strateškog pozicioniranja do pregovaranja i zatvaranja transakcije.',
                    'url' => '#advisory-ma',
                ],
            ],
            'pandea' => [
                'title' => 'ALPHA CAPITALIS je član Pandea Global M&A',
                'body' => [
                    'ALPHA CAPITALIS je član Pandea Global M&A, globalne mreže za akvizicije koja povezuje investitore i prodavatelje različitih biznisa. Pandea Global M&A djeluje s naglaskom na spajanja, preuzimanja, pripajanja, dokapitalizacije i razne vrste joint venture-a, a sve s ciljem plasiranja lokalnih projekata na internacionalno financijsko tržište.',
                    'Svojim klijentima i partnerima omogućili smo pristup velikoj mreži internacionalnih investitora. Lokalnim društvima otvaramo pristup prekograničnim transakcijama, a za internacionalne investitore djelujemo kao „one stop shop“ i lokalni partner. Ako imate projekt za koji Vam je potreban investitor ili prodajete postojeći biznis, slobodno nam se obratite kako bismo prezentirali Vaš slučaj internacionalnim investitorima.',
                ],
                'logo_alt' => 'Pandea Global M&A',
            ],
            'funding' => [
                'title' => 'Pribavljanje financiranja',
                'cards' => [
                    [
                        'title' => 'EU fondovi',
                        'text' => 'Savjetovanje i podrška u prijavi na EU bespovratna sredstva — od identifikacije do ishođenja ugovora o dodjeli.',
                        'url' => '/savjetovanje/pribavljanje-financiranja',
                    ],
                    [
                        'title' => 'Bankovni krediti',
                        'text' => 'Strukturiranje kreditnih zahtjeva i podrška u pregovorima s financijskim institucijama.',
                        'url' => '#advisory-bankovni-krediti',
                    ],
                    [
                        'title' => 'Zakon o poticanju ulaganja',
                        'text' => 'Podrška u korištenju poreznih olakšica i poticaja za investicijske projekte temeljem ZoPU-a.',
                        'url' => '#advisory-zopu',
                    ],
                ],
                'overview_title' => 'EU fondovi',
                'overview_body' => [
                    'EU fondovi su instrumenti financiranja kojima Europska unija podupire razvoj poduzetništva, inovacija, digitalne i zelene tranzicije te infrastrukture. Poduzetnicima su na raspolaganju bespovratna sredstva i javni programi potpore, a uz EU programe dostupni su i nacionalni instrumenti - HBOR krediti, HAMAG zajmovi te porezne olakšice temeljem zakona o poticanju ulaganja i istraživačko-razvojnim projektima.',
                ],
                'services_title' => 'Naše usluge',
                'services' => [
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
                'advisory_cards' => [
                    [
                        'title' => 'Financijsko savjetovanje',
                        'text' => 'Analiza financijske podobnosti projekta i identificiranje optimalnih izvora EU sufinanciranja.',
                    ],
                    [
                        'title' => 'Podrška pri prijavi',
                        'text' => 'Priprema projektne dokumentacije, pisanje prijave i koordinacija s tijelima u sustavu upravljanja fondovima.',
                    ],
                    [
                        'title' => 'Strukturiranje financiranja',
                        'text' => 'Kombiniranje EU bespovratnih sredstava s komercijalnim kreditima, vlastitim izvorima i poreznim olakšicama.',
                    ],
                ],
            ],
            'source_modules' => [
                'kicker' => 'DOSTUPNI IZVORI FINANCIRANJA',
                'title' => 'Zasebni moduli za natječaje, instrumente i olakšice',
                'intro' => 'Dostupni izvori financiranja odvojeni su od opisa usluge savjetovanja kako bi korisnik jasno razlikovao stručnu podršku od informativnih pregleda.',
                'items' => [
                    [
                        'title' => 'Otvoreni natječaji',
                        'text' => 'Pregled trenutno otvorenih natječaja i poziva za poduzetnike.',
                        'url' => '/eu-fondovi#eu-funds-calls',
                    ],
                    [
                        'title' => 'Zatvoreni natječaji',
                        'text' => 'Arhiva zatvorenih poziva i programa koji su prethodno bili dostupni.',
                        'url' => '/eu-fondovi#eu-funds-calls',
                    ],
                    [
                        'title' => 'Financijski instrumenti',
                        'text' => 'HBOR krediti, HAMAG zajmovi i instrumenti koji mogu nadopuniti bespovratna sredstva.',
                        'url' => '/eu-fondovi#eu-funds-resources',
                    ],
                    [
                        'title' => 'Porezne olakšice (ZoPU - Zakon o poticanju ulaganja)',
                        'text' => 'Poticaji, zakoni i porezne olakšice dostupni za investicijske projekte.',
                        'url' => '/eu-fondovi#eu-funds-laws',
                    ],
                ],
            ],
            'bank_loans' => [
                'title' => 'Bankovni krediti',
                'body' => [
                    'Pružamo podršku u pripremi i strukturiranju kreditnih zahtjeva kako bismo povećali vjerojatnost odobrenja financiranja i osigurali optimalne uvjete za klijenta.',
                    'Sudjelujemo u komunikaciji s bankama i drugim financijskim institucijama, pripremi potrebne dokumentacije te pregovorima vezanim uz uvjete financiranja. Cilj nam je pronaći rješenje koje odgovara investicijskim planovima, novčanom toku i dugoročnoj održivosti poslovanja.',
                ],
            ],
            'zopu' => [
                'title' => 'Zakon o poticanju ulaganja',
                'body' => [
                    'Pomažemo društvima u ostvarivanju prava na porezne olakšice i državne potpore dostupne temeljem Zakona o poticanju ulaganja. Analiziramo investicijske projekte, procjenjujemo ispunjavaju li uvjete za ostvarivanje poticaja te pružamo podršku tijekom cijelog procesa prijave i provedbe. Naš cilj je pomoći klijentima maksimalno iskoristiti dostupne pogodnosti i povećati isplativost planiranih ulaganja.',
                ],
            ],
            'ma' => [
                'title' => 'Spajanja i preuzimanja (M&A)',
                'intro' => 'Pružamo financijsko savjetovanje u postupcima spajanja, preuzimanja i prodaje društava. Usluge su usmjerene na strukturiranje transakcije, financijsku analizu poslovanja i podršku klijentima tijekom cijelog procesa provedbe.',
                'sale' => [
                    'title' => 'Prodaja poduzeća',
                    'body' => 'Rad započinje definiranjem ciljeva transakcije i analizom financijskih informacija društva. Na temelju provedenih analiza izrađujemo procjenu vrijednosti te pripremamo transakcijske materijale namijenjene potencijalnim kupcima ili investitorima. U nastavku pružamo financijsku podršku tijekom komunikacije sa zainteresiranim stranama, postupka dubinskog snimanja te tijekom pregovora o uvjetima transakcije.',
                ],
                'acquisition' => [
                    'title' => 'Kupnja poduzeća',
                    'body' => 'Naš tim stručnjaka pruža podršku u svim fazama procesa kupnje poduzeća. Nudimo usluge dubinskog snimanja prije akvizicije te podršku nakon zaključenja transakcije, uz strateško savjetovanje o mehanizmima zaključenja i pomoć u pregovorima oko Ugovora o kupoprodaji udjela (SPA), s ciljem osiguravanja glatke i učinkovite tranzicije.',
                ],
            ],
            'valuations' => [
                'title' => 'Procjene vrijednosti',
                'body' => [
                    'Pružamo usluge procjene vrijednosti trgovačkih društava za potrebe vlasnika, investitora i financijskih institucija. Procjene se izrađuju u svrhu prodaje društva, otkupa manjinskih udjela te dokapitalizacija.',
                    'Proces procjene temelji se na analizi poslovanja društva, tržišnog okruženja i financijskih informacija. Na temelju provedenih analiza izrađuje se financijski model te utvrđuje procijenjena vrijednost društva.',
                    'Pri procjeni vrijednosti primjenjujemo uobičajene tržišne metode vrednovanja, uključujući metodu diskontiranih novčanih tokova, metodu usporedivih transakcija i metodu usporedivih kompanija, ovisno o svrsi procjene i karakteristikama društva.',
                ],
                'methods_title' => 'Metode vrednovanja',
                'methods' => [
                    'Metoda diskontiranih novčanih tokova (DCF)',
                    'Metoda usporedivih transakcija',
                    'Metoda usporedivih kompanija',
                ],
            ],
            'due_diligence' => [
                'title' => 'Due diligence',
                'intro' => 'Dubinsko snimanje predstavlja ključan alat za donošenje informiranih odluka u transakcijama spajanja, preuzimanja i prodaje društva. Bilo da nastupate kao kupac ili prodavatelj, naš tim pruža jasan i objektivan uvid u financijsko stanje ciljanog društva te identificira čimbenike koji mogu utjecati na vrijednost i uvjet transakcije.',
                'help_title' => 'Pomažemo vam:',
                'help_items' => [
                    'unaprijediti razumijevanje ciljanog društva (čime je vjerojatnije da će transakcija postići svoje ciljeve)',
                    'detektirati rizike i nepravilnosti',
                    'utvrditi i razumjeti ključne čimbenike uspjeha (KPI)',
                    'ukazati na prednosti koje mogu biti temelj razvoja ili nedostatke koji se mogu riješiti',
                ],
                'closing' => 'Naš pristup usmjeren je na razumijevanje stvarne financijske snage poslovanja, održivosti ostvarenih rezultata te pravodobno prepoznavanje rizika i prilika.',
            ],
            'tax' => [
                'title' => 'Porezno savjetovanje',
                'overview_title' => 'Što je porezno savjetovanje?',
                'overview_body' => [
                    'Porezno savjetovanje obuhvaća analizu poreznih obveza, procjenu rizika, usklađenost s propisima i podršku u donošenju poslovnih odluka. U radu povezujemo relevantne zakone, službena mišljenja Porezne uprave, međunarodne porezne ugovore, OECD smjernice i praksu Europskog suda pravde kako bi preporuke bile jasne, primjenjive i poslovno održive.',
                ],
                'services_title' => 'Naše porezne usluge',
                'services' => [
                    [
                        'title' => 'Porezna mišljenja',
                        'text' => 'Pisano stručno mišljenje za specifične poslovne ili osobne situacije uz jasno tumačenje relevantnih propisa, porezne prakse i preporučenog postupanja.',
                    ],
                    [
                        'title' => 'Tax Compliance',
                        'text' => 'Priprema, pregled i podnošenje poreznih prijava te kontinuirana podrška pravnim i fizičkim osobama u pravodobnom ispunjavanju poreznih obveza.',
                    ],
                    [
                        'title' => 'Porezni pregled',
                        'text' => 'Analiza porezne pozicije i simulacija poreznog nadzora radi pravodobnog prepoznavanja rizika, pripreme dokumentacije i bolje spremnosti za postupke nadzora.',
                    ],
                    [
                        'title' => 'Porezna optimizacija',
                        'text' => 'Sustavna analiza poslovanja s ciljem prepoznavanja zakonitih mogućnosti za smanjenje poreznog opterećenja uz očuvanje usklađenosti i operativne učinkovitosti.',
                    ],
                    [
                        'title' => 'Porezni due diligence',
                        'text' => 'Dubinska analiza porezne pozicije društva prije kupnje, prodaje, spajanja, ulaganja ili drugih strateških transakcija.',
                    ],
                    [
                        'title' => 'Transferne cijene',
                        'text' => 'Izrada dokumentacije, transfernih politika i savjetovanje o povezanim transakcijama u skladu s lokalnim pravilima i OECD smjernicama.',
                    ],
                ],
                'cards' => [
                    [
                        'title' => 'Porezno planiranje',
                        'text' => 'Optimizacija poreznog položaja kroz pravovremeno planiranje transakcija, restrukturiranja i raspodjele dobiti.',
                    ],
                    [
                        'title' => 'PDV savjetovanje',
                        'text' => 'Savjetovanje u kompleksnim PDV pitanjima — prekogranične transakcije, oslobođenja, usklađenost s EU direktivama.',
                    ],
                    [
                        'title' => 'Transfer cijene',
                        'text' => 'Izrada politika transfernih cijena i dokumentacije za međunarodne grupe. Usklađenost s OECD smjernicama.',
                    ],
                    [
                        'title' => 'Porezni nadzori',
                        'text' => 'Zastupanje i podrška u postupcima poreznog nadzora. Priprema odgovora i žalbenih postupaka.',
                    ],
                    [
                        'title' => 'M&A porezno savjetovanje',
                        'text' => 'Porezna due diligence i strukturiranje transakcija kako bi se minimiziralo porezno opterećenje kupca i prodavatelja.',
                    ],
                    [
                        'title' => 'Međunarodno oporezivanje',
                        'text' => 'Savjetovanje u pitanjima rezidentnosti, ugovora o izbjegavanju dvostrukog oporezivanja i raspodjele dobiti.',
                    ],
                ],
                'approach_title' => 'Strukturiran pristup koji povezuje regulatorne zahtjeve i poslovnu praksu',
                'approach_body' => [
                    'Svaki angažman započinjemo razumijevanjem poslovnog modela, porezne pozicije i potencijalnih rizika klijenta. Analiziramo relevantne propise, praksu poreznih tijela i poslovne okolnosti kako bismo preporučili rješenja koja su istovremeno zakonita, operativno provediva i dugoročno održiva.',
                    'Fokus stavljamo na jasnoću preporuka, praktičnu primjenu i proaktivno upravljanje poreznim rizicima — od svakodnevne usklađenosti do složenih transakcija i poreznih nadzora.',
                ],
            ],
            'approach' => [
                'kicker' => 'NAŠ PRISTUP',
                'title' => 'Strukturiran pristup poslovnom savjetovanju',
                'body' => [
                    'Svaki angažman započinjemo razumijevanjem poslovnog modela, financijske i porezne pozicije te potencijalnih rizika klijenta.',
                    'Analiziramo relevantne podatke, regulatorni okvir i poslovne okolnosti kako bismo preporučili rješenja koja su jasna, provediva i dugoročno održiva.',
                ],
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o poslovnom savjetovanju',
                'intro' => 'Naš tim analizirat će vaš slučaj i predložiti optimalan pristup.',
                'contact_title' => 'Kontaktirajte nas',
            ],
            'blog_section' => [
                'kicker' => 'OBJAVE',
                'title' => 'Savjetovanje',
                'intro' => 'Zadnje objave i novosti iz područja financija, poreza, transakcija i poslovnog savjetovanja.',
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
                'subtitle_lead' => 'Business',
                'subtitle_accent' => 'advisory',
                'intro' => 'Advisory includes expert support in financial, tax, and investment matters, helping companies, investors, and entrepreneurs make quality decisions, manage risk, and create long-term value.',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'WHAT IS BUSINESS ADVISORY?',
                'title' => 'What is business advisory?',
                'body' => [
                    'Financial advisory includes strategic services related to value, capital, and transactions, from business valuation and financing structure to support in mergers and acquisitions.',
                ],
            ],
            'services_intro' => [
                'kicker' => 'BUSINESS ADVISORY SERVICES',
                'title' => 'Our Services',
                'intro' => 'Our team combines transaction experience, financial analysis, and a structured delivery process to help clients prepare clearly and make better decisions.',
            ],
            'service_cards' => [
                [
                    'title' => 'Financing',
                    'text' => 'Support in structuring and obtaining bank financing, EU grants, incentive loans, and private equity capital.',
                    'url' => '/savjetovanje/pribavljanje-financiranja',
                ],
                [
                    'title' => 'Due diligence',
                    'text' => 'Financial due diligence of target companies, including risk identification, adjustments, and key findings for transaction decisions.',
                    'url' => '#advisory-due-diligence',
                ],
                [
                    'title' => 'Valuations',
                    'text' => 'Valuation of companies, assets, and shares for transactions, accounting purposes, disputes, or management use.',
                    'url' => '#advisory-procjene-vrijednosti',
                ],
                [
                    'title' => 'M&A advisory',
                    'text' => 'Support in mergers and acquisitions, from strategic positioning to negotiation and transaction closing.',
                    'url' => '#advisory-ma',
                ],
            ],
            'pandea' => [
                'title' => 'ALPHA CAPITALIS is a member of Pandea Global M&A',
                'body' => [
                    'ALPHA CAPITALIS is a member of Pandea Global M&A, a global acquisitions network connecting investors and sellers of various businesses.',
                    'We provide clients and partners with access to a broad network of international investors and act as a local one-stop shop for cross-border transactions.',
                ],
                'logo_alt' => 'Pandea Global M&A',
            ],
            'funding' => [
                'title' => 'Financing',
                'cards' => [],
                'overview_title' => 'EU Funds',
                'overview_body' => [],
                'services_title' => 'Our Services',
                'services' => [],
                'advisory_cards' => [],
            ],
            'source_modules' => [
                'kicker' => 'AVAILABLE FUNDING SOURCES',
                'title' => 'Separate modules for calls, instruments, and incentives',
                'intro' => 'Funding source overviews are separated from the advisory service description for clearer navigation.',
                'items' => [],
            ],
            'bank_loans' => ['title' => 'Bank Loans', 'body' => []],
            'zopu' => ['title' => 'Investment Incentives Act', 'body' => []],
            'ma' => ['title' => 'Mergers and Acquisitions (M&A)', 'intro' => '', 'sale' => ['title' => 'Company Sale', 'body' => ''], 'acquisition' => ['title' => 'Company Acquisition', 'body' => '']],
            'valuations' => ['title' => 'Valuations', 'body' => [], 'methods_title' => 'Valuation Methods', 'methods' => []],
            'due_diligence' => ['title' => 'Due diligence', 'intro' => '', 'help_title' => '', 'help_items' => [], 'closing' => ''],
            'tax' => ['title' => 'Tax Advisory', 'overview_title' => 'What is tax advisory?', 'overview_body' => [], 'services_title' => 'Our tax services', 'services' => [], 'cards' => [], 'approach_title' => '', 'approach_body' => []],
            'approach' => ['kicker' => 'OUR APPROACH', 'title' => 'Structured business advisory approach', 'body' => []],
            'meeting' => ['kicker' => 'CONTACT', 'title' => 'Let’s Talk About Business Advisory', 'intro' => 'Our team will analyse your case and recommend the right approach.', 'contact_title' => 'Contact us'],
            'blog_section' => ['kicker' => 'INSIGHTS', 'title' => 'Advisory', 'intro' => 'Latest insights from finance, tax, transactions, and business advisory.'],
        ];
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
