<?php

namespace App\Services\Analytics;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class GoogleAnalyticsDataService
{
    private const CACHE_VERSION = 'v1';

    private const TOTAL_METRICS = [
        'activeUsers',
        'newUsers',
        'sessions',
        'screenPageViews',
        'engagementRate',
        'averageSessionDuration',
    ];

    private const TREND_METRICS = [
        'activeUsers',
        'newUsers',
        'sessions',
        'screenPageViews',
    ];

    /**
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     period: array{start_date:string,end_date:string,days:int},
     *     totals: array{active_users:int,new_users:int,sessions:int,page_views:int,engagement_rate:float,average_session_duration:float},
     *     trend: array<int,array{date:string,active_users:int,new_users:int,sessions:int,page_views:int}>,
     *     top_pages: array<int,array{path:string,title:string,page_views:int,active_users:int}>,
     *     sources: array<int,array{source:string,medium:string,sessions:int,active_users:int}>,
     *     devices: array<int,array{device:string,sessions:int,active_users:int}>,
     *     countries: array<int,array{country:string,sessions:int,active_users:int}>,
     *     cities: array<int,array{city:string,country:string,sessions:int,active_users:int}>
     * }
     */
    public function runReport(DateTimeInterface|string $startDate, DateTimeInterface|string $endDate): array
    {
        $period = $this->normalizePeriod($startDate, $endDate);
        if ($period === null) {
            return $this->unavailableReport($this->emptyPeriod(), 'invalid_period');
        }

        $configuredPropertyId = trim((string) config('ga4.property_id', ''));
        if ($configuredPropertyId === '') {
            return $this->unavailableReport($period, 'not_configured');
        }

        $propertyId = $this->normalizePropertyId($configuredPropertyId);
        if ($propertyId === null) {
            return $this->unavailableReport($period, 'invalid_configuration');
        }

        $cacheKey = $this->cacheKey($propertyId, $period);
        $cached = Cache::get($cacheKey);
        if ($this->isCachedReport($cached, $period)) {
            return $cached;
        }

        $report = $this->fetchReport($propertyId, $period);
        $cacheTtl = max(0, (int) config('ga4.cache_ttl_seconds', 900));

        if ($report['available'] && $cacheTtl > 0) {
            Cache::put($cacheKey, $report, $cacheTtl);
        }

        return $report;
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     * @return array<string, mixed>
     */
    private function fetchReport(string $propertyId, array $period): array
    {
        $credentials = $this->serviceAccountCredentials();
        if ($credentials === null) {
            return $this->unavailableReport($period, 'credentials_unavailable');
        }

        $assertion = $this->createJwtAssertion($credentials);
        if ($assertion === null) {
            return $this->unavailableReport($period, 'authentication_failed');
        }

        $accessToken = $this->requestAccessToken($assertion);
        unset($assertion, $credentials);

        if ($accessToken === null) {
            return $this->unavailableReport($period, 'authentication_failed');
        }

        $responses = $this->requestDataReports(
            propertyId: $propertyId,
            accessToken: $accessToken,
            period: $period,
            definitions: $this->reportDefinitions($period['days']),
        );

        unset($accessToken);

        if ($responses === null) {
            return $this->unavailableReport($period, 'report_request_failed');
        }

        return $this->mapReportResponses($period, $responses);
    }

    /**
     * @return array{client_email:string,private_key:string}|null
     */
    private function serviceAccountCredentials(): ?array
    {
        $inlineCredentials = config('ga4.credentials_json');
        $decoded = null;

        if (is_array($inlineCredentials)) {
            $decoded = $inlineCredentials;
        } elseif (is_string($inlineCredentials) && trim($inlineCredentials) !== '') {
            $decoded = json_decode($inlineCredentials, true);
            if (! is_array($decoded)) {
                return null;
            }
        } else {
            $path = trim((string) config('ga4.credentials_path', ''));
            if ($path === '') {
                return null;
            }

            if (! $this->isAbsolutePath($path)) {
                $path = base_path($path);
            }

            if (! is_file($path) || ! is_readable($path)) {
                return null;
            }

            $contents = @file_get_contents($path);
            if (! is_string($contents) || trim($contents) === '') {
                return null;
            }

            $decoded = json_decode($contents, true);
            if (! is_array($decoded)) {
                return null;
            }
        }

        $clientEmail = trim((string) ($decoded['client_email'] ?? ''));
        $privateKey = trim(str_replace('\\n', "\n", (string) ($decoded['private_key'] ?? '')));

        if ($clientEmail === '' || $privateKey === '') {
            return null;
        }

        return [
            'client_email' => $clientEmail,
            'private_key' => $privateKey,
        ];
    }

    /**
     * @param  array{client_email:string,private_key:string}  $credentials
     */
    private function createJwtAssertion(array $credentials): ?string
    {
        $tokenUrl = $this->httpsUrl((string) config('ga4.oauth_token_url', ''));
        $scope = trim((string) config('ga4.oauth_scope', ''));
        if ($tokenUrl === null || $scope === '') {
            return null;
        }

        $issuedAt = time();
        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ], JSON_UNESCAPED_SLASHES));
        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => $scope,
            'aud' => $tokenUrl,
            'iat' => $issuedAt - 5,
            'exp' => $issuedAt + 3595,
        ], JSON_UNESCAPED_SLASHES));
        $unsignedToken = $header.'.'.$claims;

        $privateKey = @openssl_pkey_get_private($credentials['private_key']);
        if ($privateKey === false) {
            return null;
        }

        $signature = '';
        if (! @openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            return null;
        }

        return $unsignedToken.'.'.$this->base64UrlEncode($signature);
    }

    private function requestAccessToken(string $assertion): ?string
    {
        $tokenUrl = $this->httpsUrl((string) config('ga4.oauth_token_url', ''));
        if ($tokenUrl === null) {
            return null;
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout($this->timeoutSeconds())
                ->post($tokenUrl, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $assertion,
                ]);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $accessToken = trim((string) $response->json('access_token', ''));

        return $accessToken !== '' ? $accessToken : null;
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     * @param  array<string, array{dimensions:array<int,string>,metrics:array<int,string>,order_bys:array<int,array<string,mixed>>,limit:int}>  $definitions
     * @return array<string, array<string, mixed>>|null
     */
    private function requestDataReports(
        string $propertyId,
        string $accessToken,
        array $period,
        array $definitions,
    ): ?array {
        $baseUrl = $this->httpsUrl((string) config('ga4.data_api_base_url', ''));
        if ($baseUrl === null) {
            return null;
        }

        $url = rtrim($baseUrl, '/').'/properties/'.rawurlencode($propertyId).':runReport';

        try {
            $responses = Http::pool(function (Pool $pool) use ($definitions, $accessToken, $period, $url): void {
                foreach ($definitions as $key => $definition) {
                    $pool->as($key)
                        ->withToken($accessToken)
                        ->acceptJson()
                        ->timeout($this->timeoutSeconds())
                        ->post($url, $this->dataReportPayload($period, $definition));
                }
            });
        } catch (Throwable) {
            return null;
        }

        $mappedResponses = [];
        foreach (array_keys($definitions) as $key) {
            $response = $responses[$key] ?? null;
            if (! $response instanceof Response || ! $response->successful()) {
                return null;
            }

            $json = $response->json();
            if (! is_array($json)) {
                return null;
            }

            $mappedResponses[$key] = $json;
        }

        return $mappedResponses;
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     * @param  array{dimensions:array<int,string>,metrics:array<int,string>,order_bys:array<int,array<string,mixed>>,limit:int}  $definition
     * @return array<string, mixed>
     */
    private function dataReportPayload(array $period, array $definition): array
    {
        $payload = [
            'dateRanges' => [[
                'startDate' => $period['start_date'],
                'endDate' => $period['end_date'],
            ]],
            'metrics' => array_map(
                static fn (string $name): array => ['name' => $name],
                $definition['metrics']
            ),
            'keepEmptyRows' => false,
            'returnPropertyQuota' => false,
            'limit' => max(1, $definition['limit']),
        ];

        if ($definition['dimensions'] !== []) {
            $payload['dimensions'] = array_map(
                static fn (string $name): array => ['name' => $name],
                $definition['dimensions']
            );
        }

        if ($definition['order_bys'] !== []) {
            $payload['orderBys'] = $definition['order_bys'];
        }

        return $payload;
    }

    /**
     * @return array<string, array{dimensions:array<int,string>,metrics:array<int,string>,order_bys:array<int,array<string,mixed>>,limit:int}>
     */
    private function reportDefinitions(int $days): array
    {
        $topPagesLimit = max(1, min(100, (int) config('ga4.top_pages_limit', 10)));
        $breakdownLimit = max(1, min(100, (int) config('ga4.breakdown_limit', 10)));

        return [
            'totals' => [
                'dimensions' => [],
                'metrics' => self::TOTAL_METRICS,
                'order_bys' => [],
                'limit' => 1,
            ],
            'trend' => [
                'dimensions' => ['date'],
                'metrics' => self::TREND_METRICS,
                'order_bys' => [[
                    'dimension' => ['dimensionName' => 'date'],
                    'desc' => false,
                ]],
                'limit' => max(1, min(100000, $days)),
            ],
            'top_pages' => [
                'dimensions' => ['pagePath', 'pageTitle'],
                'metrics' => ['screenPageViews', 'activeUsers'],
                'order_bys' => [[
                    'metric' => ['metricName' => 'screenPageViews'],
                    'desc' => true,
                ]],
                'limit' => $topPagesLimit,
            ],
            'sources' => [
                'dimensions' => ['sessionSource', 'sessionMedium'],
                'metrics' => ['sessions', 'activeUsers'],
                'order_bys' => [[
                    'metric' => ['metricName' => 'sessions'],
                    'desc' => true,
                ]],
                'limit' => $breakdownLimit,
            ],
            'devices' => [
                'dimensions' => ['deviceCategory'],
                'metrics' => ['sessions', 'activeUsers'],
                'order_bys' => [[
                    'metric' => ['metricName' => 'sessions'],
                    'desc' => true,
                ]],
                'limit' => $breakdownLimit,
            ],
            'countries' => [
                'dimensions' => ['country'],
                'metrics' => ['sessions', 'activeUsers'],
                'order_bys' => [[
                    'metric' => ['metricName' => 'sessions'],
                    'desc' => true,
                ]],
                'limit' => $breakdownLimit,
            ],
            'cities' => [
                'dimensions' => ['city', 'country'],
                'metrics' => ['sessions', 'activeUsers'],
                'order_bys' => [[
                    'metric' => ['metricName' => 'sessions'],
                    'desc' => true,
                ]],
                'limit' => $breakdownLimit,
            ],
        ];
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     * @param  array<string, array<string, mixed>>  $responses
     * @return array<string, mixed>
     */
    private function mapReportResponses(array $period, array $responses): array
    {
        $total = $this->mappedRows($responses['totals'] ?? [])[0] ?? [];

        return [
            'available' => true,
            'reason' => null,
            'period' => $period,
            'totals' => [
                'active_users' => $this->integerValue($total['activeUsers'] ?? 0),
                'new_users' => $this->integerValue($total['newUsers'] ?? 0),
                'sessions' => $this->integerValue($total['sessions'] ?? 0),
                'page_views' => $this->integerValue($total['screenPageViews'] ?? 0),
                'engagement_rate' => $this->floatValue($total['engagementRate'] ?? 0),
                'average_session_duration' => $this->floatValue($total['averageSessionDuration'] ?? 0),
            ],
            'trend' => $this->mapTrend($period, $responses['trend'] ?? []),
            'top_pages' => array_map(fn (array $row): array => [
                'path' => trim((string) ($row['pagePath'] ?? '')) ?: '/',
                'title' => trim((string) ($row['pageTitle'] ?? '')),
                'page_views' => $this->integerValue($row['screenPageViews'] ?? 0),
                'active_users' => $this->integerValue($row['activeUsers'] ?? 0),
            ], $this->mappedRows($responses['top_pages'] ?? [])),
            'sources' => array_map(fn (array $row): array => [
                'source' => trim((string) ($row['sessionSource'] ?? '')),
                'medium' => trim((string) ($row['sessionMedium'] ?? '')),
                'sessions' => $this->integerValue($row['sessions'] ?? 0),
                'active_users' => $this->integerValue($row['activeUsers'] ?? 0),
            ], $this->mappedRows($responses['sources'] ?? [])),
            'devices' => array_map(fn (array $row): array => [
                'device' => trim((string) ($row['deviceCategory'] ?? '')),
                'sessions' => $this->integerValue($row['sessions'] ?? 0),
                'active_users' => $this->integerValue($row['activeUsers'] ?? 0),
            ], $this->mappedRows($responses['devices'] ?? [])),
            'countries' => array_map(fn (array $row): array => [
                'country' => trim((string) ($row['country'] ?? '')),
                'sessions' => $this->integerValue($row['sessions'] ?? 0),
                'active_users' => $this->integerValue($row['activeUsers'] ?? 0),
            ], $this->mappedRows($responses['countries'] ?? [])),
            'cities' => array_map(fn (array $row): array => [
                'city' => trim((string) ($row['city'] ?? '')),
                'country' => trim((string) ($row['country'] ?? '')),
                'sessions' => $this->integerValue($row['sessions'] ?? 0),
                'active_users' => $this->integerValue($row['activeUsers'] ?? 0),
            ], $this->mappedRows($responses['cities'] ?? [])),
        ];
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     * @param  array<string, mixed>  $response
     * @return array<int, array{date:string,active_users:int,new_users:int,sessions:int,page_views:int}>
     */
    private function mapTrend(array $period, array $response): array
    {
        $byDate = [];
        foreach ($this->mappedRows($response) as $row) {
            $date = $this->normalizeAnalyticsDate((string) ($row['date'] ?? ''));
            if ($date === null) {
                continue;
            }

            $byDate[$date] = [
                'date' => $date,
                'active_users' => $this->integerValue($row['activeUsers'] ?? 0),
                'new_users' => $this->integerValue($row['newUsers'] ?? 0),
                'sessions' => $this->integerValue($row['sessions'] ?? 0),
                'page_views' => $this->integerValue($row['screenPageViews'] ?? 0),
            ];
        }

        $trend = [];
        $date = CarbonImmutable::createFromFormat('!Y-m-d', $period['start_date']);
        $endDate = $period['end_date'];

        while ($date && $date->toDateString() <= $endDate) {
            $dateString = $date->toDateString();
            $trend[] = $byDate[$dateString] ?? [
                'date' => $dateString,
                'active_users' => 0,
                'new_users' => 0,
                'sessions' => 0,
                'page_views' => 0,
            ];
            $date = $date->addDay();
        }

        return $trend;
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, array<string, string>>
     */
    private function mappedRows(array $response): array
    {
        $dimensionNames = array_values(array_filter(array_map(
            static fn (mixed $header): string => is_array($header) ? trim((string) ($header['name'] ?? '')) : '',
            is_array($response['dimensionHeaders'] ?? null) ? $response['dimensionHeaders'] : []
        )));
        $metricNames = array_values(array_filter(array_map(
            static fn (mixed $header): string => is_array($header) ? trim((string) ($header['name'] ?? '')) : '',
            is_array($response['metricHeaders'] ?? null) ? $response['metricHeaders'] : []
        )));

        $mapped = [];
        foreach (is_array($response['rows'] ?? null) ? $response['rows'] : [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $values = [];
            $dimensionValues = is_array($row['dimensionValues'] ?? null) ? $row['dimensionValues'] : [];
            foreach ($dimensionNames as $index => $name) {
                $value = $dimensionValues[$index] ?? [];
                $values[$name] = is_array($value) ? (string) ($value['value'] ?? '') : '';
            }

            $metricValues = is_array($row['metricValues'] ?? null) ? $row['metricValues'] : [];
            foreach ($metricNames as $index => $name) {
                $value = $metricValues[$index] ?? [];
                $values[$name] = is_array($value) ? (string) ($value['value'] ?? '') : '';
            }

            $mapped[] = $values;
        }

        return $mapped;
    }

    /**
     * @return array{start_date:string,end_date:string,days:int}|null
     */
    private function normalizePeriod(DateTimeInterface|string $startDate, DateTimeInterface|string $endDate): ?array
    {
        try {
            $start = $startDate instanceof DateTimeInterface
                ? CarbonImmutable::instance($startDate)->startOfDay()
                : CarbonImmutable::parse($startDate)->startOfDay();
            $end = $endDate instanceof DateTimeInterface
                ? CarbonImmutable::instance($endDate)->startOfDay()
                : CarbonImmutable::parse($endDate)->startOfDay();
        } catch (Throwable) {
            return null;
        }

        if ($start->greaterThan($end)) {
            return null;
        }

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'days' => (int) $start->diffInDays($end) + 1,
        ];
    }

    private function normalizePropertyId(string $value): ?string
    {
        $value = preg_replace('#^properties/#i', '', trim($value)) ?? '';

        return preg_match('/^\d+$/', $value) === 1 ? $value : null;
    }

    private function normalizeAnalyticsDate(string $value): ?string
    {
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', trim($value), $matches) !== 1) {
            return null;
        }

        if (! checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1])) {
            return null;
        }

        return $matches[1].'-'.$matches[2].'-'.$matches[3];
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     */
    private function cacheKey(string $propertyId, array $period): string
    {
        return 'ga4-data-report:'.self::CACHE_VERSION.':'.hash(
            'sha256',
            $propertyId.'|'.$period['start_date'].'|'.$period['end_date']
        );
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     */
    private function isCachedReport(mixed $cached, array $period): bool
    {
        return is_array($cached)
            && ($cached['available'] ?? false) === true
            && ($cached['period'] ?? null) === $period
            && is_array($cached['totals'] ?? null)
            && is_array($cached['trend'] ?? null);
    }

    /**
     * @param  array{start_date:string,end_date:string,days:int}  $period
     * @return array<string, mixed>
     */
    private function unavailableReport(array $period, string $reason): array
    {
        return [
            'available' => false,
            'reason' => $reason,
            'period' => $period,
            'totals' => [
                'active_users' => 0,
                'new_users' => 0,
                'sessions' => 0,
                'page_views' => 0,
                'engagement_rate' => 0.0,
                'average_session_duration' => 0.0,
            ],
            'trend' => [],
            'top_pages' => [],
            'sources' => [],
            'devices' => [],
            'countries' => [],
            'cities' => [],
        ];
    }

    /**
     * @return array{start_date:string,end_date:string,days:int}
     */
    private function emptyPeriod(): array
    {
        return [
            'start_date' => '',
            'end_date' => '',
            'days' => 0,
        ];
    }

    private function timeoutSeconds(): int
    {
        return max(1, min(60, (int) config('ga4.timeout_seconds', 15)));
    }

    private function httpsUrl(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || filter_var($value, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return strtolower((string) parse_url($value, PHP_URL_SCHEME)) === 'https' ? $value : null;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
    }

    private function base64UrlEncode(string|false $value): string
    {
        if ($value === false) {
            return '';
        }

        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function integerValue(mixed $value): int
    {
        return is_numeric($value) ? (int) round((float) $value) : 0;
    }

    private function floatValue(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
