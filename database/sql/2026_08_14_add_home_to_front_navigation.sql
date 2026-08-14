-- Live dopuna postojeće CMS navigacije stavkom "Početna".
-- Sigurno za ponovno pokretanje: postojeći meni ostaje netaknut ako "Početna" već postoji.
-- Alternativa za: php artisan migrate --force
SET NAMES utf8mb4;

START TRANSACTION;

WITH navigation_items AS (
    SELECT
        setting.id,
        menu_item.position,
        menu_item.payload
    FROM system_settings AS setting
    INNER JOIN JSON_TABLE(
        IF(JSON_VALID(setting.value), setting.value, JSON_ARRAY()),
        '$[*]' COLUMNS (
            position FOR ORDINALITY,
            payload JSON PATH '$'
        )
    ) AS menu_item
    WHERE setting.`key` = 'front_navigation_main'
      AND JSON_VALID(setting.value)
      AND JSON_TYPE(setting.value) = 'ARRAY'
      AND JSON_SEARCH(setting.value, 'one', '/', NULL, '$[*].url') IS NULL
),
rebuilt_navigation AS (
    SELECT
        id,
        JSON_ARRAYAGG(
            JSON_SET(payload, '$.sort_order', position)
        ) OVER (
            PARTITION BY id
            ORDER BY position
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS shifted_items,
        ROW_NUMBER() OVER (
            PARTITION BY id
            ORDER BY position DESC
        ) AS final_row
    FROM navigation_items
)
UPDATE system_settings AS setting
INNER JOIN rebuilt_navigation AS navigation
    ON navigation.id = setting.id
   AND navigation.final_row = 1
SET
    setting.value = JSON_MERGE_PRESERVE(
        JSON_ARRAY(
            JSON_OBJECT(
                'type', 'custom',
                'label', 'Početna',
                'label_translations', JSON_OBJECT('hr', 'Početna', 'en', 'Home'),
                'page_id', 0,
                'url', '/',
                'url_translations', JSON_OBJECT('hr', '/', 'en', '/'),
                'open_in_new_tab', FALSE,
                'show_dropdown', FALSE,
                'is_active', TRUE,
                'sort_order', 0
            )
        ),
        navigation.shifted_items
    ),
    setting.updated_at = NOW();

SELECT ROW_COUNT() AS navigation_rows_updated;

SELECT JSON_PRETTY(`value`) AS front_navigation_main
FROM system_settings
WHERE `key` = 'front_navigation_main';

COMMIT;
