-- ALPHA CAPITALIS open positions — live database deployment.
-- Target: MySQL 8 / MariaDB with utf8mb4. Safe to import repeatedly through phpMyAdmin.
--
-- IMPORTANT:
-- 1. Take a database backup before importing.
-- 2. Deploy the matching application code before importing this file.
-- 3. This creates only the open-position tables and the initial Croatian job ad.
-- 4. Existing job-opening records and editor changes are never overwritten.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `content_job_openings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `published_at` DATETIME NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `content_job_openings_code_unique` (`code`),
    KEY `content_job_openings_created_by_foreign` (`created_by`),
    KEY `content_job_openings_updated_by_foreign` (`updated_by`),
    KEY `content_job_openings_is_active_index` (`is_active`),
    KEY `content_job_openings_published_at_index` (`published_at`),
    KEY `content_job_openings_sort_order_index` (`sort_order`),
    CONSTRAINT `content_job_openings_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `content_job_openings_updated_by_foreign`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `content_job_opening_translations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_opening_id` BIGINT UNSIGNED NOT NULL,
    `locale` VARCHAR(12) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `locations` VARCHAR(500) COLLATE utf8mb4_unicode_ci NOT NULL,
    `excerpt` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `body_html` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `meta_title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
    `meta_description` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `content_job_opening_locale_unique` (`job_opening_id`, `locale`),
    UNIQUE KEY `content_job_opening_locale_slug_unique` (`locale`, `slug`),
    KEY `content_job_opening_translations_locale_index` (`locale`),
    CONSTRAINT `content_job_opening_translations_job_opening_id_foreign`
        FOREIGN KEY (`job_opening_id`) REFERENCES `content_job_openings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

SET @ac_job_code := 'racunovoda-asistent-u-racunovodstvu';
SET @ac_job_locale := 'hr';
SET @ac_job_slug := 'racunovoda-asistent-u-racunovodstvu';
SET @ac_job_content_migration_applied := (
    SELECT EXISTS (
        SELECT 1
        FROM `migrations`
        WHERE BINARY `migration` = BINARY '2026_09_02_091000_add_initial_accounting_job_opening'
    )
);

INSERT INTO `content_job_openings` (
    `code`,
    `is_active`,
    `published_at`,
    `sort_order`,
    `created_by`,
    `updated_by`,
    `created_at`,
    `updated_at`
)
SELECT
    @ac_job_code,
    1,
    '2026-09-02 07:03:00',
    0,
    NULL,
    NULL,
    UTC_TIMESTAMP(),
    UTC_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1
    FROM `content_job_openings`
    WHERE BINARY `code` = BINARY @ac_job_code
)
AND @ac_job_content_migration_applied = 0
AND NOT EXISTS (
    SELECT 1
    FROM `content_job_opening_translations`
    WHERE `locale` = @ac_job_locale
      AND `slug` = @ac_job_slug
);

SET @ac_job_opening_id := (
    SELECT `id`
    FROM `content_job_openings`
    WHERE BINARY `code` = BINARY @ac_job_code
    LIMIT 1
);

INSERT INTO `content_job_opening_translations` (
    `job_opening_id`,
    `locale`,
    `title`,
    `slug`,
    `locations`,
    `excerpt`,
    `body_html`,
    `meta_title`,
    `meta_description`,
    `created_at`,
    `updated_at`
)
SELECT
    @ac_job_opening_id,
    @ac_job_locale,
    'Računovođa / Asistent u računovodstvu (m/ž)',
    @ac_job_slug,
    'Zagreb | Rijeka | Vinkovci',
    'Tražimo osobe s minimalno godinu dana iskustva u računovodstvu koje žele učiti, napredovati i graditi karijeru u modernom računovodstvu.',
    '<p><strong>Kako izgleda računovodstvo kada spojiš znanje, dobar tim i tehnologiju koja ti stvarno olakšava posao?</strong></p>
<p>U ALPHA CAPITALIS-u već godinama gradimo upravo takvo okruženje.</p>
<p>Okruženje u kojem računovođe imaju prostor učiti, razvijati se i preuzimati sve više odgovornosti – dok istovremeno kontinuirano radimo na digitalizaciji i automatizaciji svega onoga što im nepotrebno oduzima vrijeme.</p>
<p>Jer vjerujemo da najbolji računovođe svoje vrijeme trebaju koristiti za <strong>razmišljanje, kontrolu, analizu, komunikaciju s klijentima i razvoj svog znanja</strong>, a što manje za ručno prepisivanje podataka i ponavljajuće operativne zadatke.</p>
<p>Danas ALPHA CAPITALIS okuplja više od 75 stručnjaka iz područja računovodstva, revizije, poreznog savjetovanja i financijsko-savjetodavnih usluga, a naš računovodstveni tim broji više od 40 ljudi u Zagrebu, Rijeci i Vinkovcima.</p>
<p>I nastavljamo rasti.</p>
<p>Zato tražimo nove kolege u sva tri grada u kojima poslujemo za pozicije:</p>
<h2>RAČUNOVOĐA / ASISTENT U RAČUNOVODSTVU (m/ž)</h2>
<p>Tražimo osobe s <strong>minimalno godinu dana iskustva u računovodstvu</strong> koje žele napraviti sljedeći korak u svojoj karijeri.</p>
<p>Ne očekujemo da znaš sve, važno nam je da imaš dobre temelje, da si odgovoran/a i precizan/a, da želiš učiti i da računovodstvo vidiš kao područje u kojem se želiš razvijati.</p>
<h2>Što ćeš raditi?</h2>
<p>Ovisno o svom dosadašnjem iskustvu i znanju:</p>
<ul>
<li>knjiženje i kontrola poslovne dokumentacije</li>
<li>priprema i kontrola PDV-a i drugih poreznih evidencija</li>
<li>usklađenja i kontrola računovodstvenih podataka</li>
<li>sudjelovanje u mjesečnim i godišnjim zatvaranjima</li>
<li>komunikacija s klijentima</li>
<li>priprema izvještaja i analiza</li>
<li>postupno preuzimanje sve veće samostalnosti i odgovornosti za svoje klijente</li>
</ul>
<p>Razinu odgovornosti prilagodit ćemo tvom iskustvu – cilj nam je da uz podršku tima kontinuirano napreduješ.</p>
<h2>Kako izgleda rad kod nas?</h2>
<h3>Mentorstvo i kontinuirano učenje</h3>
<p>Imat ćeš podršku iskusnijih kolega, interne edukacije i tim u kojem se znanje dijeli. Bez obzira imaš li godinu, tri ili više godina iskustva, uvijek postoji prostor za sljedeću razinu znanja.</p>
<h3>Računovodstvo koje ide naprijed</h3>
<p>Snažno ulažemo u digitalizaciju, automatizaciju i razvoj naših procesa kako bismo što više manualnih i repetitivnih poslova prepustili tehnologiji.</p>
<p>Ne uvodimo tehnologiju radi tehnologije. Uvodimo je kako bismo našim ljudima oslobodili vrijeme za posao koji zahtijeva njihovo znanje i iskustvo.</p>
<p><strong>Želimo da naši računovođe budu računovođe – a ne operateri za unos podataka.</strong></p>
<h3>Prostor za razvoj i napredovanje</h3>
<p>Želimo da ljudi koji nam se pridruže dugoročno rastu s nama – od asistenta prema samostalnom računovođi, a zatim prema seniorskim i menadžerskim pozicijama.</p>
<h3>Tim koji radi zajedno</h3>
<p>Vjerujemo u otvorenu komunikaciju, međusobnu podršku i dijeljenje znanja. Ne želimo okruženje u kojem je svatko prepušten sam sebi.</p>
<h3>Normalno radno vrijeme</h3>
<p>Vjerujemo da se vrhunski posao može raditi bez kulture prekovremenih sati. Dobra organizacija, tehnologija i kvalitetni procesi trebaju omogućiti ljudima da imaju i kvalitetan privatni život.</p>
<h2>Što ti nudimo?</h2>
<ul>
<li>mentorstvo i podršku iskusnog tima</li>
<li>kontinuirane edukacije i razvoj stručnog znanja</li>
<li>moderne digitalne alate i sve više automatiziranih procesa</li>
<li>rad s različitim i kvalitetnim klijentima</li>
<li>postupno preuzimanje većih odgovornosti</li>
<li>mogućnost razvoja i napredovanja</li>
<li>normalno radno vrijeme bez kulture prekovremenih sati</li>
<li>okruženje koje kontinuirano raste, mijenja se i razvija</li>
</ul>
<p>Ne tražimo savršene životopise, tražimo ljude koji već imaju prve temelje i iskustvo, ali žele <strong>učiti, napredovati i graditi svoju karijeru u modernom računovodstvu.</strong></p>
<p>Ako želiš raditi u računovodstvu u kojem se cijene <strong>znanje i ljudi, a tehnologija služi tome da im posao bude bolji i kvalitetniji</strong>, voljeli bismo te upoznati.</p>
<p>Pošalji nam svoj životopis na <a href="mailto:hr@alphacapitalis.com"><strong>hr@alphacapitalis.com</strong></a> i postani dio ALPHA CAPITALIS tima.</p>',
    'Računovođa / Asistent u računovodstvu | ALPHA CAPITALIS',
    'Otvorena pozicija za računovođu ili asistenta u računovodstvu u Zagrebu, Rijeci i Vinkovcima. Pridruži se ALPHA CAPITALIS timu.',
    UTC_TIMESTAMP(),
    UTC_TIMESTAMP()
FROM DUAL
WHERE @ac_job_opening_id IS NOT NULL
  AND @ac_job_content_migration_applied = 0
  AND NOT EXISTS (
      SELECT 1
      FROM `content_job_opening_translations`
      WHERE `job_opening_id` = @ac_job_opening_id
        AND `locale` = @ac_job_locale
  )
  AND NOT EXISTS (
      SELECT 1
      FROM `content_job_opening_translations`
      WHERE `locale` = @ac_job_locale
        AND `slug` = @ac_job_slug
  );

-- Record the equivalent Laravel migrations so a later migrate run does not
-- try to recreate these tables or reinsert the initial job ad.
SET @ac_job_migration_batch := (
    SELECT COALESCE(MAX(`batch`), 0) + 1
    FROM `migrations`
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_02_090000_create_content_job_openings_tables', @ac_job_migration_batch
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE BINARY `migration` = BINARY '2026_09_02_090000_create_content_job_openings_tables'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_02_091000_add_initial_accounting_job_opening', @ac_job_migration_batch
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE BINARY `migration` = BINARY '2026_09_02_091000_add_initial_accounting_job_opening'
);

COMMIT;

-- Verification report. Expected values: both tables=1, opening=1,
-- Croatian translation=1, both migrations=1.
SELECT 'content_job_openings_table' AS `check_name`, COUNT(*) AS `actual`, 1 AS `expected`
FROM `information_schema`.`TABLES`
WHERE `TABLE_SCHEMA` = DATABASE()
  AND `TABLE_NAME` = 'content_job_openings'
UNION ALL
SELECT 'content_job_opening_translations_table', COUNT(*), 1
FROM `information_schema`.`TABLES`
WHERE `TABLE_SCHEMA` = DATABASE()
  AND `TABLE_NAME` = 'content_job_opening_translations'
UNION ALL
SELECT 'initial_job_opening', COUNT(*), 1
FROM `content_job_openings`
WHERE BINARY `code` = BINARY 'racunovoda-asistent-u-racunovodstvu'
UNION ALL
SELECT 'initial_job_translation_hr', COUNT(*), 1
FROM `content_job_opening_translations` AS `translation`
INNER JOIN `content_job_openings` AS `opening`
    ON `opening`.`id` = `translation`.`job_opening_id`
WHERE BINARY `opening`.`code` = BINARY 'racunovoda-asistent-u-racunovodstvu'
  AND `translation`.`locale` = 'hr'
UNION ALL
SELECT 'schema_migration', COUNT(*), 1
FROM `migrations`
WHERE BINARY `migration` = BINARY '2026_09_02_090000_create_content_job_openings_tables'
UNION ALL
SELECT 'content_migration', COUNT(*), 1
FROM `migrations`
WHERE BINARY `migration` = BINARY '2026_09_02_091000_add_initial_accounting_job_opening';
