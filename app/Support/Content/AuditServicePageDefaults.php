<?php

namespace App\Support\Content;

class AuditServicePageDefaults
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
                'subtitle_lead' => 'Revizija',
                'subtitle_accent' => 'financijskih izvještaja',
                'intro' => 'Revizija financijskih izvještaja nije samo zakonska obveza. Ona stvara sigurnost da su financijski podaci točni, transparentni i pouzdani za donošenje poslovnih odluka.',
                'cta_label' => 'Pregledajte sekcije',
                'cta_url' => '#audit-overview',
            ],
            'overview' => [
                'kicker' => 'REVIZIJA',
                'title' => 'Pouzdana osnova za donošenje poslovnih odluka',
                'intro' => 'U Alpha Capitalisu reviziji pristupamo profesionalno i partnerski, s ciljem da klijentima pružimo jasnoću, sigurnost i povjerenje kroz cijeli angažman.',
                'highlight_title' => 'Što je revizija?',
                'body' => [
                    'Revizija je neovisna provjera financijskih izvještaja kojom se utvrđuje jesu li sastavljeni u skladu s važećim računovodstvenim standardima i propisima te prikazuju li istinito financijski položaj i rezultate poslovanja društva.',
                    'Dobro provedena revizija daje upravi, vlasnicima, bankama i investitorima kvalitetniju osnovu za donošenje odluka, bolju kontrolu rizika i veću razinu povjerenja u financijske informacije.',
                ],
            ],
            'obligors' => [
                'kicker' => 'OBVEZNICI',
                'title' => 'Tko podliježe reviziji i kada je smisleno ugovoriti ju dobrovoljno',
                'intro' => 'Zakonska obveza obuhvaća više vrsta subjekata i situacija, a revizija se često ugovara i dobrovoljno kada društvo želi povećati transparentnost i spremnost za banke, investitore ili statusne promjene.',
                'primary_title' => 'Revizija je zakonska obveza za',
                'primary_items' => [
                    'srednja i velika trgovačka društva',
                    'društva od javnog interesa',
                    'društva uključena u statusne promjene',
                    'korisnike EU sredstava kada je to propisano pravilima financiranja',
                    'neprofitne organizacije koje prelaze pragove za revizijski uvid ili reviziju',
                ],
                'thresholds_title' => 'Pragovi za dionička, komanditna i društva s ograničenom odgovornošću',
                'thresholds_intro' => 'Obveza postoji kada društvo u godini koja prethodi reviziji prelazi barem dva od sljedeća tri uvjeta:',
                'thresholds' => [
                    'ukupna aktiva 2.500.000 eura',
                    'ukupni prihod 5.000.000 eura',
                    'prosječan broj zaposlenika tijekom poslovne godine najmanje 25',
                ],
                'note' => 'Revizija se može ugovoriti i dobrovoljno kako bi društvo unaprijedilo poslovanje, pripremilo se za financiranje ili povećalo povjerenje ključnih dionika.',
            ],
            'services' => [
                'kicker' => 'USLUGE',
                'title' => 'Naše revizijske usluge',
                'intro' => 'Svaki angažman prilagođavamo poslovanju i specifičnim rizicima klijenta, uz jasan proces rada, pravovremenu komunikaciju i fokus na vrijednost za upravu i vlasnike.',
                'items' => [
                    [
                        'title' => 'Revizija godišnjih i konsolidiranih financijskih izvještaja',
                        'text' => 'Neovisna provjera financijskih izvještaja uz fokus na točnost, transparentnost i usklađenost s regulatornim zahtjevima.',
                    ],
                    [
                        'title' => 'Revizija financijskih izvještaja za posebne namjene',
                        'text' => 'Angažmani prilagođeni specifičnim zahtjevima vlasnika, investitora, kreditora ili drugih korisnika financijskih informacija.',
                    ],
                    [
                        'title' => 'Revizija EU projekata',
                        'text' => 'Provjera usklađenosti troškova, dokumentacije i izvještavanja u skladu s pravilima financiranja i provedbe projekata.',
                    ],
                    [
                        'title' => 'Uvid u financijske izvještaje',
                        'text' => 'Ograničeno uvjerenje kada je potrebna niža razina angažmana, ali i dalje jasan neovisan uvid u financijske informacije.',
                    ],
                    [
                        'title' => 'Revizija statusnih promjena i kapitala',
                        'text' => 'Podrška kod povećanja ili smanjenja kapitala, spajanja, pripajanja, podjela i drugih statusnih promjena.',
                    ],
                    [
                        'title' => 'Izražavanje ograničenog uvjerenja o izvještaju o održivosti',
                        'text' => 'Angažmani koji podržavaju transparentnije ESG i održivost izvještavanje te usklađenost sa zahtjevima tržišta i regulative.',
                    ],
                    [
                        'title' => 'IT revizija',
                        'text' => 'Procjena kontrola i rizika unutar informacijskih sustava koji podržavaju financijsko izvještavanje i ključne poslovne procese.',
                    ],
                    [
                        'title' => 'Pregledi financijskih informacija',
                        'text' => 'Pregledi i limited assurance angažmani kada je potrebna dodatna sigurnost u izvještavanje tijekom godine ili za posebne svrhe.',
                    ],
                    [
                        'title' => 'Ostali posebni revizorski angažmani',
                        'text' => 'Specifični angažmani prema potrebama klijenta, industriji i kontekstu donošenja poslovnih odluka.',
                    ],
                ],
            ],
            'value' => [
                'kicker' => 'VRIJEDNOST',
                'title' => 'Što revizija donosi društvu',
                'intro' => 'Provedena revizija stvara dodanu vrijednost za upravu, vlasnike, banke i investitore jer pomaže u pravovremenom uočavanju slabosti, boljem upravljanju rizicima i kvalitetnijem odlučivanju.',
                'benefits' => [
                    'povećano povjerenje vlasnika, banaka i investitora',
                    'smanjenje regulatornih i poslovnih rizika',
                    'povećanje transparentnosti financijskih informacija',
                    'pravovremeno uočavanje slabosti u procesima i kontrolama',
                    'sigurnija podloga za donošenje poslovnih odluka',
                ],
                'conclusion' => 'Revizija nije samo formalnost niti trošak. Ona je ulaganje u stabilnost, odgovorno poslovanje i dugoročnu vrijednost društva.',
            ],
            'approach' => [
                'kicker' => 'PRISTUP',
                'title' => 'Kako radimo i zašto klijenti biraju Alpha Capitalis',
                'intro' => 'Naš pristup kombinira procjenu rizika, razumijevanje poslovanja i jasnu komunikaciju kroz cijeli angažman, kako bi klijent dobio ne samo mišljenje nego i korisne uvide za upravljanje poslovanjem.',
                'principles_title' => 'Odlike našeg pristupa reviziji',
                'principles' => [
                    'procjena rizika i razumijevanje poslovanja klijenta',
                    'jasna i otvorena komunikacija tijekom cijelog procesa',
                    'pravovremene informacije i preporuke tijekom angažmana',
                    'poštivanje rokova i regulatornih zahtjeva',
                ],
                'reasons_title' => 'Zašto Alpha Capitalis?',
                'reasons' => [
                    'iskusni i licencirani revizori',
                    'individualni pristup svakom klijentu',
                    'razumijevanje regulatornog okvira i poslovne prakse',
                    'pouzdan partner u izazovnim situacijama',
                ],
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašem revizorskom angažmanu',
                'intro' => 'Ako trebate zakonsku reviziju, reviziju za posebne namjene ili neovisni pregled financijskih informacija, javite nam se. Zajedno možemo definirati opseg angažmana, rokove i sljedeće korake.',
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
                'intro' => 'Članci, novosti i stručni uvidi vezani uz reviziju, financijsko izvještavanje, kontrole i usklađenost.',
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
                'subtitle_lead' => 'Audit of',
                'subtitle_accent' => 'financial statements',
                'intro' => 'The audit of financial statements is not merely a statutory obligation. It provides assurance that financial information is accurate, transparent, and reliable for sound business decision-making.',
                'cta_label' => 'Explore sections',
                'cta_url' => '#audit-overview',
            ],
            'overview' => [
                'kicker' => 'AUDIT',
                'title' => 'A reliable basis for business decision-making',
                'intro' => 'At Alpha Capitalis, we approach audits professionally and in partnership with our clients, with the aim of providing clarity, security, and trust throughout the engagement.',
                'highlight_title' => 'What is an audit?',
                'body' => [
                    'An audit is an independent examination of financial statements to determine whether they have been prepared in accordance with applicable accounting standards and regulations and whether they present a true and fair view of the company’s financial position and operating results.',
                    'A well-executed audit provides management, owners, banks, and investors with a stronger basis for decision-making, better control of risks, and a higher level of confidence in financial information.',
                ],
            ],
            'obligors' => [
                'kicker' => 'OBLIGATIONS',
                'title' => 'Who is subject to an audit and when a voluntary engagement makes sense',
                'intro' => 'A statutory audit applies to several types of entities and situations, while many companies also engage auditors voluntarily to improve transparency and readiness for banks, investors, or status changes.',
                'primary_title' => 'An audit is a legal obligation for',
                'primary_items' => [
                    'medium-sized and large companies',
                    'public-interest entities',
                    'companies involved in status changes',
                    'beneficiaries of EU funds when required by financing rules',
                    'non-profit organisations that exceed the thresholds for review or statutory audit',
                ],
                'thresholds_title' => 'Thresholds for joint-stock companies, limited partnerships, and limited liability companies',
                'thresholds_intro' => 'The obligation applies when, in the year preceding the audit, the company exceeds at least two of the following three criteria:',
                'thresholds' => [
                    'total assets of EUR 2,500,000',
                    'total revenue of EUR 5,000,000',
                    'an average of at least 25 employees during the financial year',
                ],
                'note' => 'An audit may also be engaged voluntarily to improve operations, prepare for financing, or increase the confidence of key stakeholders.',
            ],
            'services' => [
                'kicker' => 'SERVICES',
                'title' => 'Our audit services',
                'intro' => 'Each engagement is tailored to the client’s business and risk profile, with a clear process, timely communication, and a focus on value for management and owners.',
                'items' => [
                    [
                        'title' => 'Audit of annual and consolidated financial statements',
                        'text' => 'Independent assurance over financial statements with a focus on accuracy, transparency, and compliance with regulatory requirements.',
                    ],
                    [
                        'title' => 'Audit of financial statements prepared for special purposes',
                        'text' => 'Engagements tailored to the needs of owners, investors, lenders, and other users of financial information.',
                    ],
                    [
                        'title' => 'Audit of EU-funded projects',
                        'text' => 'Verification of costs, documentation, and reporting in line with project financing and implementation rules.',
                    ],
                    [
                        'title' => 'Review of financial statements',
                        'text' => 'Limited assurance engagements when a lower level of assurance is appropriate but an independent view is still needed.',
                    ],
                    [
                        'title' => 'Audit of status changes and capital',
                        'text' => 'Support for capital increases or decreases, mergers, demergers, and other status changes.',
                    ],
                    [
                        'title' => 'Limited assurance on sustainability reports',
                        'text' => 'Engagements that support more transparent ESG and sustainability reporting and better alignment with market and regulatory expectations.',
                    ],
                    [
                        'title' => 'IT audit',
                        'text' => 'Assessment of controls and risks within information systems that support financial reporting and core business processes.',
                    ],
                    [
                        'title' => 'Reviews of financial information',
                        'text' => 'Reviews and limited assurance engagements for interim reporting or other specific reporting needs.',
                    ],
                    [
                        'title' => 'Other special audit engagements',
                        'text' => 'Specific engagements tailored to the client’s needs, industry context, and decision-making requirements.',
                    ],
                ],
            ],
            'value' => [
                'kicker' => 'VALUE',
                'title' => 'What an audit brings to a company',
                'intro' => 'A completed audit creates added value for management, owners, banks, and investors by helping detect weaknesses early, manage risks better, and make better-informed decisions.',
                'benefits' => [
                    'increased confidence of owners, banks, and investors',
                    'reduction of regulatory and business risks',
                    'greater transparency of financial information',
                    'timely identification of weaknesses in processes and controls',
                    'a stronger basis for business decision-making',
                ],
                'conclusion' => 'An audit is not merely a formality or a cost. It is an investment in stability, responsible business operations, and long-term value.',
            ],
            'approach' => [
                'kicker' => 'APPROACH',
                'title' => 'How we work and why clients choose Alpha Capitalis',
                'intro' => 'Our approach combines risk assessment, a strong understanding of the business, and clear communication throughout the engagement so clients receive more than an opinion. They gain useful insight for managing the business.',
                'principles_title' => 'Key characteristics of our audit approach',
                'principles' => [
                    'risk assessment and a thorough understanding of the client’s business',
                    'clear and open communication throughout the process',
                    'timely information and recommendations during the engagement',
                    'respect for deadlines and regulatory requirements',
                ],
                'reasons_title' => 'Why Alpha Capitalis?',
                'reasons' => [
                    'experienced and licensed auditors',
                    'an individual approach to each client',
                    'strong understanding of the regulatory framework and business practice',
                    'a reliable partner in demanding situations',
                ],
            ],
            'meeting' => [
                'kicker' => 'CONTACT',
                'title' => 'Let’s discuss your audit engagement',
                'intro' => 'If you need a statutory audit, a special-purpose audit, or an independent review of financial information, reach out to us. Together, we can define the engagement scope, timing, and next steps.',
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
                'intro' => 'Articles, updates, and expert insights related to audit, financial reporting, controls, and compliance.',
            ],
        ];
    }

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
