-- ALPHA CAPITALIS newsletter + Mailchimp live deployment
-- Generated for commit deployment on 2026-08-25.
-- Target: MySQL 8 / utf8mb4. Take a database backup before importing.
-- This file is idempotent and may be imported more than once.
-- Existing newsletter settings are preserved; this file contains no credentials.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Schema equivalent of:
-- 2026_08_25_130000_create_newsletter_subscriptions_table.php
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(191) NOT NULL,
    `locale` VARCHAR(12) NOT NULL DEFAULT 'hr',
    `provider` VARCHAR(32) NOT NULL DEFAULT 'mailchimp',
    `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
    `provider_member_id` VARCHAR(191) NULL,
    `subscriber_hash` CHAR(32) NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 1,
    `subscribed_at` TIMESTAMP NULL,
    `last_attempt_at` TIMESTAMP NULL,
    `last_synced_at` TIMESTAMP NULL,
    `error_code` VARCHAR(120) NULL,
    `error_message` TEXT NULL,
    `ip_hash` CHAR(64) NULL,
    `payload` JSON NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `newsletter_subscriptions_email_unique` (`email`),
    KEY `newsletter_subscriptions_locale_index` (`locale`),
    KEY `newsletter_subscriptions_provider_index` (`provider`),
    KEY `newsletter_subscriptions_status_index` (`status`),
    KEY `newsletter_subscriptions_subscriber_hash_index` (`subscriber_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

-- Seed only missing settings. Values are JSON-encoded because SystemSettingsService
-- decodes this column. The API key intentionally remains empty and must be entered
-- through the protected CMS settings screen after deployment.
INSERT INTO `system_settings` (`key`, `value`, `created_at`, `updated_at`)
SELECT 'store_newsletter_provider', '"none"', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `system_settings`
    WHERE `key` = 'store_newsletter_provider'
);

INSERT INTO `system_settings` (`key`, `value`, `created_at`, `updated_at`)
SELECT 'store_newsletter_mailchimp_server_prefix', '""', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `system_settings`
    WHERE `key` = 'store_newsletter_mailchimp_server_prefix'
);

INSERT INTO `system_settings` (`key`, `value`, `created_at`, `updated_at`)
SELECT 'store_newsletter_mailchimp_api_key', '""', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `system_settings`
    WHERE `key` = 'store_newsletter_mailchimp_api_key'
);

INSERT INTO `system_settings` (`key`, `value`, `created_at`, `updated_at`)
SELECT 'store_newsletter_mailchimp_list_id', '""', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `system_settings`
    WHERE `key` = 'store_newsletter_mailchimp_list_id'
);

-- The visible consent copy belongs to the localized CMS footer content. Seed it
-- only when missing; editors can change both values later in Content > Navigation.
INSERT INTO `system_settings` (`key`, `value`, `created_at`, `updated_at`)
SELECT 'front_navigation_chrome', JSON_OBJECT(
    'hr', JSON_OBJECT(
        'footer_newsletter_consent', 'Želim primati newsletter i prihvaćam obradu podataka u tu svrhu.'
    ),
    'en', JSON_OBJECT(
        'footer_newsletter_consent', 'I want to receive the newsletter and consent to data processing for this purpose.'
    )
), NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM `system_settings`
    WHERE `key` = 'front_navigation_chrome'
);

UPDATE `system_settings`
SET `value` = JSON_SET(
        `value`,
        '$.hr.footer_newsletter_consent',
        'Želim primati newsletter i prihvaćam obradu podataka u tu svrhu.'
    ),
    `updated_at` = NOW()
WHERE `key` = 'front_navigation_chrome'
  AND JSON_EXTRACT(`value`, '$.hr.footer_newsletter_consent') IS NULL;

UPDATE `system_settings`
SET `value` = JSON_SET(
        `value`,
        '$.en.footer_newsletter_consent',
        'I want to receive the newsletter and consent to data processing for this purpose.'
    ),
    `updated_at` = NOW()
WHERE `key` = 'front_navigation_chrome'
  AND JSON_EXTRACT(`value`, '$.en.footer_newsletter_consent') IS NULL;

-- Record the equivalent Laravel migration without relying on a uniqueness
-- constraint: the migrations table does not have one on the migration column.
SET @ac_newsletter_migration_name := '2026_08_25_130000_create_newsletter_subscriptions_table';
SET @ac_newsletter_migration_batch := (
    SELECT COALESCE(MAX(`batch`), 0) + 1
    FROM `migrations`
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT @ac_newsletter_migration_name, @ac_newsletter_migration_batch
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE BINARY `migration` = BINARY @ac_newsletter_migration_name
);

COMMIT;

-- Verification report. Expected values: table=1, settings=4, consent=2, migration=1.
-- Query the application table directly so phpMyAdmin cannot switch the UNION
-- context to information_schema before resolving the remaining table names.
SELECT 'newsletter_table' AS `check_name`,
       IF(COUNT(*) >= 0, 1, 0) AS `actual`,
       1 AS `expected`
FROM `newsletter_subscriptions`
UNION ALL
SELECT 'newsletter_settings', COUNT(*), 4
FROM `system_settings`
WHERE `key` IN (
    'store_newsletter_provider',
    'store_newsletter_mailchimp_server_prefix',
    'store_newsletter_mailchimp_api_key',
    'store_newsletter_mailchimp_list_id'
)
UNION ALL
SELECT 'newsletter_consent_copy',
       (JSON_EXTRACT(`value`, '$.hr.footer_newsletter_consent') IS NOT NULL)
       + (JSON_EXTRACT(`value`, '$.en.footer_newsletter_consent') IS NOT NULL),
       2
FROM `system_settings`
WHERE `key` = 'front_navigation_chrome'
UNION ALL
SELECT 'newsletter_migration', COUNT(*), 1
FROM `migrations`
WHERE BINARY `migration` = BINARY '2026_08_25_130000_create_newsletter_subscriptions_table';
