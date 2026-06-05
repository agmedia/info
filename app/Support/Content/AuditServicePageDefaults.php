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
                'intro' => 'Zakonska revizija obvezna je za velika i srednja društva prema Zakonu o računovodstvu, te za sva dionička društva neovisno o veličini. Prag za obveznu reviziju: prihodi iznad 30 mil. EUR, imovina iznad 15 mil. EUR ili više od 25 zaposlenih (zadovoljeni 2 od 3 kriterija u 2 uzastopne godine).',
                'primary_title' => 'Zakonska revizija obvezna je za',
                'primary_items' => [
                    'velika i srednja društva prema Zakonu o računovodstvu',
                    'sva dionička društva neovisno o veličini',
                ],
                'thresholds_title' => 'Prag za obveznu reviziju',
                'thresholds_intro' => 'Prag za obveznu reviziju primjenjuje se kada su zadovoljena 2 od 3 kriterija u 2 uzastopne godine:',
                'thresholds' => [
                    'prihodi iznad 30 mil. EUR',
                    'imovina iznad 15 mil. EUR',
                    'više od 25 zaposlenih',
                ],
                'note' => 'Revizija se može ugovoriti i dobrovoljno za društva koja žele povećati kredibilitet kod banaka, investitora ili poslovnih partnera, bez zakonske obveze.',
            ],
            'services' => [
                'kicker' => 'USLUGE',
                'title' => 'Naše revizijske usluge',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Zakonska revizija',
                        'text' => 'Revizija financijskih izvještaja prema MSFI i Zakonu o reviziji. Transparentan angažman od planiranja do mišljenja.',
                    ],
                    [
                        'title' => 'Dobrovoljna revizija',
                        'text' => 'Za društva koja žele povećati kredibilitet kod banaka, investitora ili poslovnih partnera, bez zakonske obveze.',
                    ],
                    [
                        'title' => 'Interna revizija',
                        'text' => 'Procjena internih kontrola i upravljačkih procesa. Preporuke za jačanje sustava nadzora i smanjenje operativnog rizika.',
                    ],
                    [
                        'title' => 'Revizija posebne namjene',
                        'text' => 'Angažmani vezani uz transakcije, restrukturiranja, stečajne postupke ili posebne zahtjeve dioničara.',
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
                'intro' => 'Statutory audit is mandatory for public-interest entities, large and medium-sized undertakings, and parent companies of large and medium-sized groups. It may also apply to joint-stock companies, limited liability companies, and limited partnerships when statutory thresholds are exceeded.',
                'primary_title' => 'Statutory audit is mandatory for',
                'primary_items' => [
                    'public-interest entities',
                    'large and medium-sized undertakings',
                    'parent companies of large and medium-sized groups',
                    'companies involved in status changes where an audit is required',
                    'EU funds beneficiaries when financing rules require an audit',
                ],
                'thresholds_title' => 'Thresholds for joint-stock, limited liability, and limited partnership companies',
                'thresholds_intro' => 'If the company is not already subject to audit on another basis, the obligation applies when it exceeds two of the following three criteria in the year preceding the audit:',
                'thresholds' => [
                    'total assets above EUR 2,500,000',
                    'net revenue above EUR 5,000,000',
                    'an average of at least 25 workers during the financial year',
                ],
                'note' => 'An audit may also be engaged voluntarily by companies that want to increase credibility with banks, investors, or business partners without a statutory obligation.',
            ],
            'services' => [
                'kicker' => 'SERVICES',
                'title' => 'Our audit services',
                'intro' => '',
                'items' => [
                    [
                        'title' => 'Statutory audit',
                        'text' => 'Audit of financial statements under IFRS and the Audit Act. A transparent engagement from planning to audit opinion.',
                    ],
                    [
                        'title' => 'Voluntary audit',
                        'text' => 'For companies that want to increase credibility with banks, investors, or business partners without a statutory obligation.',
                    ],
                    [
                        'title' => 'Internal audit',
                        'text' => 'Assessment of internal controls and governance processes, with recommendations for stronger oversight and lower operational risk.',
                    ],
                    [
                        'title' => 'Special-purpose audit',
                        'text' => 'Engagements connected to transactions, restructurings, insolvency proceedings, or specific shareholder requirements.',
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
