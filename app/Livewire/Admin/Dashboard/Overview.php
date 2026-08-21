<?php

namespace App\Livewire\Admin\Dashboard;

use App\Services\Analytics\GoogleAnalyticsDataService;
use Carbon\CarbonImmutable;
use Livewire\Component;

class Overview extends Component
{
    /** @var array<int, string> */
    private const ALLOWED_RANGES = ['1', '7', '30', '90'];

    public string $rangeDays = '7';

    public function mount(): void
    {
        $this->normalizeRange();
    }

    public function updatedRangeDays(): void
    {
        $this->normalizeRange();
    }

    public function render()
    {
        $days = (int) $this->rangeDays;
        $end = CarbonImmutable::now()->endOfDay();
        $start = $end->startOfDay()->subDays($days - 1);
        $analyticsReport = app(GoogleAnalyticsDataService::class)->runReport(
            $start,
            $end,
        );

        return view('livewire.admin.dashboard.overview', [
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'analytics' => $this->analyticsPayload($analyticsReport),
        ]);
    }

    /**
     * Convert the provider response into the small, stable shape consumed by the dashboard.
     *
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    private function analyticsPayload(array $report): array
    {
        $reason = trim((string) ($report['reason'] ?? ''));

        if (! (bool) ($report['available'] ?? false)) {
            $reasonKey = in_array($reason, [
                'invalid_period',
                'not_configured',
                'invalid_configuration',
                'credentials_unavailable',
                'authentication_failed',
                'report_request_failed',
            ], true) ? $reason : 'unavailable';

            return [
                'available' => false,
                'provider' => __('dashboard.analytics.provider'),
                'reason' => __("dashboard.analytics.reasons.{$reasonKey}"),
                'measurement_instruction' => __('dashboard.analytics.setup.measurement'),
                'credentials_instruction' => __('dashboard.analytics.setup.credentials'),
            ];
        }

        $totals = (array) ($report['totals'] ?? []);
        $sessions = (int) ($totals['sessions'] ?? 0);
        $newUsers = (int) ($totals['new_users'] ?? 0);
        $engagementRate = max(0, (float) ($totals['engagement_rate'] ?? 0));
        $averageSessionDuration = max(0, (float) ($totals['average_session_duration'] ?? 0));
        $pageViews = (int) ($totals['page_views'] ?? 0);

        return [
            'available' => true,
            'provider' => __('dashboard.analytics.provider'),
            'kpis' => [
                'visitors' => [
                    'value' => (int) ($totals['active_users'] ?? 0),
                    'note' => trans_choice(
                        'dashboard.analytics.notes.new_users',
                        $newUsers,
                        ['count' => number_format($newUsers)],
                    ),
                ],
                'sessions' => [
                    'value' => $sessions,
                    'note' => __('dashboard.analytics.notes.duration', [
                        'seconds' => number_format($averageSessionDuration, 0, ',', '.'),
                    ]),
                ],
                'pageviews' => [
                    'value' => $pageViews,
                    'note' => __('dashboard.analytics.notes.pages_per_session', [
                        'count' => number_format($sessions > 0 ? $pageViews / $sessions : 0, 1, ',', '.'),
                    ]),
                ],
                'engagement' => [
                    'value' => round($engagementRate * 100, 1),
                    'note' => __('dashboard.analytics.notes.engagement'),
                ],
            ],
            'chart' => $this->analyticsTrendChart((array) ($report['trend'] ?? [])),
            'top_pages' => collect((array) ($report['top_pages'] ?? []))
                ->map(function ($row): array {
                    $row = (array) $row;
                    $path = trim((string) ($row['path'] ?? '')) ?: '/';
                    $title = $this->analyticsLabel((string) ($row['title'] ?? ''));

                    return [
                        'label' => $title ?? $path,
                        'detail' => $title !== null ? $path : null,
                        'count' => (int) ($row['page_views'] ?? 0),
                    ];
                })
                ->values()
                ->all(),
            'top_sources' => collect((array) ($report['sources'] ?? []))
                ->map(function ($row): array {
                    $row = (array) $row;

                    return [
                        'label' => $this->analyticsLabel((string) ($row['source'] ?? ''))
                            ?? __('dashboard.analytics.unknown_source'),
                        'detail' => $this->analyticsLabel((string) ($row['medium'] ?? '')),
                        'count' => (int) ($row['sessions'] ?? 0),
                    ];
                })
                ->values()
                ->all(),
            'devices' => collect((array) ($report['devices'] ?? []))
                ->map(function ($row): array {
                    $row = (array) $row;
                    $device = strtolower(trim((string) ($row['device'] ?? '')));
                    $deviceKey = in_array($device, ['desktop', 'mobile', 'tablet', 'smart tv'], true)
                        ? $device
                        : 'other';

                    return [
                        'label' => __("dashboard.analytics.devices.{$deviceKey}"),
                        'count' => (int) ($row['sessions'] ?? 0),
                    ];
                })
                ->values()
                ->all(),
            'locations' => $this->analyticsLocations($report),
        ];
    }

    /**
     * @param  array<int, mixed>  $trend
     * @return array<string, mixed>
     */
    private function analyticsTrendChart(array $trend): array
    {
        $rows = collect($trend)
            ->filter(static fn ($row): bool => is_array($row))
            ->values();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $rows->map(static function (array $row): string {
                    try {
                        return CarbonImmutable::parse((string) ($row['date'] ?? ''))->format('d.m.');
                    } catch (\Throwable) {
                        return (string) ($row['date'] ?? '');
                    }
                })->all(),
                'datasets' => [
                    [
                        'label' => __('dashboard.analytics.chart.visitors'),
                        'data' => $rows->map(static fn (array $row): int => (int) ($row['active_users'] ?? 0))->all(),
                        'borderColor' => '#0f172a',
                        'backgroundColor' => 'rgba(15, 23, 42, 0.10)',
                        'pointBackgroundColor' => '#0f172a',
                        'pointRadius' => $rows->count() > 30 ? 0 : 2,
                        'tension' => 0.3,
                        'fill' => false,
                        'borderWidth' => 2,
                    ],
                    [
                        'label' => __('dashboard.analytics.chart.sessions'),
                        'data' => $rows->map(static fn (array $row): int => (int) ($row['sessions'] ?? 0))->all(),
                        'borderColor' => '#a7813f',
                        'backgroundColor' => 'rgba(167, 129, 63, 0.12)',
                        'pointBackgroundColor' => '#a7813f',
                        'pointRadius' => $rows->count() > 30 ? 0 : 2,
                        'tension' => 0.3,
                        'fill' => false,
                        'borderWidth' => 2,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'interaction' => ['mode' => 'index', 'intersect' => false],
                'plugins' => ['legend' => ['position' => 'bottom']],
                'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array<int, array{label: string, count: int}>
     */
    private function analyticsLocations(array $report): array
    {
        $cities = collect((array) ($report['cities'] ?? []))
            ->map(function ($row): ?array {
                $row = (array) $row;
                $city = $this->analyticsLabel((string) ($row['city'] ?? ''));
                $country = $this->analyticsLabel((string) ($row['country'] ?? ''));

                if ($city === null) {
                    return null;
                }

                return [
                    'label' => $country !== null ? "{$city}, {$country}" : $city,
                    'count' => (int) ($row['sessions'] ?? 0),
                ];
            })
            ->filter()
            ->values();

        if ($cities->isNotEmpty()) {
            return $cities->all();
        }

        return collect((array) ($report['countries'] ?? []))
            ->map(function ($row): ?array {
                $row = (array) $row;
                $country = $this->analyticsLabel((string) ($row['country'] ?? ''));

                return $country === null ? null : [
                    'label' => $country,
                    'count' => (int) ($row['sessions'] ?? 0),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function analyticsLabel(string $value): ?string
    {
        $value = trim($value);

        return $value === '' || in_array(strtolower($value), ['(not set)', 'not set', '(none)'], true)
            ? null
            : $value;
    }

    private function normalizeRange(): void
    {
        if (! in_array($this->rangeDays, self::ALLOWED_RANGES, true)) {
            $this->rangeDays = '7';
        }
    }
}
