-- Home content blocks for ALPHA admin.
-- Safe to run multiple times. After running on live, clear Laravel cache if blocks were cached.
SET NAMES utf8mb4;

START TRANSACTION;

INSERT INTO content_blocks (code, name, type, is_active, payload, created_by, updated_by, created_at, updated_at)
VALUES ('home-alpha-hero', 'Home Alpha Hero', 'home_hero', 1, NULL, NULL, NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id),
    name = VALUES(name),
    type = VALUES(type),
    is_active = VALUES(is_active),
    payload = VALUES(payload),
    updated_at = NOW();
SET @home_alpha_hero_id = LAST_INSERT_ID();

INSERT INTO content_block_translations (content_block_id, locale, title, subtitle, body_html, cta_label, cta_url, payload, created_at, updated_at)
VALUES
    (
        @home_alpha_hero_id,
        'hr',
        'ALPHA CAPITALIS',
        'VAŠ KOMPAS KROZ SVIJET FINANCIJA',
        NULL,
        'Naše usluge',
        '/usluge',
        JSON_OBJECT(
            'secondary_cta_label', 'Ugovori sastanak',
            'secondary_cta_url', '/contact'
        ),
        NOW(),
        NOW()
    ),
    (
        @home_alpha_hero_id,
        'en',
        'ALPHA CAPITALIS',
        'YOUR COMPASS THROUGH THE WORLD OF FINANCE',
        NULL,
        'Our services',
        '/usluge',
        JSON_OBJECT(
            'secondary_cta_label', 'Book a meeting',
            'secondary_cta_url', '/contact'
        ),
        NOW(),
        NOW()
    )
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    subtitle = VALUES(subtitle),
    body_html = VALUES(body_html),
    cta_label = VALUES(cta_label),
    cta_url = VALUES(cta_url),
    payload = VALUES(payload),
    updated_at = NOW();

DELETE FROM content_block_slots
WHERE content_block_id = @home_alpha_hero_id
  AND placement = 'home.hero'
  AND target_type IS NULL
  AND target_ref IS NULL
  AND frontend_variant = 'desktop';

INSERT INTO content_block_slots (content_block_id, placement, frontend_variant, target_type, target_ref, sort_order, is_active, starts_at, ends_at, created_by, updated_by, created_at, updated_at)
VALUES (@home_alpha_hero_id, 'home.hero', 'desktop', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NOW(), NOW());

INSERT INTO content_blocks (code, name, type, is_active, payload, created_by, updated_by, created_at, updated_at)
VALUES ('home-alpha-stats', 'Home Alpha Stats', 'home_stats', 1, NULL, NULL, NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id),
    name = VALUES(name),
    type = VALUES(type),
    is_active = VALUES(is_active),
    payload = VALUES(payload),
    updated_at = NOW();
SET @home_alpha_stats_id = LAST_INSERT_ID();

INSERT INTO content_block_translations (content_block_id, locale, title, subtitle, body_html, cta_label, cta_url, payload, created_at, updated_at)
VALUES
    (
        @home_alpha_stats_id,
        'hr',
        'Home statistike',
        NULL,
        NULL,
        NULL,
        NULL,
        JSON_OBJECT(
            'stats',
            JSON_ARRAY(
                JSON_OBJECT('value', '300', 'suffix', '+', 'label', 'Odrađenih projekata'),
                JSON_OBJECT('value', '600', 'suffix', '+', 'label', 'Redovnih klijenata'),
                JSON_OBJECT('value', '60', 'suffix', '+', 'label', 'Kvalificiranih stručnjaka')
            )
        ),
        NOW(),
        NOW()
    ),
    (
        @home_alpha_stats_id,
        'en',
        'Home statistics',
        NULL,
        NULL,
        NULL,
        NULL,
        JSON_OBJECT(
            'stats',
            JSON_ARRAY(
                JSON_OBJECT('value', '300', 'suffix', '+', 'label', 'Completed projects'),
                JSON_OBJECT('value', '600', 'suffix', '+', 'label', 'Recurring clients'),
                JSON_OBJECT('value', '60', 'suffix', '+', 'label', 'Qualified experts')
            )
        ),
        NOW(),
        NOW()
    )
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    subtitle = VALUES(subtitle),
    body_html = VALUES(body_html),
    cta_label = VALUES(cta_label),
    cta_url = VALUES(cta_url),
    payload = VALUES(payload),
    updated_at = NOW();

DELETE FROM content_block_slots
WHERE content_block_id = @home_alpha_stats_id
  AND placement = 'home.stats'
  AND target_type IS NULL
  AND target_ref IS NULL
  AND frontend_variant = 'desktop';

INSERT INTO content_block_slots (content_block_id, placement, frontend_variant, target_type, target_ref, sort_order, is_active, starts_at, ends_at, created_by, updated_by, created_at, updated_at)
VALUES (@home_alpha_stats_id, 'home.stats', 'desktop', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NOW(), NOW());

INSERT INTO content_blocks (code, name, type, is_active, payload, created_by, updated_by, created_at, updated_at)
VALUES ('home-alpha-services', 'Home Alpha Services', 'home_services', 1, NULL, NULL, NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    id = LAST_INSERT_ID(id),
    name = VALUES(name),
    type = VALUES(type),
    is_active = VALUES(is_active),
    payload = VALUES(payload),
    updated_at = NOW();
SET @home_alpha_services_id = LAST_INSERT_ID();

INSERT INTO content_block_translations (content_block_id, locale, title, subtitle, body_html, cta_label, cta_url, payload, created_at, updated_at)
VALUES
    (
        @home_alpha_services_id,
        'hr',
        'Stvaramo vrijednost za naše klijente u',
        'ALPHA CAPITALIS čini tim stručnjaka iz područja revizije, računovodstva i financijskog savjetovanja. Kroz zajedničko djelovanje pružamo cjelovita rješenja poduzećima, investitorima i poduzetnicima koji žele sigurno rasti.',
        NULL,
        NULL,
        NULL,
        JSON_OBJECT(
            'title_accent', 'svim fazama razvoja poslovanja',
            'services',
            JSON_ARRAY(
                JSON_OBJECT(
                    'title', 'Revizija',
                    'subtitle', 'sigurnost i povjerenje u brojke',
                    'text', 'Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.',
                    'bullets',
                    JSON_ARRAY(
                        'Pomažemo vlasnicima, investitorima i upravi da imaju potpunu sigurnost u financijske izvještaje.',
                        'Revizija smanjuje rizik pogrešnih odluka jer potvrđuje da su podaci točni, potpuni i u skladu s propisima.',
                        'Kroz neovisnu provjeru dobivate jasnu sliku stvarnog financijskog stanja poduzeća, što jača povjerenje banaka, partnera i regulatora.'
                    ),
                    'url', '/revizija',
                    'action_label', 'Detaljnije'
                ),
                JSON_OBJECT(
                    'title', 'Računovodstvo',
                    'subtitle', 'kontrola i jasnoća poslovanja',
                    'text', 'Precizno vođenje knjiga i pravovremeno izvještavanje koje oslobađa menadžment za strateške odluke.',
                    'bullets',
                    JSON_ARRAY(
                        'Omogućujemo da vaše poslovanje bude financijski uredno, pregledno i uvijek spremno za odluke.',
                        'To znači da u svakom trenutku imate točne podatke o prihodima, troškovima i rezultatu, bez kašnjenja i nejasnoća.',
                        'Umjesto da reagirate na probleme, možete upravljati poslovanjem na temelju pouzdanih informacija.'
                    ),
                    'url', '/racunovodstvo',
                    'action_label', 'Detaljnije'
                ),
                JSON_OBJECT(
                    'title', 'Savjetovanje',
                    'subtitle', 'rast, optimizacija i bolji financijski izbor',
                    'text', 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
                    'bullets',
                    JSON_ARRAY(
                        'Pomažemo društvima, investitorima i poduzetnicima u donošenju kvalitetnih odluka, upravljanju rizicima i stvaranju dugoročne vrijednosti.',
                        'Pružamo podršku u procjenama vrijednosti, due diligence postupcima, M&A procesima i strukturiranju financiranja.',
                        'EU fondovi, bankovni krediti i porezne olakšice povezani su u okviru pribavljanja financiranja.'
                    ),
                    'url', '/savjetovanje',
                    'action_label', 'Detaljnije'
                )
            )
        ),
        NOW(),
        NOW()
    ),
    (
        @home_alpha_services_id,
        'en',
        'We create value for our clients in',
        'ALPHA CAPITALIS brings together experts in audit, accounting and financial advisory to support companies, investors and entrepreneurs through every stage of growth.',
        NULL,
        NULL,
        NULL,
        JSON_OBJECT(
            'title_accent', 'every stage of business growth',
            'services',
            JSON_ARRAY(
                JSON_OBJECT(
                    'title', 'Audit',
                    'subtitle', 'assurance and confidence in the numbers',
                    'text', 'Independent review of financial statements that increases confidence for owners, investors and partners.',
                    'bullets',
                    JSON_ARRAY(
                        'We help owners, investors and management gain confidence in financial statements.',
                        'Audit reduces the risk of wrong decisions by confirming that data is accurate, complete and compliant.',
                        'Through independent review you gain a clear view of the company financial position, strengthening trust with banks, partners and regulators.'
                    ),
                    'url', '/revizija',
                    'action_label', 'Learn more'
                ),
                JSON_OBJECT(
                    'title', 'Accounting',
                    'subtitle', 'control and clarity of operations',
                    'text', 'Precise bookkeeping and timely reporting that frees management for strategic decisions.',
                    'bullets',
                    JSON_ARRAY(
                        'We help keep your business financially organized, transparent and ready for decisions.',
                        'That means accurate data on revenue, costs and results at any moment, without delays or uncertainty.',
                        'Instead of reacting to problems, you can manage the business based on reliable information.'
                    ),
                    'url', '/racunovodstvo',
                    'action_label', 'Learn more'
                ),
                JSON_OBJECT(
                    'title', 'Advisory',
                    'subtitle', 'growth, optimization and better financial choices',
                    'text', 'Financial and tax advisory plus capital raising - all in one place.',
                    'bullets',
                    JSON_ARRAY(
                        'We help companies, investors and entrepreneurs make better decisions, manage risk and create long-term value.',
                        'We support valuations, due diligence, M&A processes and financing structuring.',
                        'EU funds, bank loans and tax incentives are connected within the capital raising framework.'
                    ),
                    'url', '/savjetovanje',
                    'action_label', 'Learn more'
                )
            )
        ),
        NOW(),
        NOW()
    )
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    subtitle = VALUES(subtitle),
    body_html = VALUES(body_html),
    cta_label = VALUES(cta_label),
    cta_url = VALUES(cta_url),
    payload = VALUES(payload),
    updated_at = NOW();

DELETE FROM content_block_slots
WHERE content_block_id = @home_alpha_services_id
  AND placement = 'home.services'
  AND target_type IS NULL
  AND target_ref IS NULL
  AND frontend_variant = 'desktop';

INSERT INTO content_block_slots (content_block_id, placement, frontend_variant, target_type, target_ref, sort_order, is_active, starts_at, ends_at, created_by, updated_by, created_at, updated_at)
VALUES (@home_alpha_services_id, 'home.services', 'desktop', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NOW(), NOW());

COMMIT;
