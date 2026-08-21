-- ALPHA CAPITALIS team page intro and SEO settings.
-- Import once through phpMyAdmin after deploying commit cb07285 or newer.
-- This script does not alter the schema or touch content_team_members.

SET NAMES utf8mb4;

START TRANSACTION;

SET @ac_team_migration_name := '2026_08_21_080000_ensure_team_info_page';
SET @ac_team_migration_applied := (
    SELECT EXISTS(
        SELECT 1
        FROM migrations
        WHERE migration = @ac_team_migration_name
    )
);

SET @ac_team_page_id := (
    SELECT id
    FROM content_info_pages
    WHERE code = 'team-page'
    LIMIT 1
);

SET @ac_team_page_id := COALESCE(
    @ac_team_page_id,
    (
        SELECT page_id
        FROM content_info_page_translations
        WHERE locale = 'hr'
          AND slug = 'alpha-capitalis-tim'
        LIMIT 1
    )
);

INSERT INTO content_info_pages (
    code,
    layout,
    is_active,
    show_in_footer,
    published_at,
    sort_order,
    payload,
    created_by,
    updated_by,
    created_at,
    updated_at
)
SELECT
    'team-page',
    'team',
    1,
    0,
    NOW(),
    30,
    NULL,
    NULL,
    NULL,
    NOW(),
    NOW()
WHERE @ac_team_page_id IS NULL
  AND @ac_team_migration_applied = 0;

SET @ac_team_page_id := COALESCE(@ac_team_page_id, LAST_INSERT_ID());

UPDATE content_info_pages
SET code = 'team-page',
    layout = 'team',
    is_active = 1,
    show_in_footer = 0,
    published_at = COALESCE(published_at, NOW()),
    updated_at = NOW()
WHERE id = @ac_team_page_id
  AND @ac_team_migration_applied = 0;

INSERT INTO content_info_page_translations (
    page_id,
    locale,
    title,
    slug,
    excerpt,
    body_html,
    meta_title,
    meta_description,
    payload,
    created_at,
    updated_at
)
SELECT
    @ac_team_page_id,
    'hr',
    'ALPHA CAPITALIS Tim',
    'alpha-capitalis-tim',
    'Upoznajte stručnjake koji povezuju znanja iz različitih područja kako bi klijentima pružili podršku tamo gdje im je najpotrebnija.',
    NULL,
    'ALPHA CAPITALIS Tim',
    'Upoznajte stručnjake koji povezuju znanja iz različitih područja kako bi klijentima pružili podršku tamo gdje im je najpotrebnija.',
    NULL,
    NOW(),
    NOW()
WHERE @ac_team_migration_applied = 0
  AND NOT EXISTS (
      SELECT 1
      FROM content_info_page_translations
      WHERE page_id = @ac_team_page_id
        AND locale = 'hr'
  );

UPDATE content_info_page_translations
SET title = 'ALPHA CAPITALIS Tim',
    slug = 'alpha-capitalis-tim',
    excerpt = 'Upoznajte stručnjake koji povezuju znanja iz različitih područja kako bi klijentima pružili podršku tamo gdje im je najpotrebnija.',
    meta_title = 'ALPHA CAPITALIS Tim',
    meta_description = 'Upoznajte stručnjake koji povezuju znanja iz različitih područja kako bi klijentima pružili podršku tamo gdje im je najpotrebnija.',
    updated_at = NOW()
WHERE page_id = @ac_team_page_id
  AND locale = 'hr'
  AND @ac_team_migration_applied = 0;

INSERT INTO content_info_page_translations (
    page_id,
    locale,
    title,
    slug,
    excerpt,
    body_html,
    meta_title,
    meta_description,
    payload,
    created_at,
    updated_at
)
SELECT
    @ac_team_page_id,
    'en',
    'ALPHA CAPITALIS Team',
    'alpha-capitalis-team',
    'Meet the experts who connect knowledge across different fields to support clients where they need it most.',
    NULL,
    'ALPHA CAPITALIS Team',
    'Meet the experts who connect knowledge across different fields to support clients where they need it most.',
    NULL,
    NOW(),
    NOW()
WHERE @ac_team_migration_applied = 0
  AND NOT EXISTS (
      SELECT 1
      FROM content_info_page_translations
      WHERE page_id = @ac_team_page_id
        AND locale = 'en'
  );

UPDATE content_info_page_translations
SET title = 'ALPHA CAPITALIS Team',
    slug = 'alpha-capitalis-team',
    excerpt = 'Meet the experts who connect knowledge across different fields to support clients where they need it most.',
    meta_title = 'ALPHA CAPITALIS Team',
    meta_description = 'Meet the experts who connect knowledge across different fields to support clients where they need it most.',
    updated_at = NOW()
WHERE page_id = @ac_team_page_id
  AND locale = 'en'
  AND @ac_team_migration_applied = 0;

SET @ac_team_migration_batch := (
    SELECT COALESCE(MAX(batch), 0) + 1
    FROM migrations
);

INSERT INTO migrations (migration, batch)
SELECT @ac_team_migration_name, @ac_team_migration_batch
WHERE @ac_team_migration_applied = 0;

COMMIT;

SELECT
    page.code,
    translation.locale,
    translation.title,
    translation.excerpt,
    translation.meta_title,
    translation.meta_description
FROM content_info_pages AS page
INNER JOIN content_info_page_translations AS translation
    ON translation.page_id = page.id
WHERE page.code = 'team-page'
ORDER BY translation.locale;
