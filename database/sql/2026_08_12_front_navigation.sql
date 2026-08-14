-- Glavna frontend navigacija iz CMS postavke, sa stavkom "Početna".
-- Sigurno za ponovno pokretanje; zamjenjuje trenutnu glavnu navigaciju ovim redoslijedom.
SET NAMES utf8mb4;

START TRANSACTION;

INSERT INTO system_settings (`key`, `value`, created_at, updated_at)
VALUES (
    'front_navigation_main',
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
        ),
        JSON_OBJECT(
            'type', 'custom',
            'label', 'Usluge',
            'label_translations', JSON_OBJECT('hr', 'Usluge', 'en', 'Services'),
            'page_id', 0,
            'url', '/usluge',
            'url_translations', JSON_OBJECT('hr', '/usluge', 'en', '/usluge'),
            'open_in_new_tab', FALSE,
            'show_dropdown', FALSE,
            'is_active', TRUE,
            'sort_order', 1
        ),
        JSON_OBJECT(
            'type', 'custom',
            'label', 'O nama',
            'label_translations', JSON_OBJECT('hr', 'O nama', 'en', 'About us'),
            'page_id', 0,
            'url', '/o-nama',
            'url_translations', JSON_OBJECT('hr', '/o-nama', 'en', '/o-nama'),
            'open_in_new_tab', FALSE,
            'show_dropdown', FALSE,
            'is_active', TRUE,
            'sort_order', 2
        ),
        JSON_OBJECT(
            'type', 'custom',
            'label', 'Karijera',
            'label_translations', JSON_OBJECT('hr', 'Karijera', 'en', 'Careers'),
            'page_id', 0,
            'url', '/karijera',
            'url_translations', JSON_OBJECT('hr', '/karijera', 'en', '/karijera'),
            'open_in_new_tab', FALSE,
            'show_dropdown', FALSE,
            'is_active', TRUE,
            'sort_order', 3
        ),
        JSON_OBJECT(
            'type', 'blog',
            'label', 'Objave',
            'label_translations', JSON_OBJECT('hr', 'Objave', 'en', 'Insights'),
            'page_id', 0,
            'url', '',
            'url_translations', JSON_OBJECT(),
            'open_in_new_tab', FALSE,
            'show_dropdown', FALSE,
            'is_active', TRUE,
            'sort_order', 4
        ),
        JSON_OBJECT(
            'type', 'contact',
            'label', 'Kontakt',
            'label_translations', JSON_OBJECT('hr', 'Kontakt', 'en', 'Contact'),
            'page_id', 0,
            'url', '',
            'url_translations', JSON_OBJECT(),
            'open_in_new_tab', FALSE,
            'show_dropdown', FALSE,
            'is_active', TRUE,
            'sort_order', 5
        )
    ),
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`),
    updated_at = NOW();

COMMIT;
