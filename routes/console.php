<?php

use App\Models\User;
use App\Services\Content\ResourceAssetImportService;
use App\Services\Content\ResourcePublicationSyncService;
use App\Models\Settings\Local\Region;
use App\Services\Content\GlossaryImportService;
use App\Services\Content\WordPressBlogImportService;
use App\Services\Front\AddressDirectoryService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('content:import-wordpress-blog
    {file : Path to the WordPress XML export}
    {--limit=0 : Number of published posts to import (0 = all matches)}
    {--offset=0 : Skip the first N matching posts}
    {--locale=hr : Target locale for imported translations}
    {--category-mode=single : "single" to import into one category, "source" to use categories from XML}
    {--category-name=Novosti : Destination category name when category-mode=single}
    {--category-slug=novosti : Destination category slug when category-mode=single}
    {--slugs=* : Import only selected WordPress slugs}',
    function (WordPressBlogImportService $importer): int {
        try {
            $result = $importer->import((string) $this->argument('file'), [
                'limit' => (int) $this->option('limit'),
                'offset' => (int) $this->option('offset'),
                'locale' => (string) $this->option('locale'),
                'category_mode' => (string) $this->option('category-mode'),
                'category_name' => (string) $this->option('category-name'),
                'category_slug' => (string) $this->option('category-slug'),
                'slugs' => array_values(array_filter((array) $this->option('slugs'))),
                'user_id' => auth()->id(),
            ]);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Imported %d WordPress post(s) in locale "%s" using category mode "%s".',
            count($result['imported']),
            $result['locale'],
            $result['category_mode']
        ));

        if ($result['categories'] !== []) {
            $categoryLabels = collect($result['categories'])
                ->map(fn (array $category): string => sprintf('%s (/blog/%s)', $category['name'], $category['slug']))
                ->implode(', ');

            $this->line('Categories: '.$categoryLabels);
        }

        foreach ($result['imported'] as $row) {
            $this->line(sprintf(
                '- [%s] %s',
                strtoupper((string) $row['status']),
                (string) $row['title']
            ));
            $this->line('  legacy: '.$row['legacy_path']);
            $this->line('  canonical: '.$row['canonical_path']);

            if ($row['categories'] !== []) {
                $this->line('  categories: '.implode(', ', $row['categories']));
            }
        }

        return self::SUCCESS;
    })
    ->purpose('Import published WordPress blog posts from a WXR XML export');

Artisan::command('content:import-glossary
    {file : Path to the glossary CSV export}
    {--locale=hr : Target locale for imported terms}
    {--collection=svijet-financija : Collection code for imported terms}
    {--page-code=finance-glossary : Info page code to connect with the glossary}
    {--page-title=Svijet financija : Public page title}
    {--page-slug=svijet-financija : Public page slug}
    {--page-kicker=Rječnik pojmova : Label shown above the page hero}
    {--page-excerpt=Pretražite financijske i računovodstvene pojmove na jednom mjestu. : Intro copy shown below the page title}
    {--user-id= : Optional admin user ID for created_by/updated_by fields}',
    function (GlossaryImportService $importer): int {
        try {
            $result = $importer->import((string) $this->argument('file'), [
                'locale' => (string) $this->option('locale'),
                'collection' => (string) $this->option('collection'),
                'page_code' => (string) $this->option('page-code'),
                'page_title' => (string) $this->option('page-title'),
                'page_slug' => (string) $this->option('page-slug'),
                'page_kicker' => (string) $this->option('page-kicker'),
                'page_excerpt' => (string) $this->option('page-excerpt'),
                'user_id' => $this->option('user-id') !== null && $this->option('user-id') !== ''
                    ? (int) $this->option('user-id')
                    : null,
            ]);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Imported %d glossary term(s) into collection "%s" for locale "%s".',
            (int) $result['imported_count'],
            (string) $result['collection'],
            (string) $result['locale']
        ));

        $this->line(sprintf(
            'Page: %s (/%s)',
            (string) $result['page_code'],
            (string) $result['page_slug']
        ));

        foreach ((array) ($result['imported'] ?? []) as $row) {
            $this->line(sprintf(
                '- %s [%s]',
                (string) ($row['title'] ?? ''),
                (string) ($row['slug'] ?? '')
            ));
        }

        return self::SUCCESS;
    })
    ->purpose('Import finance glossary terms from a CSV export and prepare the related info page');

Artisan::command('content:import-resource-assets
    {file : Path to the WordPress XML export}
    {--codes=* : Import only selected resource document codes}
    {--force : Re-download assets even if local files already exist}',
    function (ResourceAssetImportService $importer): int {
        try {
            $result = $importer->import((string) $this->argument('file'), [
                'codes' => array_values(array_filter((array) $this->option('codes'))),
                'force' => (bool) $this->option('force'),
            ]);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Processed %d resource document(s). Downloads localized: %d. Covers localized: %d. Errors: %d.',
            (int) $result['processed_count'],
            (int) $result['localized_download_count'],
            (int) $result['localized_cover_count'],
            (int) $result['error_count']
        ));

        foreach ($result['documents'] as $row) {
            $this->line(sprintf(
                '- %s [download:%s cover:%s]',
                (string) $row['code'],
                (string) $row['download_status'],
                (string) $row['cover_status']
            ));

            if (($row['error'] ?? null) !== null) {
                $this->line('  error: '.(string) $row['error']);
            }
        }

        return self::SUCCESS;
    })
    ->purpose('Download resource PDFs and cover images locally from a WordPress XML export');

Artisan::command('content:sync-resource-publication
    {--codes=* : Sync only selected resource document codes}',
    function (ResourcePublicationSyncService $syncService): int {
        try {
            $result = $syncService->sync([
                'codes' => array_values(array_filter((array) $this->option('codes'))),
            ]);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Synced %d resource document(s). Active: %d. Inactive: %d. Remote published items: %d.',
            (int) $result['synced_count'],
            (int) $result['activated_count'],
            (int) $result['deactivated_count'],
            (int) $result['total_remote']
        ));

        foreach ($result['documents'] as $row) {
            $this->line(sprintf(
                '- %s [%s] %s',
                (string) $row['code'],
                (string) $row['status'],
                (string) ($row['published_at'] ?? 'no-date')
            ));
        }

        return self::SUCCESS;
    })
    ->purpose('Sync resource active state and publication dates from the live WordPress resources API');

Artisan::command('wholesale:token {user : User ID or email} {name=wholesale-client} {--abilities=wholesale.read,categories.read} {--expires=}', function (): int {
    if (! app(\App\Services\Catalog\CatalogFeatureService::class)->useApi()) {
        $this->error('Wholesale API is disabled in Catalog Features.');
        return self::FAILURE;
    }

    $selector = (string) $this->argument('user');
    $tokenName = trim((string) $this->argument('name'));
    $abilitiesRaw = trim((string) $this->option('abilities'));
    $expiresRaw = trim((string) $this->option('expires'));

    $user = User::query()
        ->when(ctype_digit($selector), fn ($query) => $query->where('id', (int) $selector), fn ($query) => $query->where('email', $selector))
        ->first();

    if (! $user) {
        $this->error('User not found.');
        return self::FAILURE;
    }
    if (! (bool) ($user->api_access_enabled ?? false)) {
        $this->error('User API access is disabled. Enable it in Settings > API first.');
        return self::FAILURE;
    }

    $abilities = collect(explode(',', $abilitiesRaw))
        ->map(fn ($ability) => trim((string) $ability))
        ->filter(fn ($ability) => $ability !== '')
        ->values()
        ->all();

    if ($abilities === []) {
        $this->error('At least one ability is required.');
        return self::FAILURE;
    }

    $expiresAt = null;
    if ($expiresRaw !== '') {
        try {
            $expiresAt = CarbonImmutable::parse($expiresRaw);
        } catch (\Throwable) {
            $this->error('Invalid --expires value. Use a parseable datetime, e.g. "2026-12-31 23:59:59".');
            return self::FAILURE;
        }
    }

    $token = $user->createToken($tokenName, $abilities, $expiresAt);

    $this->info('Token created.');
    $this->line('User: '.$user->id.' <'.$user->email.'>');
    $this->line('Name: '.$tokenName);
    $this->line('Abilities: '.implode(', ', $abilities));
    if ($expiresAt) {
        $this->line('Expires at: '.$expiresAt->toDateTimeString());
    }
    $this->newLine();
    $this->warn('Plain token (copy now):');
    $this->line($token->plainTextToken);

    return self::SUCCESS;
})->purpose('Create a wholesale API token for a user');

Artisan::command('local:import-regions-opencart {file : Path to OpenCart zones CSV} {--truncate : Truncate regions table before import}', function (): int {
    $file = (string) $this->argument('file');
    if (!is_file($file)) {
        $this->error('CSV file not found: '.$file);
        return self::FAILURE;
    }

    $handle = fopen($file, 'rb');
    if (!$handle) {
        $this->error('Unable to open CSV file.');
        return self::FAILURE;
    }

    $header = fgetcsv($handle, 0, ',', '"', '\\');
    if (!is_array($header)) {
        fclose($handle);
        $this->error('CSV header missing or invalid.');
        return self::FAILURE;
    }

    $header = array_map(static fn ($value): string => trim((string) $value), $header);
    $required = ['country_name', 'zone_name', 'code', 'status'];
    $index = array_flip($header);

    foreach ($required as $column) {
        if (!array_key_exists($column, $index)) {
            fclose($handle);
            $this->error('Missing required column: '.$column);
            return self::FAILURE;
        }
    }

    $normalize = static function (string $value): string {
        $value = strtolower(trim($value));
        $value = str_replace(
            ['&', ',', '.', "'", '’', '(', ')', '-', '/'],
            ' ',
            $value
        );
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        return trim($value);
    };

    $countryLabels = app(AddressDirectoryService::class)->countries('en');
    $countryMap = [];
    foreach ($countryLabels as $country) {
        $label = (string) ($country['label'] ?? '');
        $code = strtoupper((string) ($country['code'] ?? ''));
        if ($label === '' || $code === '') {
            continue;
        }
        $countryMap[$normalize($label)] = $code;
    }

    $aliases = [
        'antigua and barbuda' => 'AG',
        'bonaire sint eustatius and saba' => 'BQ',
        'bolivia' => 'BO',
        'bosnia and herzegovina' => 'BA',
        'brunei' => 'BN',
        'brunei darussalam' => 'BN',
        'cape verde' => 'CV',
        'congo' => 'CG',
        'congo republic of the' => 'CG',
        'congo democratic republic of the' => 'CD',
        'democratic republic of congo' => 'CD',
        'cote d ivoire' => 'CI',
        'curacao' => 'CW',
        'czech republic' => 'CZ',
        'east timor' => 'TL',
        'falkland islands malvinas' => 'FK',
        'france metropolitan' => 'FR',
        'heard and mc donald islands' => 'HM',
        'hong kong' => 'HK',
        'iran islamic republic of' => 'IR',
        'iran' => 'IR',
        'korea south' => 'KR',
        'korea north' => 'KP',
        'laos' => 'LA',
        'lao peoples democratic republic' => 'LA',
        'lao people s democratic republic' => 'LA',
        "lao people's democratic republic" => 'LA',
        'libyan arab jamahiriya' => 'LY',
        'macau' => 'MO',
        'macao' => 'MO',
        'macedonia the former yugoslav republic of' => 'MK',
        'micronesia' => 'FM',
        'micronesia federated states of' => 'FM',
        'moldova republic of' => 'MD',
        'myanmar' => 'MM',
        'palestinian territory occupied' => 'PS',
        'saint barthelemy' => 'BL',
        'saint kitts and nevis' => 'KN',
        'saint lucia' => 'LC',
        'saint martin french part' => 'MF',
        'saint pierre and miquelon' => 'PM',
        'saint vincent and the grenadines' => 'VC',
        'sao tome and principe' => 'ST',
        'swaziland' => 'SZ',
        'syrian arab republic' => 'SY',
        'slovak republic' => 'SK',
        'st pierre and miquelon' => 'PM',
        'trinidad and tobago' => 'TT',
        'turkey' => 'TR',
        'turks and caicos islands' => 'TC',
        'taiwan province of china' => 'TW',
        'tanzania united republic of' => 'TZ',
        'united kingdom' => 'GB',
        'united states minor outlying islands' => 'UM',
        'usa' => 'US',
        'virgin islands u s' => 'VI',
        'vatican city state holy see' => 'VA',
        'venezuela bolivarian republic of' => 'VE',
        'viet nam' => 'VN',
        'wallis and futuna islands' => 'WF',
        'moldova republic of' => 'MD',
        'russia' => 'RU',
        'russian federation' => 'RU',
        'syria' => 'SY',
        'taiwan' => 'TW',
        'tanzania' => 'TZ',
        'venezuela' => 'VE',
        'vietnam' => 'VN',
    ];

    $records = [];
    $seen = [];
    $unknownCountries = [];
    $sortOrderByCountry = [];
    $now = Carbon::now();

    while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
        if (!is_array($row) || $row === []) {
            continue;
        }

        $status = (string) ($row[$index['status']] ?? '1');
        if ($status !== '1') {
            continue;
        }

        $countryName = trim((string) ($row[$index['country_name']] ?? ''));
        $zoneName = trim((string) ($row[$index['zone_name']] ?? ''));
        $zoneCode = strtoupper(trim((string) ($row[$index['code']] ?? '')));
        if ($countryName === '' || $zoneName === '' || $zoneCode === '') {
            continue;
        }

        $normalizedCountry = $normalize($countryName);
        $countryCode = $countryMap[$normalizedCountry] ?? ($aliases[$normalizedCountry] ?? '');
        if ($countryCode === '') {
            $unknownCountries[$countryName] = true;
            continue;
        }

        $uniqueKey = $countryCode.'|'.$zoneCode;
        if (isset($seen[$uniqueKey])) {
            continue;
        }
        $seen[$uniqueKey] = true;

        $sortOrderByCountry[$countryCode] = ($sortOrderByCountry[$countryCode] ?? 0) + 1;

        $records[] = [
            'country_code' => $countryCode,
            'code' => $zoneCode,
            'name' => $zoneName,
            'is_active' => true,
            'sort_order' => $sortOrderByCountry[$countryCode],
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    fclose($handle);

    if ($records === []) {
        $this->error('No importable region rows found.');
        return self::FAILURE;
    }

    DB::transaction(function () use ($records): void {
        if ($this->option('truncate')) {
            Region::query()->delete();
        }

        foreach (array_chunk($records, 1000) as $chunk) {
            Region::query()->upsert(
                $chunk,
                ['country_code', 'code'],
                ['name', 'is_active', 'sort_order', 'updated_at']
            );
        }
    });

    $this->info('Imported regions: '.count($records));
    $this->line('Unknown countries skipped: '.count($unknownCountries));
    if ($unknownCountries !== []) {
        $preview = array_slice(array_keys($unknownCountries), 0, 20);
        $this->warn('Unknown list: '.implode(', ', $preview));
    }

    return self::SUCCESS;
})->purpose('Import OpenCart zones CSV into regions table');
