<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\ContentBlockSlot;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Support\ContactMessage;
use App\Models\Content\Support\Faq;
use App\Models\User;
use App\Models\User\UserTrackingEvent;
use App\Services\Settings\SystemSettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Overview extends Component
{
    public string $rangeDays = '7';

    public function mount(): void
    {
        if (! in_array($this->rangeDays, ['1', '7', '30'], true)) {
            $this->rangeDays = '7';
        }
    }

    public function render()
    {
        $settings = app(SystemSettingsService::class);
        $trackingEnabled = (bool) $settings->get(
            'user_tracking_enabled',
            (bool) config('user_features.flags.user_tracking_enabled', true)
        );

        [$start, $end, $previousStart, $previousEnd, $days] = $this->resolveRangeWindow();

        $usersCurrentCount = User::query()->whereBetween('created_at', [$start, $end])->count();
        $usersPreviousCount = User::query()->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $messagesCurrentCount = ContactMessage::query()->whereBetween('created_at', [$start, $end])->count();
        $messagesPreviousCount = ContactMessage::query()->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $pagesCurrentCount = InfoPage::query()->whereBetween('created_at', [$start, $end])->count();
        $pagesPreviousCount = InfoPage::query()->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $postsCurrentCount = BlogPost::query()->whereBetween('created_at', [$start, $end])->count();
        $postsPreviousCount = BlogPost::query()->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $kpis = [
            [
                'label' => __('New Users'),
                'value' => number_format($usersCurrentCount),
                'delta' => $this->formatDelta($usersCurrentCount, $usersPreviousCount),
            ],
            [
                'label' => __('Contact Messages'),
                'value' => number_format($messagesCurrentCount),
                'delta' => $this->formatDelta($messagesCurrentCount, $messagesPreviousCount),
            ],
            [
                'label' => __('New Pages'),
                'value' => number_format($pagesCurrentCount),
                'delta' => $this->formatDelta($pagesCurrentCount, $pagesPreviousCount),
            ],
            [
                'label' => __('New Blog Posts'),
                'value' => number_format($postsCurrentCount),
                'delta' => $this->formatDelta($postsCurrentCount, $postsPreviousCount),
            ],
        ];

        $recentAdminActivity = Activity::query()
            ->with('causer:id,name,email')
            ->latest('id')
            ->limit(8)
            ->get();

        $recentContactMessages = ContactMessage::query()
            ->latest('id')
            ->limit(8)
            ->get(['id', 'name', 'email', 'subject', 'created_at']);

        $recentTrackingEvents = collect();
        if ($trackingEnabled) {
            $recentTrackingEvents = UserTrackingEvent::query()
                ->with('user:id,name,email')
                ->latest('occurred_at')
                ->limit(8)
                ->get(['id', 'user_id', 'event', 'url', 'occurred_at']);
        }

        $trendRows = $this->buildTrendRows((int) min($days, 30));
        $trendLabels = $trendRows
            ->map(fn (array $row): string => CarbonImmutable::parse((string) $row['date'])->format('M d'))
            ->values()
            ->all();
        $trendUsers = $trendRows
            ->map(fn (array $row): int => (int) $row['users'])
            ->values()
            ->all();
        $trendMessages = $trendRows
            ->map(fn (array $row): int => (int) $row['messages'])
            ->values()
            ->all();

        $dashboardCharts = [
            'users_contacts_trend' => [
                'type' => 'line',
                'data' => [
                    'labels' => $trendLabels,
                    'datasets' => [
                        [
                            'label' => __('New Users'),
                            'data' => $trendUsers,
                            'borderColor' => '#0f766e',
                            'backgroundColor' => 'rgba(15, 118, 110, 0.15)',
                            'pointBackgroundColor' => '#0f766e',
                            'pointRadius' => 2.5,
                            'tension' => 0.35,
                            'fill' => true,
                            'borderWidth' => 2,
                        ],
                        [
                            'label' => __('Contact Messages'),
                            'data' => $trendMessages,
                            'borderColor' => '#0369a1',
                            'backgroundColor' => 'rgba(3, 105, 161, 0.12)',
                            'pointBackgroundColor' => '#0369a1',
                            'pointRadius' => 2.5,
                            'tension' => 0.35,
                            'fill' => true,
                            'borderWidth' => 2,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => [
                        'legend' => ['position' => 'top'],
                    ],
                    'scales' => [
                        'y' => ['beginAtZero' => true],
                    ],
                ],
            ],
        ];

        $contentSnapshot = [
            [
                'label' => __('Users'),
                'value' => User::query()->count(),
                'url' => route('admin.users'),
            ],
            [
                'label' => __('Categories'),
                'value' => Category::query()->count(),
                'url' => route('admin.categories'),
            ],
            [
                'label' => __('Pages'),
                'value' => InfoPage::query()->count(),
                'url' => route('admin.content.pages.index'),
            ],
            [
                'label' => __('Blog Posts'),
                'value' => BlogPost::query()->count(),
                'url' => route('admin.content.blog.index'),
            ],
            [
                'label' => __('FAQs'),
                'value' => Faq::query()->count(),
                'url' => route('admin.content.faqs.index'),
            ],
            [
                'label' => __('Content Blocks'),
                'value' => ContentBlock::query()->count(),
                'url' => route('admin.content.blocks'),
            ],
            [
                'label' => __('Slots'),
                'value' => ContentBlockSlot::query()->count(),
                'url' => route('admin.content.slots'),
            ],
            [
                'label' => __('Contact Messages'),
                'value' => ContactMessage::query()->count(),
                'url' => null,
            ],
        ];

        $featureFlags = [
            __('User Tracking') => $trackingEnabled,
            __('Mobile Front Variant') => (bool) config('catalog_features.flags.catalog_use_mobile_pwa', true),
            __('Blog') => true,
            __('Content Blocks') => true,
        ];

        return view('livewire.admin.dashboard.overview', [
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'kpis' => $kpis,
            'recentAdminActivity' => $recentAdminActivity,
            'recentContactMessages' => $recentContactMessages,
            'recentTrackingEvents' => $recentTrackingEvents,
            'trackingEnabled' => $trackingEnabled,
            'trendRows' => $trendRows,
            'catalogSnapshot' => $contentSnapshot,
            'featureFlags' => $featureFlags,
            'dashboardCharts' => $dashboardCharts,
        ]);
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: CarbonImmutable, 3: CarbonImmutable, 4: int}
     */
    private function resolveRangeWindow(): array
    {
        $days = (int) $this->rangeDays;
        if (! in_array($days, [1, 7, 30], true)) {
            $days = 7;
        }

        $end = CarbonImmutable::now()->endOfDay();
        $start = CarbonImmutable::now()->startOfDay()->subDays($days - 1);
        $previousEnd = $start->subSecond();
        $previousStart = $start->subDays($days);

        return [$start, $end, $previousStart, $previousEnd, $days];
    }

    /**
     * @return array{current: float, previous: float, delta: float, direction: string, percent: float|null}
     */
    private function formatDelta(float|int $current, float|int $previous): array
    {
        $current = (float) $current;
        $previous = (float) $previous;
        $delta = $current - $previous;
        $direction = $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat');

        $percent = null;
        if (abs($previous) > 0.00001) {
            $percent = ($delta / abs($previous)) * 100;
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'delta' => $delta,
            'direction' => $direction,
            'percent' => $percent,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildTrendRows(int $days): Collection
    {
        $end = CarbonImmutable::now()->endOfDay();
        $start = CarbonImmutable::now()->startOfDay()->subDays($days - 1);

        $map = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->addDays($i)->toDateString();
            $map[$date] = [
                'date' => $date,
                'users' => 0,
                'messages' => 0,
                'bar_width' => 0,
            ];
        }

        $userRows = User::query()->whereBetween('created_at', [$start, $end])->get(['created_at']);
        foreach ($userRows as $row) {
            $bucket = $row->created_at?->toDateString();
            if ($bucket && array_key_exists($bucket, $map)) {
                $map[$bucket]['users']++;
            }
        }

        $messageRows = ContactMessage::query()->whereBetween('created_at', [$start, $end])->get(['created_at']);
        foreach ($messageRows as $row) {
            $bucket = $row->created_at?->toDateString();
            if ($bucket && array_key_exists($bucket, $map)) {
                $map[$bucket]['messages']++;
            }
        }

        $maxUsers = max(1, (int) collect($map)->max('users'));
        foreach ($map as $key => $row) {
            $map[$key]['bar_width'] = (int) round(((int) $row['users'] / $maxUsers) * 100);
        }

        return collect(array_values($map));
    }
}
