<?php

namespace Tests\Feature\Analytics;

use App\Services\Analytics\GoogleAnalyticsDataService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleAnalyticsDataServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Http::preventStrayRequests();

        config([
            'ga4.property_id' => null,
            'ga4.credentials_json' => null,
            'ga4.credentials_path' => null,
            'ga4.data_api_base_url' => 'https://analytics.example.test/v1beta',
            'ga4.oauth_token_url' => 'https://oauth.example.test/token',
            'ga4.cache_ttl_seconds' => 900,
            'ga4.timeout_seconds' => 5,
            'ga4.top_pages_limit' => 10,
            'ga4.breakdown_limit' => 10,
        ]);
    }

    public function test_it_returns_a_stable_unavailable_shape_when_not_configured(): void
    {
        Http::fake();

        $report = app(GoogleAnalyticsDataService::class)
            ->runReport('2026-08-01', '2026-08-03');

        $this->assertFalse($report['available']);
        $this->assertSame('not_configured', $report['reason']);
        $this->assertSame([
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-03',
            'days' => 3,
        ], $report['period']);
        $this->assertSame(0, $report['totals']['active_users']);
        $this->assertSame([], $report['trend']);
        $this->assertSame([], $report['top_pages']);
        $this->assertSame([], $report['sources']);
        $this->assertSame([], $report['devices']);
        $this->assertSame([], $report['countries']);
        $this->assertSame([], $report['cities']);

        Http::assertNothingSent();
    }

    public function test_it_fetches_maps_and_caches_all_dashboard_reports_with_inline_credentials(): void
    {
        $credentials = $this->temporaryCredentials();
        config([
            'ga4.property_id' => 'properties/123456789',
            'ga4.credentials_json' => json_encode($credentials, JSON_THROW_ON_ERROR),
        ]);

        Http::fake(function (Request $request) use ($credentials) {
            if ($request->url() === 'https://oauth.example.test/token') {
                $this->assertSame(
                    'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    $request->data()['grant_type'] ?? null
                );
                $segments = explode('.', (string) ($request->data()['assertion'] ?? ''));
                $this->assertCount(3, $segments);
                $claims = json_decode($this->decodeJwtSegment($segments[1]), true, flags: JSON_THROW_ON_ERROR);
                $this->assertSame('https://oauth.example.test/token', $claims['aud'] ?? null);
                $this->assertSame('https://www.googleapis.com/auth/analytics.readonly', $claims['scope'] ?? null);
                $this->assertLessThanOrEqual(3600, ($claims['exp'] ?? 0) - ($claims['iat'] ?? 0));
                $this->assertStringNotContainsString($credentials['private_key'], $request->body());

                return Http::response([
                    'access_token' => 'test-access-token',
                    'token_type' => 'Bearer',
                    'expires_in' => 3600,
                ]);
            }

            $this->assertSame(
                'https://analytics.example.test/v1beta/properties/123456789:runReport',
                $request->url()
            );
            $this->assertTrue($request->hasHeader('Authorization', 'Bearer test-access-token'));
            $this->assertSame([[
                'startDate' => '2026-08-01',
                'endDate' => '2026-08-03',
            ]], $request->data()['dateRanges'] ?? null);

            $dimensions = array_map(
                static fn (array $dimension): string => (string) $dimension['name'],
                $request->data()['dimensions'] ?? []
            );
            $metrics = array_map(
                static fn (array $metric): string => (string) $metric['name'],
                $request->data()['metrics'] ?? []
            );

            $rows = match (implode(',', $dimensions)) {
                '' => [[
                    'activeUsers' => '42',
                    'newUsers' => '18',
                    'sessions' => '60',
                    'screenPageViews' => '120',
                    'engagementRate' => '0.65',
                    'averageSessionDuration' => '84.5',
                ]],
                'date' => [
                    [
                        'date' => '20260801',
                        'activeUsers' => '12',
                        'newUsers' => '5',
                        'sessions' => '16',
                        'screenPageViews' => '31',
                    ],
                    [
                        'date' => '20260803',
                        'activeUsers' => '20',
                        'newUsers' => '8',
                        'sessions' => '27',
                        'screenPageViews' => '55',
                    ],
                ],
                'pagePath,pageTitle' => [[
                    'pagePath' => '/usluge/revizija',
                    'pageTitle' => 'Revizija',
                    'screenPageViews' => '34',
                    'activeUsers' => '21',
                ]],
                'sessionSource,sessionMedium' => [[
                    'sessionSource' => 'google',
                    'sessionMedium' => 'organic',
                    'sessions' => '28',
                    'activeUsers' => '22',
                ]],
                'deviceCategory' => [[
                    'deviceCategory' => 'desktop',
                    'sessions' => '39',
                    'activeUsers' => '30',
                ]],
                'country' => [[
                    'country' => 'Croatia',
                    'sessions' => '41',
                    'activeUsers' => '32',
                ]],
                'city,country' => [[
                    'city' => 'Zagreb',
                    'country' => 'Croatia',
                    'sessions' => '25',
                    'activeUsers' => '19',
                ]],
                default => [],
            };

            return Http::response($this->analyticsResponse($dimensions, $metrics, $rows));
        });

        $service = app(GoogleAnalyticsDataService::class);
        $report = $service->runReport('2026-08-01', '2026-08-03');

        $this->assertTrue($report['available']);
        $this->assertNull($report['reason']);
        $this->assertSame([
            'active_users' => 42,
            'new_users' => 18,
            'sessions' => 60,
            'page_views' => 120,
            'engagement_rate' => 0.65,
            'average_session_duration' => 84.5,
        ], $report['totals']);
        $this->assertSame([
            'date' => '2026-08-02',
            'active_users' => 0,
            'new_users' => 0,
            'sessions' => 0,
            'page_views' => 0,
        ], $report['trend'][1]);
        $this->assertSame('/usluge/revizija', $report['top_pages'][0]['path']);
        $this->assertSame('organic', $report['sources'][0]['medium']);
        $this->assertSame('desktop', $report['devices'][0]['device']);
        $this->assertSame('Croatia', $report['countries'][0]['country']);
        $this->assertSame('Zagreb', $report['cities'][0]['city']);

        $serializedReport = json_encode($report, JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString($credentials['private_key'], $serializedReport);
        $this->assertStringNotContainsString('test-access-token', $serializedReport);
        $this->assertStringNotContainsString($credentials['client_email'], $serializedReport);

        Http::assertSentCount(8);

        $this->assertSame($report, $service->runReport('2026-08-01', '2026-08-03'));
        Http::assertSentCount(8);
    }

    public function test_it_reads_credentials_from_a_path_and_fails_authentication_without_exposing_them(): void
    {
        $credentials = $this->temporaryCredentials();
        $path = tempnam(sys_get_temp_dir(), 'ga4-test-');
        $this->assertNotFalse($path);
        file_put_contents($path, json_encode($credentials, JSON_THROW_ON_ERROR));

        try {
            config([
                'ga4.property_id' => '123456789',
                'ga4.credentials_path' => $path,
            ]);

            Http::fake([
                'https://oauth.example.test/token' => Http::response([
                    'error' => 'invalid_grant',
                ], 401),
            ]);

            $report = app(GoogleAnalyticsDataService::class)
                ->runReport('2026-08-01', '2026-08-03');

            $this->assertFalse($report['available']);
            $this->assertSame('authentication_failed', $report['reason']);
            $this->assertSame([], $report['trend']);

            $serializedReport = json_encode($report, JSON_THROW_ON_ERROR);
            $this->assertStringNotContainsString($credentials['private_key'], $serializedReport);
            $this->assertStringNotContainsString($credentials['client_email'], $serializedReport);

            Http::assertSentCount(1);
        } finally {
            @unlink($path);
        }
    }

    public function test_it_returns_an_uncached_unavailable_state_when_the_data_api_fails(): void
    {
        config([
            'ga4.property_id' => '123456789',
            'ga4.credentials_json' => $this->temporaryCredentials(),
        ]);

        Http::fake([
            'https://oauth.example.test/token' => Http::response([
                'access_token' => 'short-lived-test-token',
            ]),
            'https://analytics.example.test/*' => Http::response([
                'error' => ['status' => 'UNAVAILABLE'],
            ], 503),
        ]);

        $service = app(GoogleAnalyticsDataService::class);
        $first = $service->runReport('2026-08-01', '2026-08-03');
        $second = $service->runReport('2026-08-01', '2026-08-03');

        $this->assertFalse($first['available']);
        $this->assertSame('report_request_failed', $first['reason']);
        $this->assertSame($first, $second);
        Http::assertSentCount(16);
    }

    /**
     * @return array{client_email:string,private_key:string}
     */
    private function temporaryCredentials(): array
    {
        $key = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $this->assertNotFalse($key);

        $privateKey = '';
        $this->assertTrue(openssl_pkey_export($key, $privateKey));

        return [
            'client_email' => 'dashboard-reader@example.test',
            'private_key' => $privateKey,
        ];
    }

    /**
     * @param  array<int, string>  $dimensions
     * @param  array<int, string>  $metrics
     * @param  array<int, array<string, string>>  $rows
     * @return array<string, mixed>
     */
    private function analyticsResponse(array $dimensions, array $metrics, array $rows): array
    {
        return [
            'dimensionHeaders' => array_map(
                static fn (string $name): array => ['name' => $name],
                $dimensions
            ),
            'metricHeaders' => array_map(
                static fn (string $name): array => ['name' => $name, 'type' => 'TYPE_INTEGER'],
                $metrics
            ),
            'rows' => array_map(static fn (array $row): array => [
                'dimensionValues' => array_map(
                    static fn (string $name): array => ['value' => (string) ($row[$name] ?? '')],
                    $dimensions
                ),
                'metricValues' => array_map(
                    static fn (string $name): array => ['value' => (string) ($row[$name] ?? '0')],
                    $metrics
                ),
            ], $rows),
            'rowCount' => count($rows),
        ];
    }

    private function decodeJwtSegment(string $segment): string
    {
        $padding = strlen($segment) % 4;
        if ($padding > 0) {
            $segment .= str_repeat('=', 4 - $padding);
        }

        return (string) base64_decode(strtr($segment, '-_', '+/'), true);
    }
}
