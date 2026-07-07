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
                'subtitle_accent' => '',
                'intro' => 'Neovisna, stručna provjera vaših financijskih izvještaja. Povećavamo povjerenje, smanjujemo rizike i jačamo kredibilitet vašeg poslovanja.',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'REVIZIJA',
                'title' => 'Što je revizija?',
                'intro' => '',
                'highlight_title' => 'Što je revizija?',
                'body' => [
                    'Revizija je neovisna provjera financijskih izvještaja društva s ciljem utvrđivanja daju li izvještaji istinit i pošten prikaz financijskog položaja. Revizor ne zastupa menadžment ni vlasnike - zastupa istinu u brojevima.',
                ],
            ],
            'obligors' => [
                'kicker' => 'OBVEZNICI',
                'title' => 'Obveznici revizije',
                'intro' => '',
                'display_mode' => 'list',
                'primary_title' => 'Revizija je zakonska obveza za:',
                'primary_items' => [
                    'srednja i velika trgovačka društva',
                    'društva od javnog interesa',
                    [
                        'text' => 'dionička društva, komanditna društva, društva s ograničenom odgovornošću koja u godini koja prethodi reviziji prelaze barem dva od sljedeća tri uvjeta:',
                        'children' => [
                            'ukupna aktiva 2.500.000 eura',
                            'ukupni prihod 5.000.000 eura',
                            'prosječan broj zaposlenika tijekom poslovne godine iznosi najmanje 25',
                        ],
                    ],
                    'društva uključena u statusne promjene',
                    'korisnike EU sredstava kada je to propisano pravilima financiranja',
                    'neprofitne organizacije koje su u prethodnoj godini ostvarile ukupan prihod veći od 398.168,43 eura do uključivo 1.327.228,08 eura (podliježu revizijskom uvidu)',
                    'neprofitne organizacije koje su u prethodnoj godini ostvarile ukupan prihod veći od 1.327.228,08 eura (podliježu reviziji)',
                ],
                'thresholds_title' => 'Prag za obveznu reviziju',
                'thresholds_intro' => 'Prag za obveznu reviziju primjenjuje se kada su zadovoljena 2 od 3 kriterija u 2 uzastopne godine:',
                'thresholds' => [
                    'prihodi iznad 30 mil. EUR',
                    'imovina iznad 15 mil. EUR',
                    'više od 25 zaposlenih',
                ],
                'note' => 'Provedena revizija stvara dodanu vrijednost kako za upravu, tako i za vlasnike, banke i investitore, stoga ju društva mogu ugovoriti i dobrovoljno, kako bi unaprijedili svoje poslovanje.',
            ],
            'services' => [
                'kicker' => 'USLUGE',
                'title' => 'Naše revizijske usluge',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Revizija financijskih izvještaja',
                        'text' => 'Revizija financijskih izvještaja u skladu sa Zakonom o reviziji i Međunarodnim revizijskim standardima. Neovisno i objektivno revizorsko mišljenje za veću vjerodostojnost financijskih informacija.',
                    ],
                    [
                        'title' => 'Konsolidirani financijski izvještaji',
                        'text' => 'Revizija konsolidiranih financijskih izvještaja grupa društava s fokusom na kvalitetu procesa konsolidacije i transparentnost izvještavanja.',
                    ],
                    [
                        'title' => 'Pregledi i uvidi',
                        'text' => 'Uvid u financijske izvještaje i pregledi financijskih informacija koji pružaju ograničeno uvjerenje o pouzdanosti financijskih podataka.',
                    ],
                    [
                        'title' => 'Održivost i ESG',
                        'text' => 'Angažmani s izražavanjem ograničenog uvjerenja o izvještajima o održivosti i drugim nefinancijskim informacijama sukladno regulatornim zahtjevima i dobrim praksama.',
                    ],
                    [
                        'title' => 'Specijalizirani revizorski angažmani',
                        'text' => 'Revizija financijskih izvještaja za posebne namjene, revizija EU projekata, revizija statusnih promjena i kapitala te ostali angažmani prilagođeni specifičnim potrebama klijenata.',
                    ],
                    [
                        'title' => 'IT revizija',
                        'text' => 'Procjena informacijskih sustava i IT kontrola s ciljem povećanja sigurnosti, pouzdanosti i učinkovitosti poslovnih procesa.',
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
                'title' => 'Naš pristup',
                'intro' => 'Svaki revizijski angažman počinjemo s razumijevanjem vašeg poslovanja - ne s checklistom. Planiramo reviziju prema specifičnim rizicima vaše industrije i veličine, komuniciramo otvoreno kroz cijeli proces i zaključujemo jasnim mišljenjem bez iznenađenja.',
                'body' => [
                    'Svaki revizijski angažman počinjemo s razumijevanjem vašeg poslovanja - ne s checklistom. Planiramo reviziju prema specifičnim rizicima vaše industrije i veličine, komuniciramo otvoreno kroz cijeli proces i zaključujemo jasnim mišljenjem bez iznenađenja.',
                ],
            ],
            'meeting' => [
                'kicker' => 'KONTAKT',
                'title' => 'Razgovarajmo o vašem revizorskom angažmanu',
                'intro' => 'Javite nam se - procijenit ćemo vaše potrebe i predložiti pristup koji odgovara veličini i specifičnostima vašeg poslovanja.',
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
                'subtitle_lead' => 'Audit',
                'subtitle_accent' => '',
                'intro' => 'Independent, expert review of your financial statements. We increase trust, reduce risk, and strengthen the credibility of your business.',
                'cta_label' => '',
                'cta_url' => '',
            ],
            'overview' => [
                'kicker' => 'AUDIT',
                'title' => 'What is an audit?',
                'intro' => '',
                'highlight_title' => 'What is an audit?',
                'body' => [
                    'An audit is an independent examination of a company’s financial statements to determine whether they present a true and fair view of its financial position. The auditor does not represent management or owners - the auditor represents the truth in the numbers.',
                ],
            ],
            'obligors' => [
                'kicker' => 'OBLIGATIONS',
                'title' => 'Entities subject to statutory audit',
                'intro' => '',
                'display_mode' => 'list',
                'primary_title' => 'Audit is a statutory obligation for:',
                'primary_items' => [
                    'medium-sized and large companies',
                    'public-interest entities',
                    [
                        'text' => 'joint-stock companies, limited partnerships, and limited liability companies that in the year preceding the audit exceed at least two of the following three criteria:',
                        'children' => [
                            'total assets of EUR 2,500,000',
                            'total revenue of EUR 5,000,000',
                            'an average number of employees during the financial year of at least 25',
                        ],
                    ],
                    'companies involved in status changes',
                    'EU funds beneficiaries when required by financing rules',
                    'non-profit organizations whose total revenue in the previous year exceeded EUR 398,168.43 up to and including EUR 1,327,228.08 (subject to review)',
                    'non-profit organizations whose total revenue in the previous year exceeded EUR 1,327,228.08 (subject to audit)',
                ],
                'thresholds_title' => 'Thresholds for joint-stock, limited liability, and limited partnership companies',
                'thresholds_intro' => 'If the company is not already subject to audit on another basis, the obligation applies when it exceeds two of the following three criteria in the year preceding the audit:',
                'thresholds' => [
                    'total assets above EUR 2,500,000',
                    'net revenue above EUR 5,000,000',
                    'an average of at least 25 workers during the financial year',
                ],
                'note' => 'A completed audit creates added value for management, owners, banks, and investors, so companies may also engage it voluntarily to improve their business.',
            ],
            'services' => [
                'kicker' => 'SERVICES',
                'title' => 'Our audit services',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Audit of financial statements',
                        'text' => 'Audit of financial statements in accordance with the Audit Act and International Standards on Auditing, with an independent and objective audit opinion for greater credibility of financial information.',
                    ],
                    [
                        'title' => 'Consolidated financial statements',
                        'text' => 'Audit of consolidated financial statements for groups of companies, focused on the quality of the consolidation process and transparent reporting.',
                    ],
                    [
                        'title' => 'Reviews and insights',
                        'text' => 'Review engagements and reviews of financial information that provide limited assurance about the reliability of financial data.',
                    ],
                    [
                        'title' => 'Sustainability and ESG',
                        'text' => 'Limited assurance engagements for sustainability reports and other non-financial information in line with regulatory requirements and good practice.',
                    ],
                    [
                        'title' => 'Specialized audit engagements',
                        'text' => 'Audits of special-purpose financial statements, EU project audits, audits of status changes and capital, and other engagements tailored to specific client needs.',
                    ],
                    [
                        'title' => 'IT audit',
                        'text' => 'Assessment of information systems and IT controls to increase the security, reliability, and efficiency of business processes.',
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
                'title' => 'Our approach',
                'intro' => 'Every audit engagement starts with understanding your business - not with a checklist. We plan the audit around the specific risks of your industry and size, communicate openly throughout the process, and conclude with a clear opinion without surprises.',
                'body' => [
                    'Every audit engagement starts with understanding your business - not with a checklist. We plan the audit around the specific risks of your industry and size, communicate openly throughout the process, and conclude with a clear opinion without surprises.',
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
