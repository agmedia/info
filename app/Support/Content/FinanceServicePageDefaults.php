<?php

namespace App\Support\Content;

class FinanceServicePageDefaults
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
                'subtitle_accent' => 'financije',
                'intro' => 'Pružamo financijsko savjetovanje u postupcima spajanja, preuzimanja i prodaje društava, procjenama vrijednosti, pribavljanju kapitala i financijskom restrukturiranju, uz podršku kroz cijeli proces provedbe.',
                'cta_label' => 'Pregledajte usluge',
                'cta_url' => '#finance-usluge',
            ],
            'pandea' => [
                'title' => 'Članovi Globalne Mreže',
                'body' => [
                    'ALPHA CAPITALIS je član Pandea Global M&A, globalne mreže za akvizicije koja povezuje investitore i prodavatelje različitih biznisa. Pandea Global M&A djeluje s naglaskom na spajanja, preuzimanja, pripajanja, dokapitalizacije i razne vrste joint venture-a, a sve s ciljem plasiranja lokalnih projekata na internacionalno financijsko tržište.',
                    'Svojim klijentima i partnerima omogućili smo pristup velikoj mreži internacionalnih investitora. Lokalnim društvima otvaramo pristup prekograničnim transakcijama, a za internacionalne investitore djelujemo kao "one stop shop" i lokalni partner. Ako imate projekt za koji vam je potreban investitor ili prodajete postojeći biznis, slobodno nam se obratite kako bismo prezentirali vaš slučaj internacionalnim investitorima.',
                ],
                'logo_alt' => 'Pandea Global M&A',
            ],
            'services_intro' => [
                'kicker' => 'USLUGE',
                'title' => 'Podrška kroz ključne transakcijske i financijske procese',
                'intro' => 'Naš tim kombinira transakcijsko iskustvo, financijsku analizu i strukturiran proces rada kako bismo klijentima osigurali kvalitetnu pripremu, jasne materijale i podršku u donošenju odluka.',
            ],
            'ma' => [
                'title' => 'Spajanja i preuzimanja (M&A)',
                'intro' => 'Pružamo financijsko savjetovanje u postupcima spajanja, preuzimanja i prodaje društava. Usluge su usmjerene na strukturiranje transakcije, financijsku analizu poslovanja i podršku klijentima tijekom cijelog procesa provedbe.',
                'sale' => [
                    'title' => 'Prodaja poduzeća',
                    'body' => 'Rad započinje definiranjem ciljeva transakcije i analizom financijskih informacija društva. Na temelju provedenih analiza izrađujemo procjenu vrijednosti te pripremamo transakcijske materijale namijenjene potencijalnim kupcima ili investitorima. U nastavku pružamo financijsku podršku tijekom komunikacije sa zainteresiranim stranama, postupka dubinskog snimanja te tijekom pregovora o uvjetima transakcije.',
                    'process_title' => 'Opis standardnih koraka u organiziranom procesu prodaje društva',
                    'phases' => [
                        [
                            'title' => 'Faza I',
                            'label' => 'Priprema za prodaju',
                            'items' => [
                                'Procjena vrijednosti',
                                'Strukturiranje transakcije',
                            ],
                        ],
                        [
                            'title' => 'Faza II',
                            'label' => 'Priprema dokumentacije',
                            'items' => [
                                'Kratki profil tvrtke ("Teaser")',
                                'Ugovor o povjerljivosti podataka',
                                'Informacijski memorandum',
                            ],
                        ],
                        [
                            'title' => 'Faza III',
                            'label' => 'Marketing',
                            'items' => [
                                'Lista investitora',
                                'Kontaktiranje investitora',
                                'Slanje kratkih profila',
                                'Skraćena lista kvalificiranih investitora',
                            ],
                        ],
                        [
                            'title' => 'Faza IV',
                            'label' => 'Ponude i informacijski memorandum',
                            'items' => [
                                'Slanje ugovora odabranim tvrtkama',
                                'Slanje informativnog memoranduma nakon potpisa NDA-a',
                                'Primanje neobvezujućih ponuda',
                            ],
                        ],
                        [
                            'title' => 'Faza V',
                            'label' => 'Data room, pregovori i zatvaranje',
                            'items' => [
                                'Odabir ponuđača za pristup data roomu / prezentaciji',
                                'Pregovori u vezi s kupoprodajnim ugovorom',
                                'Zatvaranje kupoprodaje',
                            ],
                        ],
                    ],
                ],
                'acquisition' => [
                    'title' => 'Kupnja poduzeća',
                    'body' => 'Naš tim stručnjaka pruža podršku u svim fazama procesa kupnje poduzeća. Nudimo usluge dubinskog snimanja prije akvizicije te podršku nakon zaključenja transakcije, uz strateško savjetovanje o mehanizmima zaključenja i pomoć u pregovorima oko Ugovora o kupoprodaji udjela (SPA), s ciljem osiguravanja glatke i učinkovite tranzicije.',
                ],
            ],
            'due_diligence' => [
                'title' => 'Dubinska snimanja (due diligence)',
                'intro' => 'Dubinsko snimanje predstavlja ključan alat za donošenje informiranih odluka u transakcijama spajanja, preuzimanja i prodaje društva. Bilo da nastupate kao kupac ili prodavatelj, naš tim pruža jasan i objektivan uvid u financijsko stanje ciljanog društva te identificira čimbenike koji mogu utjecati na vrijednost i uvjet transakcije.',
                'help_title' => 'Pomažemo vam',
                'help_items' => [
                    'unaprijediti razumijevanje ciljanog društva kako bi transakcija lakše ostvarila svoje ciljeve',
                    'detektirati rizike i nepravilnosti',
                    'utvrditi i razumjeti ključne čimbenike uspjeha (KPI)',
                    'ukazati na prednosti koje mogu biti temelj razvoja ili nedostatke koji se mogu riješiti',
                ],
                'closing' => 'Naš pristup usmjeren je na razumijevanje stvarne financijske snage poslovanja, održivosti ostvarenih rezultata te pravodobno prepoznavanje rizika i prilika.',
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
            'capital_raising' => [
                'title' => 'Pribavljanje kapitala',
                'body' => [
                    'Struktura kapitala predstavlja omjer dužničkog i vlasničkog kapitala društva. Takva podjela otkriva kako je društvo financiralo imovinu s kojom generira prihode.',
                    'Osnovni zadatak financijskog menadžmenta društva je pronaći optimalnu strukturu kapitala koja će odražavati najmanji stupanj rizika uz najmanji trošak kapitala.',
                    'Jedan od problema s kojim se susreću poduzetnici je pribavljanje kapitala i financiranje nastavka poslovnog projekta.',
                    'Pružamo savjetodavnu podršku u postupcima pribavljanja financiranja za investicijske projekte, rast poslovanja i optimizaciju postojeće strukture financiranja. Proces započinje analizom financijskih potreba i kapaciteta klijenta, nakon čega izrađujemo financijske projekcije i pripremamo relevantne materijale za financijske institucije i potencijalne investitore.',
                    'U okviru usluge izrađujemo investicijske studije koje služe kao podloga za donošenje investicijskih odluka, osiguravanje financiranja i procjenu isplativosti ulaganja. Analiza obuhvaća poslovno i tržišno okruženje, investicijske troškove te projekcije financijskih rezultata i novčanih tokova, uz sagledavanje ključnih pretpostavki i rizika koji mogu utjecati na uspješnost ulaganja.',
                ],
                'sources_title' => 'Izvori financiranja',
                'sources' => [
                    'Private equity fondovi (preuzimanje, dokapitalizacija, dug)',
                    'Venture capital fondovi (preuzimanje, dokapitalizacija)',
                    'Anđeli investitori (preuzimanje, dokapitalizacija)',
                    'Mezzanine fondovi (dug)',
                    'Komercijalne banke (dug)',
                    'Razvojne banke (dug i jamstva)',
                    'Agencija za poticanje ulaganja (poticaji i jamstva)',
                    'Tržište kapitala (izdavanje obveznica i dionica)',
                    'Strateški investitori (preuzimanje, dokapitalizacija)',
                    'EU fondovi (bespovratna sredstva)',
                ],
            ],
            'restructuring' => [
                'title' => 'Financijsko restrukturiranje',
                'body' => [
                    'Financijsko restrukturiranje je svaka značajna promjena u financijskoj strukturi društva, vlasništva, kontroli i/ili poslovnom portfelju, a sve s ciljem povećanja vrijednosti društva.',
                    'Provodi se kako bi se prevladale financijske poteškoće, unaprijedilo poslovanje, izbjegli veći porezni nameti ili zbog osobnih razloga menadžmenta i njihovih ciljeva. Glavni cilj restrukturiranja je smanjiti troškove društva i povećati neto profitabilnost.',
                ],
                'prebankruptcy_title' => 'Što je predstečajna nagodba?',
                'prebankruptcy_body' => 'Postupak čija je svrha uspostavljanje likvidnosti i solventnosti dužnika, odnosno ponovno osposobljavanje dužnika za pravodobno ispunjavanje dospjelih novčanih obveza i osiguravanje trajnije sposobnosti ispunjavanja svih novčanih obveza.',
                'options_title' => 'Mogućnosti restrukturiranja trgovačkog društva',
                'options' => [
                    'Restrukturiranje obveza prema bankama i dobavljačima',
                    'Prodaja imovine koja nije dio osnovne djelatnosti',
                    'Zamjena potraživanja za vlasničke udjele',
                    'Dokapitalizacija i/ili preuzimanje od strane strateškog ili financijskog investitora',
                    'Reprogram duga',
                    'Izdvajanje gospodarske cjeline',
                    'Prodaja duga i potraživanja',
                    'Sale & Lease back',
                ],
                'reasons_title' => 'Razlozi restrukturiranja društva',
                'reasons' => [
                    'Usklađivanje proizvodnih kapaciteta, strukture i broja zaposlenih',
                    'Usklađivanje potrebnog kapitala',
                    'Unaprjeđenje prodaje',
                    'Prijenos upravljanja na mlađe generacije',
                    'Porezna optimizacija',
                ],
                'team_services_title' => 'Tim ALPHA CAPITALIS nudi sljedeće usluge',
                'team_services' => [
                    'Analiza početnog stanja',
                    'Izrada modela otplate duga i obveza',
                    'Analiza scenarija',
                    'Refinanciranje duga kod kreditnih institucija',
                    'Pronalazak alternativnog izvora financiranja',
                    'Vođenje pregovora s dobavljačima',
                    'Vođenje pregovora s kreditorima',
                    'Reprogram dugova i obveza',
                    'Prezentiranje poslovnog modela svim interesnim stranama',
                    'Pronalazak investitora za otkup duga, dokapitalizaciju ili preuzimanje',
                ],
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašem projektu',
                'intro' => 'Ako planirate transakciju, procjenu vrijednosti, pribavljanje kapitala ili restrukturiranje, javite nam se. Zajedno možemo definirati sljedeće korake i pripremiti model podrške koji odgovara vašoj situaciji.',
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
                'subtitle_lead' => 'Advisors for',
                'subtitle_accent' => 'finance',
                'intro' => 'We provide financial advisory services in mergers, acquisitions, company sales, business valuations, capital raising, and financial restructuring, with support throughout the full execution process.',
                'cta_label' => 'Explore services',
                'cta_url' => '#finance-usluge',
            ],
            'pandea' => [
                'title' => 'Global Network Members',
                'body' => [
                    'ALPHA CAPITALIS is a member of Pandea Global M&A, a global acquisition network that connects investors with sellers of various businesses. Pandea Global M&A operates with a focus on mergers, acquisitions, consolidations, recapitalizations, and various types of joint ventures, all with the aim of placing local projects on the international financial market.',
                    'We have provided our clients and partners with access to a vast network of international investors. We offer local companies access to cross-border transactions, while serving as a "one-stop shop" and local partner for international investors. If you have a project that requires an investor, or if you are selling an existing business, please feel free to contact us so that we may present your opportunity to international investors.',
                ],
                'logo_alt' => 'Pandea Global M&A',
            ],
            'services_intro' => [
                'kicker' => 'SERVICES',
                'title' => 'Support across key transactional and financial processes',
                'intro' => 'Our team combines transaction experience, financial analysis, and a structured delivery process to help clients prepare clearly, build the right materials, and move through decision-making with confidence.',
            ],
            'ma' => [
                'title' => 'Mergers and Acquisitions (M&A)',
                'intro' => 'We provide financial advisory services for mergers, acquisitions, and company sales. Our services focus on transaction structuring, business financial analysis, and client support throughout the entire execution process.',
                'sale' => [
                    'title' => 'Company Sale',
                    'body' => 'The process begins with defining transaction goals and analyzing the company\'s financial information. Based on these analyses, we conduct a valuation and prepare transaction materials intended for potential buyers or investors. Subsequently, we provide support during communication with interested parties, the due diligence process, and negotiations regarding transaction terms.',
                    'process_title' => 'Standard steps in an organised company sale process',
                    'phases' => [
                        [
                            'title' => 'Phase I',
                            'label' => 'Preparation for sale',
                            'items' => [
                                'Valuation',
                                'Transaction structuring',
                            ],
                        ],
                        [
                            'title' => 'Phase II',
                            'label' => 'Preparation of documentation',
                            'items' => [
                                'Company short profile ("Teaser")',
                                'Non-Disclosure Agreement (NDA)',
                                'Information Memorandum',
                            ],
                        ],
                        [
                            'title' => 'Phase III',
                            'label' => 'Marketing',
                            'items' => [
                                'Investor list',
                                'Contacting investors',
                                'Sending short profiles',
                                'Shortlist of qualified investors',
                            ],
                        ],
                        [
                            'title' => 'Phase IV',
                            'label' => 'Offers and information memorandum',
                            'items' => [
                                'Sending agreements to selected companies',
                                'Sending the Information Memorandum after the NDA is signed',
                                'Receiving non-binding offers',
                            ],
                        ],
                        [
                            'title' => 'Phase V',
                            'label' => 'Data room, negotiations and closing',
                            'items' => [
                                'Selection of bidders for data room access / presentation',
                                'Negotiations regarding the Sales and Purchase Agreement',
                                'Closing of the transaction',
                            ],
                        ],
                    ],
                ],
                'acquisition' => [
                    'title' => 'Company Acquisition',
                    'body' => 'Our team of experts provides support at all stages of the company acquisition process. We offer pre-acquisition due diligence services and post-closing support, along with strategic advisory on closing mechanisms and assistance in negotiations regarding the Share Purchase Agreement (SPA), with the aim of ensuring a smooth and efficient transition.',
                ],
            ],
            'due_diligence' => [
                'title' => 'Due Diligence',
                'intro' => 'Due diligence represents a key tool for making informed decisions in mergers, acquisitions, and company sales. Whether you are acting as a buyer or a seller, our team provides clear and objective insight into the financial position of the target company and identifies factors that may impact the value and terms of the transaction.',
                'help_title' => 'We help you',
                'help_items' => [
                    'improve your understanding of the target company so the transaction is more likely to achieve its objectives',
                    'detect risks and irregularities',
                    'identify and understand key performance indicators (KPIs)',
                    'highlight strengths that can become a basis for development or weaknesses that can be resolved',
                ],
                'closing' => 'Our approach focuses on understanding the true financial strength of the business, the sustainability of achieved results, and the timely recognition of risks and opportunities.',
            ],
            'valuations' => [
                'title' => 'Valuations',
                'body' => [
                    'We provide business valuation services for owners, investors, and financial institutions. Valuations are prepared for the purpose of company sales, buyouts of minority stakes, and recapitalizations.',
                    'The valuation process is based on an analysis of the company\'s operations, market environment, and financial information. Based on the conducted analyses, a financial model is developed, and the estimated value of the company is determined.',
                    'In valuing the company, we apply standard market valuation methods, including the Discounted Cash Flow (DCF) method, the Comparable Transactions method, and the Comparable Companies method, depending on the purpose of the valuation and the company\'s characteristics.',
                ],
                'methods_title' => 'Valuation methods',
                'methods' => [
                    'Discounted Cash Flow (DCF) method',
                    'Comparable Transactions method',
                    'Comparable Companies method',
                ],
            ],
            'capital_raising' => [
                'title' => 'Capital Raising',
                'body' => [
                    'Capital structure represents the ratio of a company\'s debt and equity capital. This breakdown reveals how the company has financed the assets with which it generates revenue.',
                    'The primary task of a company\'s financial management is to find the optimal capital structure that reflects the lowest level of risk alongside the lowest cost of capital.',
                    'One of the challenges entrepreneurs face is raising capital and financing the continuation of business projects.',
                    'We provide advisory support in the processes of raising financing for investment projects, business growth, and optimising existing capital structures. The process begins with an analysis of the client\'s financial needs and capacity, after which we create financial projections and prepare relevant materials for financial institutions and potential investors.',
                    'As part of this service, we prepare investment studies that serve as a basis for making investment decisions, securing financing, and assessing investment feasibility. The analysis covers the business and market environment, investment costs, and projections of financial results and cash flows, while considering key assumptions and risks that may affect investment success.',
                ],
                'sources_title' => 'Sources of financing',
                'sources' => [
                    'Private equity funds (acquisition, recapitalisation, debt)',
                    'Venture capital funds (acquisition, recapitalisation)',
                    'Angel investors (acquisition, recapitalisation)',
                    'Mezzanine funds (debt)',
                    'Commercial banks (debt)',
                    'Development banks (debt and guarantees)',
                    'Investment promotion agencies (incentives and guarantees)',
                    'Capital market (bond and share issuance)',
                    'Strategic investors (acquisition, recapitalisation)',
                    'EU funds (grants)',
                ],
            ],
            'restructuring' => [
                'title' => 'Financial Restructuring',
                'body' => [
                    'Financial restructuring is any significant change in a company\'s financial structure, ownership, control, and/or business portfolio, aimed at increasing company value.',
                    'It is conducted to overcome financial difficulties, improve operations, avoid higher tax burdens, or due to management\'s personal reasons and objectives. The main goal of restructuring is to reduce company costs and increase net profitability.',
                ],
                'prebankruptcy_title' => 'What is a pre-bankruptcy settlement?',
                'prebankruptcy_body' => 'It is a procedure aimed at re-establishing the debtor\'s liquidity and solvency, specifically rehabilitating the debtor to ensure the timely fulfilment of due monetary obligations and securing the long-term ability to meet all financial obligations.',
                'options_title' => 'Corporate restructuring options',
                'options' => [
                    'Restructuring of obligations towards banks and suppliers',
                    'Sale of non-core assets',
                    'Debt-to-equity swap',
                    'Recapitalisation and/or acquisition by a strategic or financial investor',
                    'Debt rescheduling',
                    'Spin-off of a business unit',
                    'Sale of debt and receivables',
                    'Sale & Lease back',
                ],
                'reasons_title' => 'Reasons for corporate restructuring',
                'reasons' => [
                    'Alignment of production capacities, structure, and workforce',
                    'Alignment of capital requirements',
                    'Sales improvement',
                    'Transfer of management to the next generation',
                    'Tax optimisation',
                ],
                'team_services_title' => 'The ALPHA CAPITALIS team offers the following services',
                'team_services' => [
                    'Analysis of the current situation',
                    'Development of debt and liability repayment models',
                    'Scenario analysis',
                    'Debt refinancing with credit institutions',
                    'Sourcing of alternative financing',
                    'Leading negotiations with suppliers',
                    'Leading negotiations with creditors',
                    'Rescheduling of debts and obligations',
                    'Presenting the business model to all stakeholders',
                    'Finding investors for debt buyout, recapitalisation, or acquisition',
                ],
            ],
            'meeting' => [
                'kicker' => 'CONTACT',
                'title' => 'Let\'s talk about your project',
                'intro' => 'If you are planning a transaction, valuation, capital raise, or restructuring process, get in touch with us. Together we can define the next steps and shape a support model that fits your situation.',
                'visit_title' => 'Visit us',
                'visit_lines' => [
                    'Roberta Frangeša Mihanovića 9,',
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
            ],
        ];
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
