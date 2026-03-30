<?php

namespace App\Support\Content;

class TaxServicePageDefaults
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
                'subtitle_lead' => 'Porezno',
                'subtitle_accent' => 'savjetovanje',
                'intro' => 'Pomažemo klijentima iz različitih sektora donositi poslovne odluke koje su usklađene s poreznim propisima i istodobno optimalne s ekonomske točke gledišta.',
                'cta_label' => 'Pregledajte usluge',
                'cta_url' => '#tax-overview',
            ],
            'overview' => [
                'kicker' => 'POREZI',
                'title' => 'Porezna podrška za svakodnevno poslovanje i strateške odluke',
                'intro' => 'Naše porezno savjetovanje obuhvaća operativnu usklađenost, stručno mišljenje za specifične situacije i podršku u složenijim transakcijama i međunarodnim odnosima.',
                'highlight_title' => 'Savjetovanje koje povezuje propise i poslovnu praksu',
                'body' => [
                    'Savjetujemo velika trgovačka društva, poduzetnike, obrtnike i fizičke osobe kroz pitanja usklađenosti, poreznog planiranja, procjene rizika i pripreme za nadzor. Kroz blisku suradnju s klijentom usklađujemo poslovne procese sa zakonskim zahtjevima i smanjujemo izloženost neželjenim poreznim rizicima.',
                    'U radu povezujemo relevantne zakone i pravilnike, službena mišljenja Porezne uprave, ugovore o izbjegavanju dvostrukog oporezivanja, OECD smjernice i praksu Europskog suda pravde kako bi preporuke bile jasne, primjenjive i poslovno održive.',
                ],
                'highlights_title' => 'Područja podrške',
                'highlights' => [
                    'Pisana porezna mišljenja i računovodstvena tumačenja',
                    'Tax Compliance za pravne i fizičke osobe',
                    'Porezni pregled i priprema za nadzor',
                    'Porezna optimizacija',
                    'Porezni due diligence',
                    'Transferne cijene',
                ],
            ],
            'services' => [
                'kicker' => 'USLUGE',
                'title' => 'Šest ključnih područja poreznog savjetovanja',
                'intro' => 'Pokrijemo i redovne porezne obveze i složene situacije koje zahtijevaju strukturirano savjetovanje, analizu rizika i dokumentaciju.',
                'items' => [
                    [
                        'title' => 'Porezna mišljenja',
                        'text' => 'Pisano stručno mišljenje za specifične situacije, uz obrazloženje relevantnih propisa, prakse i preporučenog postupanja.',
                    ],
                    [
                        'title' => 'Tax Compliance',
                        'text' => 'Priprema, pregled i podnošenje poreznih prijava te kontinuirana podrška u ispunjavanju poreznih obveza.',
                    ],
                    [
                        'title' => 'Porezni pregled',
                        'text' => 'Pregled porezne pozicije i simulacija nadzora radi pravodobnog uočavanja rizika i pripreme dokumentacije.',
                    ],
                    [
                        'title' => 'Porezna optimizacija',
                        'text' => 'Sustavna analiza poslovanja radi prepoznavanja zakonitih mogućnosti za smanjenje poreznog opterećenja.',
                    ],
                    [
                        'title' => 'Porezni due diligence',
                        'text' => 'Dubinska analiza porezne pozicije društva prije kupnje, prodaje, spajanja ili drugih strateških transakcija.',
                    ],
                    [
                        'title' => 'Transferne cijene',
                        'text' => 'Dokumentacija, politike i savjetovanje za povezane transakcije u skladu s lokalnim pravilima i OECD smjernicama.',
                    ],
                ],
            ],
            'compliance' => [
                'kicker' => 'TAX COMPLIANCE',
                'title' => 'Operativna usklađenost za pravne i fizičke osobe',
                'intro' => 'Tax Compliance usluge obuhvaćaju pravodobno i točno ispunjavanje poreznih obveza, pripremu prijava, podršku tijekom godine i komunikaciju s poreznim tijelima.',
                'corporate' => [
                    'title' => 'Corporate Tax Compliance',
                    'intro' => 'Pružamo cjelovitu podršku trgovačkim društvima i drugim pravnim osobama u području poreza na dobit i PDV-a.',
                    'groups' => [
                        [
                            'title' => 'Corporate Income Tax (CIT)',
                            'items' => [
                                'priprema i podnošenje godišnjih prijava poreza na dobit',
                                'izrada poreznih izračuna i usklađenja računovodstvene i porezne dobiti',
                                'identifikacija porezno nepriznatih troškova i poreznih olakšica',
                                'priprema dokumentacije i obrazloženja za potrebe Porezne uprave',
                                'asistencija tijekom poreznih nadzora i komunikacija s poreznim tijelima',
                            ],
                        ],
                        [
                            'title' => 'Value Added Tax (VAT)',
                            'items' => [
                                'priprema i podnošenje mjesečnih i tromjesečnih PDV prijava',
                                'savjetovanje o pravilnoj primjeni PDV tretmana transakcija',
                                'pregled PDV evidencija i usklađenosti s važećim propisima',
                                'podrška u vezi povrata PDV-a i ispravaka prijava',
                                'asistencija u komunikaciji s Poreznom upravom u vezi PDV pitanja',
                            ],
                        ],
                    ],
                ],
                'individual' => [
                    'title' => 'Individual Tax Compliance',
                    'intro' => 'Pomažemo rezidentima i nerezidentima u ispunjavanju poreznih obveza u Hrvatskoj, uz fokus na jasnoću, pravodobnost i smanjenje rizika.',
                    'items' => [
                        'priprema i podnošenje godišnjih prijava poreza na dohodak',
                        'asistencija kod prijava dohotka iz zemlje i inozemstva',
                        'obračun poreznih obveza po osnovi rada, kapitala, imovine i drugih izvora',
                        'savjetovanje o osobnim poreznim olakšicama i oslobođenjima',
                        'komunikacija s Poreznom upravom i podrška u slučaju upita ili nadzora',
                    ],
                ],
            ],
            'review' => [
                'kicker' => 'POREZNI PREGLED',
                'title' => 'Priprema za nadzor i procjena poreznih rizika',
                'intro' => 'Usluga poreznog pregleda pomaže klijentima razumjeti vlastitu poreznu poziciju prije nego što pitanja otvori Porezna uprava.',
                'body' => [
                    'Primarni cilj ove usluge jest pripremiti društvo za mogući porezni nadzor i pravodobno otkriti područja koja mogu rezultirati dodatnim obvezama, kamatama ili neizvjesnošću u poslovanju.',
                    'Kao praktičnu alternativu nudimo simulaciju poreznog nadzora u kojoj naši stručnjaci pregledavaju PDV, porez na dobit, porez na dohodak i druga relevantna područja kao da provode puni nadzor društva.',
                    'Ako je nadzor već pokrenut, pomažemo u pripremi prigovora na zapisnik, žalbi na porezno rješenje i u svim ostalim koracima komunikacije s poreznim tijelima.',
                ],
                'highlights_title' => 'Vrijednost za klijenta',
                'highlights' => [
                    'jasnija slika stvarnih poreznih rizika',
                    'prioritizacija korektivnih koraka prije nadzora',
                    'bolja pripremljenost dokumentacije i procesa',
                    'podrška ako je nadzor već u tijeku',
                ],
            ],
            'optimization' => [
                'kicker' => 'OPTIMIZACIJA',
                'title' => 'Otkrivanje potencijala za zakonitu poreznu uštedu',
                'intro' => 'Porezna optimizacija nije jednokratna aktivnost nego kontinuiran proces koji pomaže društvu planirati porezne obveze, smanjiti rizike i održati financijsku stabilnost.',
                'body' => [
                    'Kroz analizu porezno-računovodstvene dokumentacije i poslovnih procesa identificiramo situacije u kojima društvo plaća više poreza nego što bi bilo potrebno u porezno optimalnoj kombinaciji.',
                    'Fokus je na rješenjima koja smanjuju porezno opterećenje bez narušavanja operativnog koncepta poslovanja te pritom održavaju usklađenost s propisima i poslovnu učinkovitost.',
                    'Takav pristup poreznu optimizaciju pretvara u strateški alat koji podržava ulaganja, reorganizacije, redovno poslovanje i pripremu važnih transakcija.',
                ],
            ],
            'due_diligence' => [
                'kicker' => 'DUE DILIGENCE',
                'title' => 'Pouzdana porezna analiza prije strateških odluka',
                'intro' => 'Porezni due diligence daje jasan uvid u poreznu poziciju društva prije kupnje, prodaje, spajanja, pripajanja ili ulaska investitora.',
                'body' => [
                    'Analiziramo povijesno poslovanje, porezne prijave, regulatorni okvir i moguće izloženosti rizicima kako bi klijent prije zaključenja transakcije razumio potencijalne obveze i novčane odljeve.',
                    'Naš pristup pomaže identificirati i kvantificirati porezne rizike te osigurava da transakcija bude transparentna, kontrolirana i bez neželjenih iznenađenja nakon zaključenja.',
                    'Rezultat je pouzdana osnova za informirano donošenje odluka, pregovaračku pripremu i strukturiranje transakcije s potpunijim uvidom u porezni položaj ciljanog društva.',
                ],
            ],
            'transfer_pricing' => [
                'kicker' => 'TRANSFERNE CIJENE',
                'title' => 'Upravljanje poreznim rizicima u međunarodnom poslovanju',
                'intro' => 'Transferne cijene ključne su za grupe povezanih društava jer utječu na raspodjelu dobiti i porezne obveze u različitim jurisdikcijama.',
                'body' => [
                    'Neusklađene ili nedovoljno dokumentirane transferne cijene mogu dovesti do korekcija porezne osnovice, kamata i kazni. Zato pripremamo studije transfernih cijena koje uključuju analize funkcija, rizika i imovine te usporedbe s tržišnim uvjetima.',
                    'Uz dokumentaciju razvijamo i transferne politike koje odražavaju stvarne poslovne funkcije i rizike, savjetujemo o strukturiranju povezanih transakcija te pomažemo u integraciji transfernih cijena u poslovne i financijske procese.',
                    'Klijentima pružamo i podršku tijekom poreznih nadzora i sporova, uključujući pripremu odgovora na zahtjeve poreznih tijela i zastupanje u postupcima korekcije porezne osnovice.',
                ],
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašim poreznim pitanjima',
                'intro' => 'Ako trebate stručno mišljenje, usklađenje poreznih procesa, podršku pri nadzoru ili savjetovanje za kompleksnu transakciju, javite nam se. Zajedno možemo definirati najprikladniji model podrške.',
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
            'blog_section' => [
                'kicker' => 'BLOG',
                'title' => 'Najnovije objave iz kategorije :category',
                'intro' => 'Stručni članci, novosti i praktični uvidi vezani uz poreze, usklađenost, transakcije i porezne rizike.',
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
                'subtitle_lead' => 'Tax',
                'subtitle_accent' => 'advisory',
                'intro' => 'We help clients across sectors make business decisions that are compliant with tax regulations while remaining economically sound and practical.',
                'cta_label' => 'Explore services',
                'cta_url' => '#tax-overview',
            ],
            'overview' => [
                'kicker' => 'TAX',
                'title' => 'Tax support for day-to-day operations and strategic decisions',
                'intro' => 'Our tax advisory work covers operational compliance, written advice for specific situations, and support in complex transactions and cross-border matters.',
                'highlight_title' => 'Advice that connects regulation and business reality',
                'body' => [
                    'We advise corporations, entrepreneurs, sole proprietors, and individuals on compliance, tax planning, risk assessment, and audit readiness. Through close cooperation with clients, we align business processes with legal requirements and reduce exposure to unnecessary tax risk.',
                    'Our work combines relevant legislation and regulations, official Tax Administration guidance, double taxation treaties, OECD guidelines, and European Court of Justice practice so recommendations remain clear, actionable, and commercially sustainable.',
                ],
                'highlights_title' => 'Areas of support',
                'highlights' => [
                    'Written tax opinions and accounting interpretations',
                    'Tax compliance for legal entities and natural persons',
                    'Tax review and audit readiness',
                    'Tax optimization',
                    'Tax due diligence',
                    'Transfer pricing',
                ],
            ],
            'services' => [
                'kicker' => 'SERVICES',
                'title' => 'Six core areas of tax advisory support',
                'intro' => 'We cover both recurring tax obligations and more complex situations that require structured advice, risk assessment, and supporting documentation.',
                'items' => [
                    [
                        'title' => 'Tax Opinions',
                        'text' => 'Professional written advice for specific situations, with a clear explanation of the relevant rules, practice, and recommended course of action.',
                    ],
                    [
                        'title' => 'Tax Compliance',
                        'text' => 'Preparation, review, and submission of tax returns together with ongoing support in fulfilling tax obligations.',
                    ],
                    [
                        'title' => 'Tax Review',
                        'text' => 'A review of the tax position and audit simulation to identify risks early and prepare documentation before issues escalate.',
                    ],
                    [
                        'title' => 'Tax Optimization',
                        'text' => 'A structured review of operations aimed at identifying lawful opportunities to reduce the tax burden.',
                    ],
                    [
                        'title' => 'Tax Due Diligence',
                        'text' => 'In-depth tax analysis before acquisitions, disposals, mergers, or other strategic transactions.',
                    ],
                    [
                        'title' => 'Transfer Pricing',
                        'text' => 'Documentation, policies, and advisory support for related-party transactions in line with local rules and OECD standards.',
                    ],
                ],
            ],
            'compliance' => [
                'kicker' => 'TAX COMPLIANCE',
                'title' => 'Operational compliance for legal entities and individuals',
                'intro' => 'Our tax compliance services cover timely and accurate fulfillment of tax obligations, preparation of returns, year-round support, and communication with tax authorities.',
                'corporate' => [
                    'title' => 'Corporate Tax Compliance',
                    'intro' => 'We provide full support to companies and other legal entities in the areas of corporate income tax and VAT.',
                    'groups' => [
                        [
                            'title' => 'Corporate Income Tax (CIT)',
                            'items' => [
                                'preparation and filing of annual corporate income tax returns',
                                'tax computations and reconciliation of accounting and taxable profit',
                                'identification of non-deductible expenses and tax incentives',
                                'preparation of supporting documentation and explanations for the Tax Administration',
                                'assistance during tax audits and communication with tax authorities',
                            ],
                        ],
                        [
                            'title' => 'Value Added Tax (VAT)',
                            'items' => [
                                'preparation and submission of monthly and quarterly VAT returns',
                                'advice on the correct VAT treatment of transactions',
                                'review of VAT records and compliance with current regulations',
                                'support with VAT refunds and return adjustments',
                                'assistance in communication with the Tax Administration regarding VAT issues',
                            ],
                        ],
                    ],
                ],
                'individual' => [
                    'title' => 'Individual Tax Compliance',
                    'intro' => 'We assist residents and non-residents in meeting their Croatian tax obligations, with a focus on clarity, timeliness, and risk reduction.',
                    'items' => [
                        'preparation and filing of annual personal income tax returns',
                        'assistance with reporting domestic and foreign income',
                        'calculation of tax liabilities relating to employment, capital, property, and other income sources',
                        'advice on personal tax reliefs and exemptions',
                        'communication with the Tax Administration and support during inquiries or audits',
                    ],
                ],
            ],
            'review' => [
                'kicker' => 'TAX REVIEW',
                'title' => 'Audit readiness and tax risk assessment',
                'intro' => 'Our tax review service helps clients understand their tax position before the tax authorities raise the questions for them.',
                'body' => [
                    'The main purpose of this service is to prepare a company for a potential tax audit and identify areas that could lead to additional liabilities, interest, penalties, or business uncertainty.',
                    'As a practical alternative, we offer a tax audit simulation in which our professionals review VAT, corporate income tax, personal income tax, and other relevant areas as if they were conducting a full tax audit.',
                    'If an audit is already underway, we assist with objections to tax records, appeals against tax assessments, and support throughout the communication process with the tax authorities.',
                ],
                'highlights_title' => 'Client value',
                'highlights' => [
                    'a clearer view of the actual tax risk profile',
                    'prioritized corrective actions before an audit',
                    'better preparedness of documentation and processes',
                    'support if an audit is already in progress',
                ],
            ],
            'optimization' => [
                'kicker' => 'OPTIMIZATION',
                'title' => 'Unlocking lawful tax savings',
                'intro' => 'Tax optimization is not a one-off activity but an ongoing process that helps a company plan tax liabilities, reduce risks, and maintain financial stability.',
                'body' => [
                    'By reviewing accounting and tax documentation together with business processes, we identify situations in which a company is paying more tax than necessary under an optimal tax structure.',
                    'Our focus is on solutions that reduce the tax burden without disrupting the operating model while preserving regulatory compliance and business efficiency.',
                    'This turns tax optimization into a strategic tool that supports investments, reorganizations, day-to-day operations, and the preparation of important transactions.',
                ],
            ],
            'due_diligence' => [
                'kicker' => 'DUE DILIGENCE',
                'title' => 'Reliable tax analysis before strategic decisions',
                'intro' => 'Tax due diligence provides a clear view of a company’s tax position before an acquisition, disposal, merger, demerger, or investor entry.',
                'body' => [
                    'We analyse historical operations, tax filings, the regulatory framework, and possible risk exposure so the client understands potential liabilities and cash outflows before closing a transaction.',
                    'Our approach helps identify and quantify tax risks and ensures that the transaction remains transparent, controlled, and free from unwanted surprises after closing.',
                    'The result is a more reliable basis for decision-making, negotiation preparation, and transaction structuring, with a fuller understanding of the target company’s tax position.',
                ],
            ],
            'transfer_pricing' => [
                'kicker' => 'TRANSFER PRICING',
                'title' => 'Managing tax risk in international business',
                'intro' => 'Transfer pricing is critical for groups of related entities because it affects profit allocation and tax liabilities across jurisdictions.',
                'body' => [
                    'Misaligned or poorly documented transfer prices can lead to tax base adjustments, interest, and penalties. We therefore prepare transfer pricing studies including functional, risk, and asset analyses together with market benchmarking.',
                    'Beyond documentation, we help design transfer pricing policies that reflect the actual business model, advise on structuring related-party transactions, and support the integration of transfer pricing into financial and operational processes.',
                    'We also support clients during tax audits and disputes, including the preparation of responses to tax authority requests and representation in tax base adjustment procedures.',
                ],
            ],
            'meeting' => [
                'kicker' => 'CONTACT',
                'title' => 'Let’s discuss your tax matters',
                'intro' => 'If you need written tax advice, support in aligning tax processes, assistance during an audit, or guidance for a complex transaction, reach out to us. Together we can define the most suitable support model.',
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
            ],
            'blog_section' => [
                'kicker' => 'BLOG',
                'title' => 'Latest posts from :category',
                'intro' => 'Expert articles, updates, and practical insights related to tax, compliance, transactions, and tax risk.',
            ],
        ];
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
