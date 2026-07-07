START TRANSACTION;

UPDATE content_service_page_translations AS t
INNER JOIN content_service_pages AS p ON p.id = t.service_page_id
SET
    t.meta_description = 'Revizija financijskih izvještaja, konsolidiranih izvještaja, uvidi, ESG i specijalizirani revizorski angažmani.',
    t.payload = JSON_SET(
        COALESCE(t.payload, JSON_OBJECT()),
        '$.obligors',
        JSON_OBJECT(
            'kicker', 'OBVEZNICI',
            'title', 'Obveznici revizije',
            'intro', '',
            'display_mode', 'list',
            'primary_title', 'Revizija je zakonska obveza za:',
            'primary_items', JSON_ARRAY(
                'srednja i velika trgovačka društva',
                'društva od javnog interesa',
                JSON_OBJECT(
                    'text', 'dionička društva, komanditna društva, društva s ograničenom odgovornošću koja u godini koja prethodi reviziji prelaze barem dva od sljedeća tri uvjeta:',
                    'children', JSON_ARRAY(
                        'ukupna aktiva 2.500.000 eura',
                        'ukupni prihod 5.000.000 eura',
                        'prosječan broj zaposlenika tijekom poslovne godine iznosi najmanje 25'
                    )
                ),
                'društva uključena u statusne promjene',
                'korisnike EU sredstava kada je to propisano pravilima financiranja',
                'neprofitne organizacije koje su u prethodnoj godini ostvarile ukupan prihod veći od 398.168,43 eura do uključivo 1.327.228,08 eura (podliježu revizijskom uvidu)',
                'neprofitne organizacije koje su u prethodnoj godini ostvarile ukupan prihod veći od 1.327.228,08 eura (podliježu reviziji)'
            ),
            'note', 'Provedena revizija stvara dodanu vrijednost kako za upravu, tako i za vlasnike, banke i investitore, stoga ju društva mogu ugovoriti i dobrovoljno, kako bi unaprijedili svoje poslovanje.'
        ),
        '$.services',
        JSON_OBJECT(
            'kicker', 'USLUGE',
            'title', 'Naše revizijske usluge',
            'intro', '',
            'items', JSON_ARRAY(
                JSON_OBJECT(
                    'title', 'Revizija financijskih izvještaja',
                    'text', 'Revizija financijskih izvještaja u skladu sa Zakonom o reviziji i Međunarodnim revizijskim standardima. Neovisno i objektivno revizorsko mišljenje za veću vjerodostojnost financijskih informacija.'
                ),
                JSON_OBJECT(
                    'title', 'Konsolidirani financijski izvještaji',
                    'text', 'Revizija konsolidiranih financijskih izvještaja grupa društava s fokusom na kvalitetu procesa konsolidacije i transparentnost izvještavanja.'
                ),
                JSON_OBJECT(
                    'title', 'Pregledi i uvidi',
                    'text', 'Uvid u financijske izvještaje i pregledi financijskih informacija koji pružaju ograničeno uvjerenje o pouzdanosti financijskih podataka.'
                ),
                JSON_OBJECT(
                    'title', 'Održivost i ESG',
                    'text', 'Angažmani s izražavanjem ograničenog uvjerenja o izvještajima o održivosti i drugim nefinancijskim informacijama sukladno regulatornim zahtjevima i dobrim praksama.'
                ),
                JSON_OBJECT(
                    'title', 'Specijalizirani revizorski angažmani',
                    'text', 'Revizija financijskih izvještaja za posebne namjene, revizija EU projekata, revizija statusnih promjena i kapitala te ostali angažmani prilagođeni specifičnim potrebama klijenata.'
                ),
                JSON_OBJECT(
                    'title', 'IT revizija',
                    'text', 'Procjena informacijskih sustava i IT kontrola s ciljem povećanja sigurnosti, pouzdanosti i učinkovitosti poslovnih procesa.'
                )
            )
        )
    ),
    t.updated_at = NOW()
WHERE p.template_key = 'audit'
  AND t.locale = 'hr';

UPDATE content_service_page_translations AS t
INNER JOIN content_service_pages AS p ON p.id = t.service_page_id
SET
    t.meta_description = 'Audit of financial statements, consolidated statements, review engagements, ESG, and specialized audit engagements.',
    t.payload = JSON_SET(
        COALESCE(t.payload, JSON_OBJECT()),
        '$.obligors',
        JSON_OBJECT(
            'kicker', 'OBLIGATIONS',
            'title', 'Entities subject to statutory audit',
            'intro', '',
            'display_mode', 'list',
            'primary_title', 'Audit is a statutory obligation for:',
            'primary_items', JSON_ARRAY(
                'medium-sized and large companies',
                'public-interest entities',
                JSON_OBJECT(
                    'text', 'joint-stock companies, limited partnerships, and limited liability companies that in the year preceding the audit exceed at least two of the following three criteria:',
                    'children', JSON_ARRAY(
                        'total assets of EUR 2,500,000',
                        'total revenue of EUR 5,000,000',
                        'an average number of employees during the financial year of at least 25'
                    )
                ),
                'companies involved in status changes',
                'EU funds beneficiaries when required by financing rules',
                'non-profit organizations whose total revenue in the previous year exceeded EUR 398,168.43 up to and including EUR 1,327,228.08 (subject to review)',
                'non-profit organizations whose total revenue in the previous year exceeded EUR 1,327,228.08 (subject to audit)'
            ),
            'note', 'A completed audit creates added value for management, owners, banks, and investors, so companies may also engage it voluntarily to improve their business.'
        ),
        '$.services',
        JSON_OBJECT(
            'kicker', 'SERVICES',
            'title', 'Our audit services',
            'intro', '',
            'items', JSON_ARRAY(
                JSON_OBJECT(
                    'title', 'Audit of financial statements',
                    'text', 'Audit of financial statements in accordance with the Audit Act and International Standards on Auditing, with an independent and objective audit opinion for greater credibility of financial information.'
                ),
                JSON_OBJECT(
                    'title', 'Consolidated financial statements',
                    'text', 'Audit of consolidated financial statements for groups of companies, focused on the quality of the consolidation process and transparent reporting.'
                ),
                JSON_OBJECT(
                    'title', 'Reviews and insights',
                    'text', 'Review engagements and reviews of financial information that provide limited assurance about the reliability of financial data.'
                ),
                JSON_OBJECT(
                    'title', 'Sustainability and ESG',
                    'text', 'Limited assurance engagements for sustainability reports and other non-financial information in line with regulatory requirements and good practice.'
                ),
                JSON_OBJECT(
                    'title', 'Specialized audit engagements',
                    'text', 'Audits of special-purpose financial statements, EU project audits, audits of status changes and capital, and other engagements tailored to specific client needs.'
                ),
                JSON_OBJECT(
                    'title', 'IT audit',
                    'text', 'Assessment of information systems and IT controls to increase the security, reliability, and efficiency of business processes.'
                )
            )
        )
    ),
    t.updated_at = NOW()
WHERE p.template_key = 'audit'
  AND t.locale = 'en';

COMMIT;
