<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Dashboard\Overview;
use App\Services\Analytics\GoogleAnalyticsDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class DashboardAnalyticsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_only_supported_ranges_and_has_a_clear_unconfigured_state(): void
    {
        config(['ga4.property_id' => '']);

        Livewire::test(Overview::class)
            ->assertSet('analyticsLoaded', false)
            ->assertSee('wire:init="loadAnalytics"', false)
            ->assertSee(__('dashboard.analytics.loading.title'))
            ->call('loadAnalytics')
            ->assertSet('analyticsLoaded', true)
            ->assertViewHas('analytics', fn (array $analytics): bool => $analytics['available'] === false
                && str_contains($analytics['reason'], 'Google Analytics'))
            ->assertSee('Kako povezati GA4')
            ->assertSee('G-YCD72KQJTC')
            ->set('rangeDays', '90')
            ->assertSet('rangeDays', '90')
            ->assertViewHas('days', 90)
            ->set('rangeDays', '5')
            ->assertSet('rangeDays', '7')
            ->assertViewHas('days', 7);
    }

    public function test_dashboard_maps_ga4_report_to_visits_devices_and_locations(): void
    {
        $this->mock(GoogleAnalyticsDataService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('runReport')->once()->andReturn([
                'available' => true,
                'reason' => null,
                'totals' => [
                    'active_users' => 10,
                    'new_users' => 4,
                    'sessions' => 20,
                    'page_views' => 50,
                    'engagement_rate' => 0.625,
                    'average_session_duration' => 91.2,
                ],
                'trend' => [[
                    'date' => now()->toDateString(),
                    'active_users' => 10,
                    'new_users' => 4,
                    'sessions' => 20,
                    'page_views' => 50,
                ]],
                'top_pages' => [[
                    'path' => '/financije',
                    'title' => 'Financije',
                    'page_views' => 30,
                    'active_users' => 8,
                ]],
                'sources' => [[
                    'source' => 'google',
                    'medium' => 'organic',
                    'sessions' => 12,
                    'active_users' => 8,
                ]],
                'devices' => [[
                    'device' => 'mobile',
                    'sessions' => 11,
                    'active_users' => 7,
                ]],
                'countries' => [],
                'cities' => [[
                    'city' => 'Zagreb',
                    'country' => 'Croatia',
                    'sessions' => 9,
                    'active_users' => 6,
                ]],
            ]);
        });

        Livewire::test(Overview::class)
            ->assertSet('analyticsLoaded', false)
            ->call('loadAnalytics')
            ->call('loadAnalytics')
            ->assertSet('analyticsLoaded', true)
            ->assertViewHas('analytics', function (array $analytics): bool {
                return $analytics['available'] === true
                    && data_get($analytics, 'kpis.visitors.value') === 10
                    && data_get($analytics, 'kpis.sessions.value') === 20
                    && data_get($analytics, 'kpis.pageviews.value') === 50
                    && data_get($analytics, 'kpis.engagement.value') === 62.5
                    && data_get($analytics, 'top_pages.0.label') === 'Financije'
                    && data_get($analytics, 'top_sources.0.label') === 'google'
                    && data_get($analytics, 'devices.0.label') === 'Mobitel'
                    && data_get($analytics, 'locations.0.label') === 'Zagreb, Croatia'
                    && count(data_get($analytics, 'chart.data.datasets', [])) === 2;
            })
            ->assertSee('data-dashboard-chart', false)
            ->assertSee('data-dashboard-chart-error', false);
    }

    public function test_dashboard_shows_a_retryable_error_when_the_initial_report_request_fails(): void
    {
        $this->mock(GoogleAnalyticsDataService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('runReport')->once()->andThrow(new RuntimeException('GA4 unavailable'));
        });

        Livewire::test(Overview::class)
            ->call('loadAnalytics')
            ->assertSet('analyticsLoaded', true)
            ->assertViewHas('analytics', fn (array $analytics): bool => $analytics['available'] === false
                && $analytics['reason_key'] === 'report_request_failed')
            ->assertSee(__('dashboard.analytics.loading.error_title'))
            ->assertSee(__('dashboard.analytics.reasons.report_request_failed'))
            ->assertSee(__('dashboard.analytics.loading.retry'));
    }
}
