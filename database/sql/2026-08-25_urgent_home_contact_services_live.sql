-- Urgent client updates for the homepage, contact page, and service pages.
-- This script is idempotent and targets the current HR/EN CMS structure.
-- Deploy the application code first, run this script only if migrations are not
-- being executed on live, then run: php artisan optimize:clear

SET NAMES utf8mb4;

DROP TEMPORARY TABLE IF EXISTS
    urgent_20260825_advisory_rebuilt,
    urgent_20260825_advisory_stats,
    urgent_20260825_advisory_items,
    urgent_20260825_advisory_targets,
    urgent_20260825_accounting_rebuilt,
    urgent_20260825_accounting_ordered,
    urgent_20260825_accounting_stats,
    urgent_20260825_accounting_items,
    urgent_20260825_accounting_targets,
    urgent_20260825_numbers;

CREATE TEMPORARY TABLE urgent_20260825_numbers (
    item_index TINYINT UNSIGNED NOT NULL PRIMARY KEY
) ENGINE = MEMORY;

INSERT INTO urgent_20260825_numbers (item_index) VALUES
    (0), (1), (2), (3), (4), (5), (6), (7),
    (8), (9), (10), (11), (12), (13), (14), (15),
    (16), (17), (18), (19), (20), (21), (22), (23),
    (24), (25), (26), (27), (28), (29), (30), (31),
    (32), (33), (34), (35), (36), (37), (38), (39),
    (40), (41), (42), (43), (44), (45), (46), (47),
    (48), (49), (50), (51), (52), (53), (54), (55),
    (56), (57), (58), (59), (60), (61), (62), (63);

-- Keep array reconstruction safely above phpMyAdmin/MySQL's small default.
-- This is session-scoped and disappears with the import connection.
SET SESSION group_concat_max_len = 16777216;

-- Build all temporary work tables before the transaction. This remains
-- compatible with GTID-enforced servers using statement-based binlogging.
CREATE TEMPORARY TABLE urgent_20260825_accounting_targets (
    translation_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    locale VARCHAR(10) NOT NULL,
    source_length SMALLINT UNSIGNED NOT NULL,
    extracted_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    removed_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    tax_item JSON NULL
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_accounting_items (
    translation_id BIGINT UNSIGNED NOT NULL,
    original_position TINYINT UNSIGNED NOT NULL,
    item_json JSON NOT NULL,
    item_title VARCHAR(512) NOT NULL DEFAULT '',
    item_url TEXT NULL,
    is_tax TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    is_consolidation TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    is_financial TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (translation_id, original_position),
    INDEX urgent_accounting_tax_lookup (
        translation_id,
        is_tax,
        original_position
    )
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_accounting_stats (
    translation_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    extracted_count SMALLINT UNSIGNED NOT NULL,
    removed_count SMALLINT UNSIGNED NOT NULL,
    first_tax_position TINYINT UNSIGNED NULL,
    first_financial_position TINYINT UNSIGNED NULL,
    first_kept_position TINYINT UNSIGNED NULL
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_accounting_ordered (
    translation_id BIGINT UNSIGNED NOT NULL,
    sort_position SMALLINT UNSIGNED NOT NULL,
    item_json JSON NOT NULL,
    PRIMARY KEY (translation_id, sort_position)
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_accounting_rebuilt (
    translation_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    ordered_count SMALLINT UNSIGNED NOT NULL,
    rebuilt_json LONGTEXT NOT NULL
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_advisory_targets (
    translation_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    source_length SMALLINT UNSIGNED NOT NULL,
    extracted_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    removed_count SMALLINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_advisory_items (
    translation_id BIGINT UNSIGNED NOT NULL,
    original_position TINYINT UNSIGNED NOT NULL,
    item_json JSON NOT NULL,
    item_title VARCHAR(512) NOT NULL DEFAULT '',
    item_url TEXT NULL,
    is_tax TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (translation_id, original_position)
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_advisory_stats (
    translation_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    extracted_count SMALLINT UNSIGNED NOT NULL,
    removed_count SMALLINT UNSIGNED NOT NULL
) ENGINE = InnoDB;

CREATE TEMPORARY TABLE urgent_20260825_advisory_rebuilt (
    translation_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    ordered_count SMALLINT UNSIGNED NOT NULL,
    rebuilt_json LONGTEXT NOT NULL
) ENGINE = InnoDB;

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

-- Preserve every card object as JSON, retain the first existing tax card,
-- remove all tax duplicates and consolidation, and reinsert tax immediately
-- after Financial Accounting. Only arrays that can be extracted and rebuilt
-- completely within the explicit 0..63 safety limit are updated.

INSERT INTO urgent_20260825_accounting_targets (
    translation_id,
    locale,
    source_length
)
SELECT
    translation.id,
    translation.locale,
    JSON_LENGTH(translation.payload, '$.services.items')
FROM content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'racunovodstvo' OR page.template_key = 'accounting')
WHERE translation.locale IN ('hr', 'en')
  AND JSON_TYPE(JSON_EXTRACT(translation.payload, '$.services.items')) = 'ARRAY'
  AND JSON_LENGTH(translation.payload, '$.services.items') BETWEEN 0 AND 64;

INSERT INTO urgent_20260825_accounting_items (
    translation_id,
    original_position,
    item_json
)
SELECT
    target.translation_id,
    numbers.item_index,
    JSON_EXTRACT(
        translation.payload,
        CONCAT('$.services.items[', numbers.item_index, ']')
    )
FROM urgent_20260825_accounting_targets AS target
INNER JOIN content_service_page_translations AS translation
    ON translation.id = target.translation_id
INNER JOIN urgent_20260825_numbers AS numbers
    ON numbers.item_index < target.source_length
WHERE JSON_TYPE(
    JSON_EXTRACT(
        translation.payload,
        CONCAT('$.services.items[', numbers.item_index, ']')
    )
) = 'OBJECT';

UPDATE urgent_20260825_accounting_items AS item
SET item.item_title = LOWER(
        TRIM(
            COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.title')),
                ''
            )
        )
    ),
    item.item_url = LOWER(
        TRIM(
            COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.url')),
                ''
            )
        )
    );

UPDATE urgent_20260825_accounting_items AS item
SET item.is_tax = CASE
        WHEN item.item_title IN ('porezno savjetovanje', 'tax advisory')
            OR INSTR(item.item_url, 'porezno-savjetovanje') > 0
            OR INSTR(item.item_url, 'tax-advisory') > 0
            THEN 1
        ELSE 0
    END,
    item.is_consolidation = CASE
        WHEN item.item_title IN ('konsolidacija', 'consolidation')
            THEN 1
        ELSE 0
    END,
    item.is_financial = CASE
        WHEN item.item_title IN (
            'financijsko računovodstvo',
            'financial accounting'
        )
            THEN 1
        ELSE 0
    END;

INSERT INTO urgent_20260825_accounting_stats (
    translation_id,
    extracted_count,
    removed_count,
    first_tax_position,
    first_financial_position,
    first_kept_position
)
SELECT
    target.translation_id,
    COUNT(item.original_position),
    COALESCE(
        SUM(
            CASE
                WHEN item.is_tax = 1 OR item.is_consolidation = 1 THEN 1
                ELSE 0
            END
        ),
        0
    ),
    MIN(CASE WHEN item.is_tax = 1 THEN item.original_position END),
    MIN(
        CASE
            WHEN item.is_financial = 1
              AND item.is_tax = 0
              AND item.is_consolidation = 0
                THEN item.original_position
        END
    ),
    MIN(
        CASE
            WHEN item.is_tax = 0 AND item.is_consolidation = 0
                THEN item.original_position
        END
    )
FROM urgent_20260825_accounting_targets AS target
LEFT JOIN urgent_20260825_accounting_items AS item
    ON item.translation_id = target.translation_id
GROUP BY target.translation_id;

UPDATE urgent_20260825_accounting_targets AS target
INNER JOIN urgent_20260825_accounting_stats AS stats
    ON stats.translation_id = target.translation_id
LEFT JOIN urgent_20260825_accounting_items AS tax_item
    ON tax_item.translation_id = target.translation_id
    AND tax_item.original_position = stats.first_tax_position
SET target.extracted_count = stats.extracted_count,
    target.removed_count = stats.removed_count,
    target.tax_item = COALESCE(
        JSON_EXTRACT(tax_item.item_json, '$'),
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
    );

INSERT INTO urgent_20260825_accounting_ordered (
    translation_id,
    sort_position,
    item_json
)
SELECT
    item.translation_id,
    item.original_position * 2,
    JSON_EXTRACT(item.item_json, '$')
FROM urgent_20260825_accounting_items AS item
WHERE item.is_tax = 0
  AND item.is_consolidation = 0;

INSERT INTO urgent_20260825_accounting_ordered (
    translation_id,
    sort_position,
    item_json
)
SELECT
    target.translation_id,
    COALESCE(
        stats.first_financial_position * 2 + 1,
        stats.first_kept_position * 2 + 1,
        0
    ),
    JSON_EXTRACT(target.tax_item, '$')
FROM urgent_20260825_accounting_targets AS target
INNER JOIN urgent_20260825_accounting_stats AS stats
    ON stats.translation_id = target.translation_id
WHERE target.tax_item IS NOT NULL;

INSERT INTO urgent_20260825_accounting_rebuilt (
    translation_id,
    ordered_count,
    rebuilt_json
)
SELECT
    ordered.translation_id,
    COUNT(*),
    CONCAT(
        '[',
        GROUP_CONCAT(
            CAST(ordered.item_json AS CHAR CHARACTER SET utf8mb4)
            ORDER BY ordered.sort_position
            SEPARATOR ','
        ),
        ']'
    )
FROM urgent_20260825_accounting_ordered AS ordered
GROUP BY ordered.translation_id;

UPDATE content_service_page_translations AS translation
INNER JOIN urgent_20260825_accounting_targets AS target
    ON target.translation_id = translation.id
INNER JOIN urgent_20260825_accounting_rebuilt AS rebuilt
    ON rebuilt.translation_id = translation.id
SET translation.payload = JSON_SET(
        translation.payload,
        '$.services.items',
        JSON_EXTRACT(rebuilt.rebuilt_json, '$')
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE target.source_length BETWEEN 0 AND 64
  AND target.extracted_count = target.source_length
  AND target.removed_count <= target.source_length
  AND JSON_TYPE(target.tax_item) = 'OBJECT'
  AND rebuilt.ordered_count =
      target.source_length - target.removed_count + 1
  AND JSON_VALID(rebuilt.rebuilt_json) = 1
  AND JSON_TYPE(JSON_EXTRACT(rebuilt.rebuilt_json, '$')) = 'ARRAY'
  AND JSON_LENGTH(rebuilt.rebuilt_json) = rebuilt.ordered_count
  AND JSON_TYPE(JSON_EXTRACT(translation.payload, '$.services.items')) = 'ARRAY'
  AND JSON_LENGTH(translation.payload, '$.services.items') =
      target.source_length;

-- Remove every tax card from the Advisory hub while retaining all non-tax
-- card objects and their original order. The same extraction, JSON validity,
-- count, and maximum-length guards prevent a partial array rewrite.

-- Besides applying the requested SEO copy, this UPDATE locks the Advisory
-- translation rows until COMMIT so an editor cannot be overwritten mid-rebuild.
UPDATE content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'advisory' OR page.template_key = 'advisory')
SET translation.meta_description = CASE translation.locale
        WHEN 'hr' THEN 'Financijsko i strateško savjetovanje, pribavljanje financiranja, due diligence, procjene vrijednosti i M&A savjetovanje.'
        ELSE 'Financial and strategic advisory, financing, due diligence, valuations, and M&A advisory.'
    END,
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale IN ('hr', 'en');

INSERT INTO urgent_20260825_advisory_targets (
    translation_id,
    source_length
)
SELECT
    translation.id,
    JSON_LENGTH(translation.payload, '$.service_cards')
FROM content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'advisory' OR page.template_key = 'advisory')
WHERE translation.locale IN ('hr', 'en')
  AND JSON_TYPE(JSON_EXTRACT(translation.payload, '$.service_cards')) = 'ARRAY'
  AND JSON_LENGTH(translation.payload, '$.service_cards') BETWEEN 0 AND 64;

INSERT INTO urgent_20260825_advisory_items (
    translation_id,
    original_position,
    item_json
)
SELECT
    target.translation_id,
    numbers.item_index,
    JSON_EXTRACT(
        translation.payload,
        CONCAT('$.service_cards[', numbers.item_index, ']')
    )
FROM urgent_20260825_advisory_targets AS target
INNER JOIN content_service_page_translations AS translation
    ON translation.id = target.translation_id
INNER JOIN urgent_20260825_numbers AS numbers
    ON numbers.item_index < target.source_length
WHERE JSON_TYPE(
    JSON_EXTRACT(
        translation.payload,
        CONCAT('$.service_cards[', numbers.item_index, ']')
    )
) = 'OBJECT';

UPDATE urgent_20260825_advisory_items AS item
SET item.item_title = LOWER(
        TRIM(
            COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.title')),
                ''
            )
        )
    ),
    item.item_url = LOWER(
        TRIM(
            COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(item.item_json, '$.url')),
                ''
            )
        )
    );

UPDATE urgent_20260825_advisory_items AS item
SET item.is_tax = CASE
        WHEN item.item_title IN ('porezno savjetovanje', 'tax advisory')
            OR INSTR(item.item_url, 'porezno-savjetovanje') > 0
            OR INSTR(item.item_url, 'tax-advisory') > 0
            THEN 1
        ELSE 0
    END;

INSERT INTO urgent_20260825_advisory_stats (
    translation_id,
    extracted_count,
    removed_count
)
SELECT
    target.translation_id,
    COUNT(item.original_position),
    COALESCE(
        SUM(CASE WHEN item.is_tax = 1 THEN 1 ELSE 0 END),
        0
    )
FROM urgent_20260825_advisory_targets AS target
LEFT JOIN urgent_20260825_advisory_items AS item
    ON item.translation_id = target.translation_id
GROUP BY target.translation_id;

UPDATE urgent_20260825_advisory_targets AS target
INNER JOIN urgent_20260825_advisory_stats AS stats
    ON stats.translation_id = target.translation_id
SET target.extracted_count = stats.extracted_count,
    target.removed_count = stats.removed_count;

INSERT INTO urgent_20260825_advisory_rebuilt (
    translation_id,
    ordered_count,
    rebuilt_json
)
SELECT
    target.translation_id,
    COUNT(item.original_position),
    CONCAT(
        '[',
        COALESCE(
            GROUP_CONCAT(
                CAST(item.item_json AS CHAR CHARACTER SET utf8mb4)
                ORDER BY item.original_position
                SEPARATOR ','
            ),
            ''
        ),
        ']'
    )
FROM urgent_20260825_advisory_targets AS target
LEFT JOIN urgent_20260825_advisory_items AS item
    ON item.translation_id = target.translation_id
    AND item.is_tax = 0
GROUP BY target.translation_id;

UPDATE content_service_page_translations AS translation
INNER JOIN urgent_20260825_advisory_targets AS target
    ON target.translation_id = translation.id
INNER JOIN urgent_20260825_advisory_rebuilt AS rebuilt
    ON rebuilt.translation_id = translation.id
SET translation.payload = JSON_SET(
        translation.payload,
        '$.service_cards',
        JSON_EXTRACT(rebuilt.rebuilt_json, '$')
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE target.source_length BETWEEN 0 AND 64
  AND target.extracted_count = target.source_length
  AND target.removed_count <= target.source_length
  AND rebuilt.ordered_count =
      target.source_length - target.removed_count
  AND JSON_VALID(rebuilt.rebuilt_json) = 1
  AND JSON_TYPE(JSON_EXTRACT(rebuilt.rebuilt_json, '$')) = 'ARRAY'
  AND JSON_LENGTH(rebuilt.rebuilt_json) = rebuilt.ordered_count
  AND JSON_TYPE(JSON_EXTRACT(translation.payload, '$.service_cards')) = 'ARRAY'
  AND JSON_LENGTH(translation.payload, '$.service_cards') =
      target.source_length;

-- Guarded copy adjustments matching the PHP migration.
UPDATE content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'advisory' OR page.template_key = 'advisory')
SET translation.payload = JSON_SET(
        translation.payload,
        '$.overview.body',
        JSON_EXTRACT(
            REPLACE(
                REPLACE(
                    CAST(JSON_EXTRACT(translation.payload, '$.overview.body') AS CHAR CHARACTER SET utf8mb4),
                    'Financijske, porezne i strateške',
                    'Financijske i strateške'
                ),
                'financijske, porezne i strateške',
                'financijske i strateške'
            ),
            '$'
        )
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale = 'hr'
  AND JSON_TYPE(JSON_EXTRACT(translation.payload, '$.overview.body')) = 'ARRAY'
  AND (
      INSTR(CAST(JSON_EXTRACT(translation.payload, '$.overview.body') AS CHAR CHARACTER SET utf8mb4), 'Financijske, porezne i strateške') > 0
      OR INSTR(CAST(JSON_EXTRACT(translation.payload, '$.overview.body') AS CHAR CHARACTER SET utf8mb4), 'financijske, porezne i strateške') > 0
  );

UPDATE content_service_page_translations AS translation
INNER JOIN content_service_pages AS page
    ON page.id = translation.service_page_id
    AND (page.code = 'advisory' OR page.template_key = 'advisory')
SET translation.payload = JSON_SET(
        translation.payload,
        '$.hero.intro',
        'Advisory provides expert support in financial, strategic, and investment matters, helping companies, investors, and entrepreneurs make quality decisions, manage risk, and create long-term value.'
    ),
    translation.updated_at = CURRENT_TIMESTAMP
WHERE translation.locale = 'en'
  AND INSTR(
      LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(translation.payload, '$.hero.intro')), '')),
      'tax'
  ) > 0;

-- Legacy homepage fallback setting used when the CMS hero block is unavailable.
UPDATE system_settings
SET value = JSON_QUOTE('Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.'),
    updated_at = CURRENT_TIMESTAMP
WHERE `key` = 'store_home_hero_subtitle';

COMMIT;

DROP TEMPORARY TABLE IF EXISTS
    urgent_20260825_advisory_rebuilt,
    urgent_20260825_advisory_stats,
    urgent_20260825_advisory_items,
    urgent_20260825_advisory_targets,
    urgent_20260825_accounting_rebuilt,
    urgent_20260825_accounting_ordered,
    urgent_20260825_accounting_stats,
    urgent_20260825_accounting_items,
    urgent_20260825_accounting_targets,
    urgent_20260825_numbers;

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
