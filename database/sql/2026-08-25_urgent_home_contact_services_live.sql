-- Urgent client updates for the homepage, contact page, and service pages.
-- This script is idempotent and targets the current HR/EN CMS structure.
-- Deploy the application code first, run this script only if migrations are not
-- being executed on live, then run: php artisan optimize:clear

SET NAMES utf8mb4;
START TRANSACTION;

-- Homepage/contact statistics: exactly three equal items, with no experience card.
UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-stats'
SET translation.payload = JSON_SET(
        COALESCE(translation.payload, JSON_OBJECT()),
        '$.stats',
        CASE translation.locale
            WHEN 'hr' THEN JSON_ARRAY(
                JSON_OBJECT('label', 'Odrađenih projekata', 'value', '300', 'suffix', '+'),
                JSON_OBJECT('label', 'Redovnih klijenata', 'value', '700', 'suffix', ''),
                JSON_OBJECT('label', 'Kvalificiranih stručnjaka', 'value', '75', 'suffix', '')
            )
            ELSE JSON_ARRAY(
                JSON_OBJECT('label', 'Completed projects', 'value', '300', 'suffix', '+'),
                JSON_OBJECT('label', 'Regular clients', 'value', '700', 'suffix', ''),
                JSON_OBJECT('label', 'Qualified professionals', 'value', '75', 'suffix', '')
            )
        END,
        '$.contact_stats',
        CASE translation.locale
            WHEN 'hr' THEN JSON_ARRAY(
                JSON_OBJECT('label', 'Odrađenih projekata', 'value', '300', 'suffix', '+'),
                JSON_OBJECT('label', 'Redovnih klijenata', 'value', '700', 'suffix', ''),
                JSON_OBJECT('label', 'Kvalificiranih stručnjaka', 'value', '75', 'suffix', '')
            )
            ELSE JSON_ARRAY(
                JSON_OBJECT('label', 'Completed projects', 'value', '300', 'suffix', '+'),
                JSON_OBJECT('label', 'Regular clients', 'value', '700', 'suffix', ''),
                JSON_OBJECT('label', 'Qualified professionals', 'value', '75', 'suffix', '')
            )
        END,
        '$.contact_page.direct_phone', '+385 (1) 580 6656'
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en');

-- Office numbers and localized office labels. JSON_SEARCH keeps this independent
-- of the current location order inside the CMS array.
UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-stats'
SET translation.payload = JSON_SET(
        translation.payload,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis', NULL, '$.locations.items[*].entity_key')),
            '.entity_key',
            '.phone'
        ),
        '+385 (1) 580 6656'
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en')
  AND JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis', NULL, '$.locations.items[*].entity_key') IS NOT NULL;

UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-stats'
SET translation.payload = JSON_SET(
        translation.payload,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-east', NULL, '$.locations.items[*].entity_key')),
            '.entity_key',
            '.phone'
        ),
        '+385 (1) 580 6656'
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en')
  AND JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-east', NULL, '$.locations.items[*].entity_key') IS NOT NULL;

UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-stats'
SET translation.payload = JSON_SET(
        translation.payload,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-timia', NULL, '$.locations.items[*].entity_key')),
            '.entity_key',
            '.phone'
        ),
        '+385 (0) 51 301 503'
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en')
  AND JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-timia', NULL, '$.locations.items[*].entity_key') IS NOT NULL;

UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-stats'
SET translation.payload = JSON_SET(
        translation.payload,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis', NULL, '$.locations.items[*].entity_key')),
            '.entity_key',
            '.office_label'
        ),
        CASE translation.locale WHEN 'hr' THEN 'Ured Zagreb' ELSE 'Zagreb Office' END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-east', NULL, '$.locations.items[*].entity_key')),
            '.entity_key',
            '.office_label'
        ),
        CASE translation.locale WHEN 'hr' THEN 'Ured Vinkovci' ELSE 'Vinkovci Office' END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-timia', NULL, '$.locations.items[*].entity_key')),
            '.entity_key',
            '.office_label'
        ),
        CASE translation.locale WHEN 'hr' THEN 'Ured Rijeka' ELSE 'Rijeka Office' END
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en')
  AND JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis', NULL, '$.locations.items[*].entity_key') IS NOT NULL
  AND JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-east', NULL, '$.locations.items[*].entity_key') IS NOT NULL
  AND JSON_SEARCH(translation.payload, 'one', 'alpha-capitalis-timia', NULL, '$.locations.items[*].entity_key') IS NOT NULL;

-- Updated service name in the homepage hero.
UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-hero'
SET translation.subtitle = CASE translation.locale
        WHEN 'hr' THEN 'Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.'
        ELSE 'Accounting and Tax Advisory, Audit and Advisory — all in one place.'
    END,
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en');

-- Homepage service cards. Locate cards by key instead of relying on array order.
UPDATE content_block_translations AS translation
INNER JOIN content_blocks AS block
    ON block.id = translation.content_block_id
    AND block.code = 'home-alpha-services'
SET translation.payload = JSON_SET(
        translation.payload,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.services[*].key')),
            '.key',
            '.title'
        ),
        CASE translation.locale WHEN 'hr' THEN 'Računovodstvo i porezi' ELSE 'Accounting and Tax Advisory' END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.services[*].key')),
            '.key',
            '.subtitle'
        ),
        CASE translation.locale WHEN 'hr' THEN 'kontrola, jasnoća i porezna sigurnost' ELSE 'control, clarity and tax confidence' END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.services[*].key')),
            '.key',
            '.text'
        ),
        CASE translation.locale
            WHEN 'hr' THEN 'Precizno vođenje knjiga, pravovremeno izvještavanje i porezno savjetovanje za sigurnije poslovne odluke.'
            ELSE 'Accurate bookkeeping, timely reporting and tax advisory for more confident business decisions.'
        END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'advisory', NULL, '$.services[*].key')),
            '.key',
            '.text'
        ),
        CASE translation.locale
            WHEN 'hr' THEN 'Financijsko i strateško savjetovanje te pribavljanje kapitala - sve na jednom mjestu.'
            ELSE 'Financial and strategic advisory, along with capital raising — all in one place.'
        END
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en')
  AND JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.services[*].key') IS NOT NULL
  AND JSON_SEARCH(translation.payload, 'one', 'advisory', NULL, '$.services[*].key') IS NOT NULL;

-- Services overview cards.
UPDATE content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'services' OR page.template_key = 'services_index')
SET translation.meta_description = CASE translation.locale
        WHEN 'hr' THEN 'Pregled usluga ALPHA CAPITALISA: revizija, računovodstvo i porezi te poslovno savjetovanje.'
        ELSE 'Overview of ALPHA CAPITALIS services: audit, accounting and tax advisory, and business advisory.'
    END,
    translation.payload = JSON_SET(
        translation.payload,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.primary_pillars[*].key')),
            '.key',
            '.title'
        ),
        CASE translation.locale WHEN 'hr' THEN 'Računovodstvo i porezi' ELSE 'Accounting and Tax Advisory' END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.primary_pillars[*].key')),
            '.key',
            '.subtitle'
        ),
        CASE translation.locale WHEN 'hr' THEN 'kontrola, jasnoća i porezna sigurnost' ELSE 'control, clarity and tax confidence' END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.primary_pillars[*].key')),
            '.key',
            '.text'
        ),
        CASE translation.locale
            WHEN 'hr' THEN 'Precizno vođenje knjiga, pravovremeno izvještavanje i porezno savjetovanje za sigurnije poslovne odluke.'
            ELSE 'Accurate bookkeeping, timely reporting and tax advisory for more confident business decisions.'
        END,
        REPLACE(
            JSON_UNQUOTE(JSON_SEARCH(translation.payload, 'one', 'advisory', NULL, '$.primary_pillars[*].key')),
            '.key',
            '.text'
        ),
        CASE translation.locale
            WHEN 'hr' THEN 'Financijsko i strateško savjetovanje te pribavljanje kapitala - sve na jednom mjestu.'
            ELSE 'Financial and strategic advisory, along with capital raising — all in one place.'
        END
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en')
  AND JSON_SEARCH(translation.payload, 'one', 'accounting', NULL, '$.primary_pillars[*].key') IS NOT NULL
  AND JSON_SEARCH(translation.payload, 'one', 'advisory', NULL, '$.primary_pillars[*].key') IS NOT NULL;

-- Accounting and Tax Advisory page: name, SEO copy, section labels, and cards.
UPDATE content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'racunovodstvo' OR page.template_key = 'accounting')
SET translation.title = CASE translation.locale WHEN 'hr' THEN 'Računovodstvo i porezi' ELSE 'Accounting and Tax Advisory' END,
    translation.meta_title = CASE translation.locale WHEN 'hr' THEN 'Računovodstvo i porezi' ELSE 'Accounting and Tax Advisory' END,
    translation.meta_description = CASE translation.locale
        WHEN 'hr' THEN 'Računovodstvena i porezna podrška, vođenje poslovnih knjiga, obračun plaća, porezno savjetovanje i izvještavanje za svakodnevno poslovanje.'
        ELSE 'Accounting and tax advisory support, bookkeeping, payroll, tax compliance, and reporting for day-to-day business operations.'
    END,
    translation.payload = JSON_SET(
        COALESCE(translation.payload, JSON_OBJECT()),
        '$.hero.subtitle_lead', CASE translation.locale WHEN 'hr' THEN 'Računovodstvo i porezi' ELSE 'Accounting and Tax Advisory' END,
        '$.hero.image_alt', CASE translation.locale WHEN 'hr' THEN 'Usluge računovodstva i poreznog savjetovanja' ELSE 'Accounting and tax advisory services' END,
        '$.overview.kicker', CASE translation.locale WHEN 'hr' THEN 'RAČUNOVODSTVO I POREZI' ELSE 'ACCOUNTING AND TAX ADVISORY' END,
        '$.services.title', CASE translation.locale WHEN 'hr' THEN 'Naše usluge računovodstva i poreza' ELSE 'Our Accounting and Tax Advisory Services' END,
        '$.intro_section.kicker', CASE translation.locale WHEN 'hr' THEN 'RAČUNOVODSTVO I POREZI' ELSE 'ACCOUNTING AND TAX ADVISORY' END,
        '$.intro_section.title', CASE translation.locale WHEN 'hr' THEN 'Usluge računovodstva i poreza' ELSE 'Accounting and Tax Advisory Services' END,
        '$.intro_section.body[1]', CASE translation.locale WHEN 'hr' THEN 'Usluge računovodstva i poreznog savjetovanja:' ELSE 'Accounting and Tax Advisory services:' END
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en');

-- Preserve the first existing tax card object (including editor-managed copy and
-- any extra fields), remove consolidation and duplicate tax cards, then place tax
-- immediately after Financial Accounting. If tax is missing, insert the same
-- locale-specific default used by the PHP migration.
WITH
accounting_targets AS (
    SELECT
        translation.id AS translation_id,
        translation.locale,
        COALESCE(translation.payload, JSON_OBJECT()) AS payload
    FROM content_service_page_translations AS translation
    INNER JOIN content_service_pages AS page
        ON page.id = translation.service_page_id
        AND (page.code = 'racunovodstvo' OR page.template_key = 'accounting')
    WHERE translation.locale IN ('hr', 'en')
),
accounting_items AS (
    SELECT
        target.translation_id,
        target.locale,
        item_rows.item_ordinal - 1 AS original_position,
        JSON_EXTRACT(
            target.payload,
            CONCAT('$.services.items[', item_rows.item_ordinal - 1, ']')
        ) AS item_json
    FROM accounting_targets AS target
    CROSS JOIN JSON_TABLE(
        COALESCE(JSON_EXTRACT(target.payload, '$.services.items'), JSON_ARRAY()),
        '$[*]' COLUMNS (item_ordinal FOR ORDINALITY)
    ) AS item_rows
),
accounting_normalized AS (
    SELECT
        item.*,
        LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.title')), ''))) AS item_title,
        LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.url')), ''))) AS item_url
    FROM accounting_items AS item
),
accounting_marked AS (
    SELECT
        item.*,
        (
            item.item_title IN ('porezno savjetovanje', 'tax advisory')
            OR INSTR(item.item_url, 'porezno-savjetovanje') > 0
            OR INSTR(item.item_url, 'tax-advisory') > 0
        ) AS is_tax,
        item.item_title IN ('konsolidacija', 'consolidation') AS is_consolidation,
        item.item_title IN ('financijsko računovodstvo', 'financial accounting') AS is_financial
    FROM accounting_normalized AS item
),
accounting_filtered AS (
    SELECT
        item.*,
        ROW_NUMBER() OVER (
            PARTITION BY item.translation_id
            ORDER BY item.original_position
        ) - 1 AS filtered_position
    FROM accounting_marked AS item
    WHERE item.is_tax = 0
      AND item.is_consolidation = 0
),
accounting_filtered_stats AS (
    SELECT
        item.translation_id,
        COUNT(*) AS filtered_count,
        MIN(CASE WHEN item.is_financial = 1 THEN item.filtered_position END) AS financial_position
    FROM accounting_filtered AS item
    GROUP BY item.translation_id
),
accounting_tax_ranked AS (
    SELECT
        item.translation_id,
        item.item_json,
        ROW_NUMBER() OVER (
            PARTITION BY item.translation_id
            ORDER BY item.original_position
        ) AS tax_rank
    FROM accounting_marked AS item
    WHERE item.is_tax = 1
),
accounting_existing_tax AS (
    SELECT item.translation_id, item.item_json
    FROM accounting_tax_ranked AS item
    WHERE item.tax_rank = 1
),
accounting_placement AS (
    SELECT
        target.translation_id,
        COALESCE(
            stats.financial_position + 1,
            LEAST(1, COALESCE(stats.filtered_count, 0))
        ) AS insert_position,
        COALESCE(
            tax.item_json,
            CASE target.locale
                WHEN 'hr' THEN JSON_OBJECT(
                    'title', 'Porezno savjetovanje',
                    'text', 'Podrška u poreznom planiranju, usklađenosti, poreznim pregledima, transfernim cijenama, poreznim nadzorima i transakcijama.'
                )
                ELSE JSON_OBJECT(
                    'title', 'Tax Advisory',
                    'text', 'Support with tax planning, compliance, tax reviews, transfer pricing, tax audits and transactions.'
                )
            END
        ) AS tax_item_json
    FROM accounting_targets AS target
    LEFT JOIN accounting_filtered_stats AS stats
        ON stats.translation_id = target.translation_id
    LEFT JOIN accounting_existing_tax AS tax
        ON tax.translation_id = target.translation_id
),
accounting_ordered_items AS (
    SELECT
        item.translation_id,
        CASE
            WHEN item.filtered_position >= placement.insert_position
                THEN item.filtered_position + 1
            ELSE item.filtered_position
        END AS final_position,
        item.item_json
    FROM accounting_filtered AS item
    INNER JOIN accounting_placement AS placement
        ON placement.translation_id = item.translation_id

    UNION ALL

    SELECT
        placement.translation_id,
        placement.insert_position AS final_position,
        placement.tax_item_json AS item_json
    FROM accounting_placement AS placement
),
accounting_rebuilt AS (
    SELECT
        item.translation_id,
        JSON_ARRAYAGG(JSON_EXTRACT(item.item_json, '$')) OVER (
            PARTITION BY item.translation_id
            ORDER BY item.final_position
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS rebuilt_items,
        ROW_NUMBER() OVER (
            PARTITION BY item.translation_id
            ORDER BY item.final_position DESC
        ) AS result_rank
    FROM accounting_ordered_items AS item
)
UPDATE content_service_page_translations AS translation
INNER JOIN accounting_rebuilt AS rebuilt
    ON rebuilt.translation_id = translation.id
    AND rebuilt.result_rank = 1
SET translation.payload = JSON_SET(
        COALESCE(translation.payload, JSON_OBJECT()),
        '$.services.items',
        rebuilt.rebuilt_items
    ),
    translation.updated_at = CURRENT_TIMESTAMP;

-- Remove every tax card from the Advisory hub by localized title or URL while
-- preserving every non-tax object verbatim. The dedicated tax page and payload
-- remain available. HR overview and EN hero copy follow the same guarded rules
-- as the PHP migration.
WITH
advisory_targets AS (
    SELECT
        translation.id AS translation_id,
        translation.locale,
        COALESCE(translation.payload, JSON_OBJECT()) AS payload
    FROM content_service_page_translations AS translation
    INNER JOIN content_service_pages AS page
        ON page.id = translation.service_page_id
        AND (page.code = 'advisory' OR page.template_key = 'advisory')
    WHERE translation.locale IN ('hr', 'en')
),
advisory_items AS (
    SELECT
        target.translation_id,
        item_rows.item_ordinal - 1 AS original_position,
        JSON_EXTRACT(
            target.payload,
            CONCAT('$.service_cards[', item_rows.item_ordinal - 1, ']')
        ) AS item_json
    FROM advisory_targets AS target
    CROSS JOIN JSON_TABLE(
        COALESCE(JSON_EXTRACT(target.payload, '$.service_cards'), JSON_ARRAY()),
        '$[*]' COLUMNS (item_ordinal FOR ORDINALITY)
    ) AS item_rows
),
advisory_normalized AS (
    SELECT
        item.*,
        LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.title')), ''))) AS item_title,
        LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.url')), ''))) AS item_url
    FROM advisory_items AS item
),
advisory_filtered AS (
    SELECT item.*
    FROM advisory_normalized AS item
    WHERE NOT (
        item.item_title IN ('porezno savjetovanje', 'tax advisory')
        OR INSTR(item.item_url, 'porezno-savjetovanje') > 0
        OR INSTR(item.item_url, 'tax-advisory') > 0
    )
),
advisory_rebuilt AS (
    SELECT
        item.translation_id,
        JSON_ARRAYAGG(JSON_EXTRACT(item.item_json, '$')) OVER (
            PARTITION BY item.translation_id
            ORDER BY item.original_position
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS rebuilt_cards,
        ROW_NUMBER() OVER (
            PARTITION BY item.translation_id
            ORDER BY item.original_position DESC
        ) AS result_rank
    FROM advisory_filtered AS item
)
UPDATE content_service_page_translations AS translation
INNER JOIN advisory_targets AS target
    ON target.translation_id = translation.id
LEFT JOIN advisory_rebuilt AS rebuilt
    ON rebuilt.translation_id = translation.id
    AND rebuilt.result_rank = 1
SET translation.payload = JSON_SET(
        CASE
            WHEN target.locale = 'hr'
                AND JSON_TYPE(JSON_EXTRACT(target.payload, '$.overview.body')) = 'ARRAY'
                THEN JSON_SET(
                    target.payload,
                    '$.overview.body',
                    JSON_EXTRACT(
                        REPLACE(
                            REPLACE(
                                CAST(
                                    JSON_EXTRACT(target.payload, '$.overview.body')
                                    AS CHAR CHARACTER SET utf8mb4
                                ),
                                'Financijske, porezne i strateške',
                                'Financijske i strateške'
                            ),
                            'financijske, porezne i strateške',
                            'financijske i strateške'
                        ),
                        '$'
                    )
                )
            WHEN target.locale = 'en'
                AND INSTR(
                    LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(target.payload, '$.hero.intro')), '')),
                    'tax'
                ) > 0
                THEN JSON_SET(
                    target.payload,
                    '$.hero.intro',
                    'Advisory provides expert support in financial, strategic, and investment matters, helping companies, investors, and entrepreneurs make quality decisions, manage risk, and create long-term value.'
                )
            ELSE target.payload
        END,
        '$.service_cards',
        COALESCE(rebuilt.rebuilt_cards, JSON_ARRAY())
    ),
    translation.meta_description = CASE target.locale
        WHEN 'hr' THEN 'Financijsko i strateško savjetovanje, pribavljanje financiranja, due diligence, procjene vrijednosti i M&A savjetovanje.'
        ELSE 'Financial and strategic advisory, financing, due diligence, valuations, and M&A advisory.'
    END,
    translation.updated_at = CURRENT_TIMESTAMP;

-- Legacy homepage fallback setting used when the CMS hero block is unavailable.
UPDATE system_settings
SET value = JSON_QUOTE('Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.'),
    updated_at = CURRENT_TIMESTAMP
WHERE `key` = 'store_home_hero_subtitle';

COMMIT;

-- Post-run verification. Each stats/contact array should have three items;
-- Accounting should have six cards and Advisory four.
SELECT
    block.code,
    translation.locale,
    JSON_LENGTH(translation.payload, '$.stats') AS home_stats_count,
    JSON_LENGTH(translation.payload, '$.contact_stats') AS contact_stats_count,
    JSON_EXTRACT(translation.payload, '$.locations.items') AS office_contacts
FROM content_block_translations AS translation
INNER JOIN content_blocks AS block ON block.id = translation.content_block_id
WHERE block.code = 'home-alpha-stats'
ORDER BY translation.locale;

SELECT
    page.code,
    translation.locale,
    translation.title,
    JSON_LENGTH(translation.payload, '$.services.items') AS accounting_card_count,
    JSON_LENGTH(translation.payload, '$.service_cards') AS advisory_card_count
FROM content_service_page_translations AS translation
INNER JOIN content_service_pages AS page ON page.id = translation.service_page_id
WHERE page.code IN ('racunovodstvo', 'advisory')
ORDER BY page.code, translation.locale;
