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
                'subtitle_lead' => 'Savjetovanje',
                'subtitle_accent' => '',
                'intro' => 'Budućnost poslovanja oblikuju odluke koje donosite danas. Zato Vam pružamo stručnu financijsku i stratešku perspektivu koja pomaže prepoznati prilike, upravljati rizicima i stvarati dugoročnu vrijednost.',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'SAVJETOVANJE',
                'title' => 'Zašto Vam je savjetovanje bitno?',
                'body' => [
                    'Važne poslovne odluke rijetko imaju jednostavne odgovore. Financijske, porezne i strateške odluke mogu imati dugoročan utjecaj na poslovanje, zbog čega je važno imati stručnu perspektivu na koju se možete osloniti.',
                    'Naše savjetovanje povezuje stručnost iz različitih područja kako bismo Vam pomogli sagledati širu sliku, prepoznati prilike, upravljati rizicima i donositi odluke s većom sigurnošću.',
                ],
            ],
            'services_intro' => [
                'kicker' => 'USLUGE SAVJETOVANJA',
                'title' => 'Usluge savjetovanja',
                'intro' => '',
            ],
            'service_cards' => [
                [
                    'title' => 'Pribavljanje financiranja',
                    'text' => 'Podrška u strukturiranju i pribavljanju bankovnog financiranja, EU bespovratnih sredstava, poticajnih kredita i private equity kapitala.',
                    'url' => '/savjetovanje/pribavljanje-financiranja',
                ],
                [
                    'title' => 'M&A savjetovanje',
                    'text' => 'Podrška u procesima spajanja i preuzimanja — od strateškog pozicioniranja do pregovaranja i zatvaranja transakcije.',
                    'url' => '/savjetovanje/prodaja-i-kupnja-poduzeca',
                ],
                [
                    'title' => 'Dubinska snimanja (Due Diligence)',
                    'text' => 'Detaljna analiza poslovanja društva radi jasnog uvida u financijsko stanje, rizike i prilike prije važnih odluka ili transakcija.',
                    'url' => '/savjetovanje/dubinska-snimanja',
                ],
                [
                    'title' => 'Procjena vrijednosti društva',
                    'text' => 'Procjena ekonomske vrijednosti društva kao podloga za prodaju, kupnju, dokapitalizaciju i druge strateške aktivnosti.',
                    'url' => '/savjetovanje/procjena-vrijednosti-drustva',
                ],
                [
                    'title' => 'Porezno savjetovanje',
                    'text' => 'Podrška u poreznom planiranju, usklađenosti, poreznim pregledima, transfernim cijenama, poreznim nadzorima i transakcijama.',
                    'url' => '/savjetovanje/porezno-savjetovanje',
                ],
            ],
            'pandea' => [
                'title' => 'ALPHA CAPITALIS je član Pandea Global M&A',
                'body' => [
                    'ALPHA CAPITALIS je član Pandea Global M&A, globalne mreže za akvizicije koja povezuje investitore i prodavatelje različitih biznisa. Pandea Global M&A djeluje s naglaskom na spajanja, preuzimanja, pripajanja, dokapitalizacije i različite vrste joint venture-a, s ciljem plasiranja lokalnih projekata na međunarodno financijsko tržište.',
                    'Svojim klijentima i partnerima omogućujemo pristup širokoj mreži međunarodnih investitora. Lokalnim društvima otvaramo pristup prekograničnim transakcijama, a za međunarodne investitore djelujemo kao one stop shop i lokalni partner.',
                    'Ako imate projekt za koji vam je potreban investitor ili prodajete postojeći biznis, možete nam se obratiti kako bismo vaš slučaj prezentirali međunarodnim investitorima.',
                ],
                'logo_alt' => 'Pandea Global M&A',
            ],
            'funding' => [
                'title' => 'Pribavljanje financiranja',
                'intro' => 'Pribavljanje financiranja obuhvaća podršku društvima u pronalasku odgovarajućih izvora kapitala za rast poslovanja, investicije i ostvarenje strateških ciljeva.',
                'cards' => [
                    [
                        'title' => 'EU fondovi',
                        'text' => 'EU bespovratna sredstva, nacionalni instrumenti potpore i projektna podrška od identifikacije poziva do provedbe.',
                        'url' => '/eu-fondovi',
                    ],
                    [
                        'title' => 'Bankovni krediti',
                        'text' => 'Strukturiranje kreditnih zahtjeva i podrška u pregovorima s financijskim institucijama.',
                        'url' => '/savjetovanje/pribavljanje-financiranja/bankovni-krediti',
                    ],
                    [
                        'title' => 'Zakon o poticanju ulaganja',
                        'text' => 'Podrška u korištenju poreznih olakšica i poticaja za investicijske projekte temeljem ZoPU-a.',
                        'url' => '/savjetovanje/pribavljanje-financiranja/zakon-o-poticanju-ulaganja',
                    ],
                ],
                'overview_title' => 'EU fondovi',
                'overview_body' => [
                    'EU fondovi su instrumenti financiranja kojima Europska unija podupire razvoj poduzetništva, inovacija, digitalne i zelene tranzicije te infrastrukture. Poduzetnicima su na raspolaganju bespovratna sredstva iz Višegodišnjeg financijskog okvira 2021.–2027., a uz EU programe dostupni su i nacionalni instrumenti potpore — HBOR krediti, HAMAG zajmovi te porezne olakšice temeljem Zakona o poticanju ulaganja i istraživačko-razvojnim projektima.',
                ],
                'services_title' => 'Usluge EU fondova',
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
                'advisory_cards' => [],
            ],
            'source_modules' => [
                'kicker' => 'DOSTUPNI IZVORI FINANCIRANJA',
                'title' => 'Dostupni izvori financiranja',
                'intro' => '',
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
                        'title' => 'Porezne olakšice',
                        'text' => 'Poticaji, zakoni i porezne olakšice dostupni za investicijske projekte.',
                        'url' => '/eu-fondovi#eu-funds-laws',
                    ],
                    [
                        'title' => 'Bankovni krediti',
                        'text' => 'Sažetak podrške pri pripremi i strukturiranju kreditnih zahtjeva.',
                        'url' => '/savjetovanje/pribavljanje-financiranja#advisory-bankovni-krediti',
                    ],
                ],
            ],
            'bank_loans' => [
                'title' => 'Bankovni krediti',
                'kicker' => 'BANKOVNI KREDITI',
                'overview_title' => 'Što su bankovni krediti?',
                'overview_body' => [
                    'Bankovni krediti predstavljaju jedan od najčešćih izvora financiranja poslovanja, investicija i razvoja društava. Omogućuju poduzetnicima i društvima osiguravanje potrebnih sredstava za rast, povećanje kapaciteta, financiranje projekata ili optimizaciju postojeće financijske strukture.',
                ],
                'services_title' => 'Naše usluge',
                'services_body' => [
                    'Pružamo podršku klijentima u procesu pribavljanja bankovnog financiranja, od analize financijskih potreba i pripreme dokumentacije do komunikacije s financijskim institucijama. Naš pristup usmjeren je na pronalazak optimalnih kreditnih rješenja koja odgovaraju poslovnim ciljevima i financijskim mogućnostima klijenta.',
                ],
                'help_title' => 'U okviru usluge pomažemo u:',
                'help_items' => [
                    'analizi potreba za financiranjem',
                    'pripremi financijskih projekcija i investicijskih podloga',
                    'strukturiranju kreditnog zahtjeva',
                    'komunikaciji i pregovorima s bankama',
                    'analizi uvjeta financiranja',
                    'odabiru najpovoljnije strukture kredita',
                ],
                'approach_title' => 'Naš pristup',
                'approach_body' => [
                    'Proces započinjemo detaljnom analizom poslovanja, postojećih obveza i planova razvoja društva. Na temelju razumijevanja poslovnog modela pripremamo kvalitetne financijske materijale i podržavamo klijenta kroz cijeli proces financiranja, s ciljem osiguranja održivog i učinkovitog kreditnog rješenja.',
                ],
            ],
            'zopu' => [
                'title' => 'Zakon o poticanju ulaganja',
                'kicker' => 'ZAKON O POTICANJU ULAGANJA',
                'overview_title' => 'Što je Zakon o poticanju ulaganja?',
                'overview_body' => [
                    'Zakon o poticanju ulaganja predstavlja okvir za korištenje dostupnih potpora namijenjenih poticanju investicija, otvaranju novih radnih mjesta i povećanju konkurentnosti društava. Kroz različite oblike potpora omogućuje se smanjenje troškova ulaganja i stvaranje povoljnijih uvjeta za razvoj poslovanja.',
                ],
                'services_title' => 'Naše usluge',
                'services_body' => [
                    'Pružamo stručnu podršku društvima u pripremi i provedbi investicijskih projekata koji mogu ostvariti pravo na potpore prema Zakonu o poticanju ulaganja. Naš pristup obuhvaća analizu prihvatljivosti projekta, pripremu potrebne dokumentacije te podršku kroz cijeli proces realizacije ulaganja.',
                ],
                'help_title' => 'U okviru usluge pomažemo u:',
                'help_items' => [
                    'analizi mogućnosti korištenja potpora',
                    'procjeni prihvatljivosti investicijskog projekta',
                    'pripremi investicijske dokumentacije i prijavne dokumentacije',
                    'definiranju ključnih pokazatelja ulaganja',
                    'komunikaciji s nadležnim institucijama',
                    'praćenju provedbe investicijskih aktivnosti',
                ],
                'approach_title' => 'Naš pristup',
                'approach_body' => [
                    'Proces započinjemo razumijevanjem poslovnih ciljeva klijenta i planirane investicije. Analiziramo potencijalne mogućnosti ostvarivanja potpora te strukturiramo projekt na način koji omogućuje učinkovitu provedbu ulaganja i dugoročnu poslovnu vrijednost.',
                ],
            ],
            'ma' => [
                'title' => 'Prodaja i kupnja poduzeća (M&A)',
                'kicker' => 'M&A SAVJETOVANJE',
                'overview_title' => 'Što je prodaja i kupnja poduzeća?',
                'overview_body' => [
                    'Prodaja i kupnja poduzeća predstavljaju složene poslovne procese koji uključuju prijenos vlasništva, pronalazak odgovarajućih partnera i strukturiranje transakcije s ciljem ostvarivanja optimalne vrijednosti za sve uključene strane.',
                ],
                'show_pandea' => true,
                'services_title' => 'Naše usluge',
                'services_body' => [
                    'Pružamo stručnu podršku vlasnicima i investitorima kroz cijeli proces prodaje ili kupnje poduzeća – od početne analize i pripreme transakcije do pregovora i zaključenja posla. Naš pristup usmjeren je na učinkovito upravljanje procesom, zaštitu interesa klijenta i ostvarenje najboljih mogućih uvjeta transakcije.',
                ],
                'help_title' => 'U okviru usluge pomažemo u:',
                'help_items' => [
                    'analizi poslovanja i pripremi društva za prodaju',
                    'procjeni vrijednosti poduzeća',
                    'strukturiranju transakcije',
                    'pripremi transakcijske dokumentacije',
                    'identifikaciji potencijalnih kupaca ili investitora',
                    'analizi ciljanih društava za akviziciju',
                    'podršci tijekom dubinskog snimanja i pregovora',
                    'pripremi i provedbi procesa zaključenja transakcije',
                ],
                'approach_title' => 'Naš pristup',
                'approach_body' => [
                    'Proces započinjemo razumijevanjem ciljeva vlasnika ili investitora te analizom financijskog i poslovnog položaja društva. Kroz strukturirani proces, financijsku analizu i koordinaciju svih aktivnosti pomažemo klijentima donijeti informirane odluke i uspješno realizirati transakciju.',
                ],
            ],
            'valuations' => [
                'title' => 'Procjena vrijednosti društva',
                'kicker' => 'PROCJENA VRIJEDNOSTI',
                'overview_title' => 'Što je procjena vrijednosti?',
                'overview_body' => [
                    'Procjena vrijednosti predstavlja proces utvrđivanja ekonomske vrijednosti društva na temelju analize poslovanja, financijskih rezultata, tržišnih uvjeta i budućeg potencijala rasta. Koristi se kao podloga za donošenje odluka u procesima prodaje, kupnje, dokapitalizacije i drugih strateških aktivnosti.',
                ],
                'services_title' => 'Naše usluge',
                'services_body' => [
                    'Pružamo usluge procjene vrijednosti trgovačkih društava vlasnicima, investitorima i financijskim institucijama, prilagođavajući pristup svrsi i specifičnostima svakog pojedinog projekta. Naš cilj je pružiti objektivan i pouzdan uvid u vrijednost društva te ključne čimbenike koji na nju utječu.',
                ],
                'help_title' => 'U okviru usluge pomažemo u:',
                'help_items' => [
                    'analizi poslovanja i financijskih pokazatelja društva',
                    'izradi financijskih modela i projekcija',
                    'procjeni budućih novčanih tokova i potencijala rasta',
                    'primjeni odgovarajućih metoda vrednovanja',
                    'podršci kod prodaje društva, akvizicija i dokapitalizacija',
                    'pružanju stručne podloge za pregovore i strateške odluke',
                ],
                'approach_title' => 'Naš pristup',
                'approach_body' => [
                    'Proces započinjemo razumijevanjem poslovnog modela, financijske pozicije i cilja procjene. Primjenom relevantnih tržišnih metoda vrednovanja, uključujući metodu diskontiranih novčanih tokova, usporedivih transakcija i usporedivih društava, izrađujemo procjenu koja pruža jasnu osnovu za donošenje kvalitetnih poslovnih odluka.',
                ],
            ],
            'due_diligence' => [
                'title' => 'Dubinska snimanja (Due Diligence)',
                'kicker' => 'DUE DILIGENCE',
                'overview_title' => 'Što je dubinsko snimanje (Due Diligence)?',
                'overview_body' => [
                    'Dubinsko snimanje predstavlja detaljnu analizu poslovanja društva s ciljem dobivanja jasnog i objektivnog uvida u financijsko stanje, poslovne rizike i potencijalnu vrijednost društva prije donošenja važnih poslovnih odluka ili provedbe transakcije.',
                ],
                'services_title' => 'Naše usluge',
                'services_body' => [
                    'Pružamo usluge dubinskog snimanja kupcima, prodavateljima i investitorima u procesima spajanja, preuzimanja i drugih poslovnih transakcija. Naš pristup usmjeren je na prepoznavanje ključnih čimbenika koji utječu na vrijednost društva te pravovremeno identificiranje rizika i prilika.',
                ],
                'help_title' => 'U okviru usluge pomažemo u:',
                'help_items' => [
                    'analizi financijskih rezultata i poslovanja društva',
                    'procjeni održivosti ostvarenih rezultata',
                    'identifikaciji ključnih rizika i potencijalnih nepravilnosti',
                    'analizi ključnih pokazatelja uspješnosti (KPI)',
                    'razumijevanju poslovnog modela i tržišnog položaja',
                    'prepoznavanju područja za unapređenje i stvaranje dodatne vrijednosti',
                ],
                'approach_title' => 'Naš pristup',
                'approach_body' => [
                    'Proces započinjemo detaljnom analizom dostupnih financijskih i poslovnih podataka. Kroz strukturirani pristup i razumijevanje poslovanja društva pružamo pouzdanu podlogu za donošenje informiranih odluka, smanjenje rizika i uspješnu realizaciju poslovnih ciljeva.',
                ],
            ],
            'tax' => [
                'title' => 'Porezno savjetovanje',
                'kicker' => 'POREZNO SAVJETOVANJE',
                'overview_title' => 'Što je porezno savjetovanje?',
                'overview_body' => [
                    'Porezno savjetovanje obuhvaća stručnu podršku u razumijevanju i upravljanju poreznim obvezama, procjeni poreznih rizika i donošenju poslovnih odluka usklađenih s važećim propisima. Cilj je osigurati poreznu učinkovitost, usklađenost poslovanja i dugoročnu održivost.',
                ],
                'services_title' => 'Naše usluge',
                'services_body' => [
                    'Pružamo porezno savjetovanje pravnim i fizičkim osobama kroz analizu porezne pozicije, planiranje poslovnih aktivnosti i podršku u svakodnevnim poreznim pitanjima, kao i kod složenijih poslovnih transakcija. Naš pristup temelji se na povezivanju poreznih propisa, poslovnih ciljeva i praktične primjene.',
                ],
                'help_title' => 'U okviru usluge pomažemo u:',
                'help_items' => [
                    'poreznom planiranju i optimizaciji',
                    'analizi poreznih rizika i usklađenosti poslovanja',
                    'pripremi poreznih mišljenja i stručnih tumačenja',
                    'podršci kod poreznih nadzora',
                    'PDV savjetovanju i međunarodnim poreznim pitanjima',
                    'transfernim cijenama i povezanim transakcijama',
                    'poreznom savjetovanju kod poslovnih restrukturiranja i M&A transakcija',
                ],
                'approach_title' => 'Naš pristup',
                'approach_body' => [
                    'Svaki angažman započinjemo razumijevanjem poslovnog modela, financijske i porezne pozicije klijenta te specifičnih poslovnih okolnosti. Analiziramo relevantne propise i poreznu praksu kako bismo preporučili rješenja koja su zakonita, praktično provediva i usmjerena na smanjenje rizika te stvaranje dugoročne vrijednosti.',
                ],
            ],
            'approach' => [
                'kicker' => 'NAŠ PRISTUP',
                'title' => 'Naš pristup',
                'body' => [
                    'Svaki savjetodavni angažman započinjemo razumijevanjem poslovnog konteksta, ciljeva i ključnih rizika. Analiziramo financijske podatke, tržišni položaj, porezne i regulatorne okolnosti te specifičnosti projekta kako bismo predložili rješenja koja su jasna, provediva i usmjerena na stvaranje dugoročne vrijednosti.',
                    'Naš pristup spaja strateško razmišljanje i praktičnu provedbu — od početne analize do konkretnih odluka, dokumentacije, komunikacije s dionicima i provedbe projekta.',
                ],
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašim poslovnim odlukama',
                'intro' => 'Javite nam se i zajedno ćemo procijeniti koji oblik savjetodavne podrške najbolje odgovara vašim ciljevima, fazi poslovanja i konkretnom izazovu.',
                'contact_title' => 'Kontaktirajte nas',
            ],
            'blog_section' => [
                'kicker' => 'OBJAVE',
                'title' => 'Savjetovanje',
                'intro' => 'Zadnje objave i novosti iz područja financija, poreza, transakcija i savjetovanja.',
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
