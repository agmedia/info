@php
    $rangeOptions = [
        '1' => __('Danas'),
        '7' => __('Zadnjih 7 dana'),
        '30' => __('Zadnjih 30 dana'),
        '90' => __('Zadnjih 90 dana'),
    ];

    $formatDate = static function ($value): string {
        if (! $value) {
            return '—';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('d.m.Y.');
        } catch (\Throwable) {
            return '—';
        }
    };

    $formatValue = static function ($value, bool $percentage = false): string {
        if ($value === null || $value === '') {
            return '—';
        }

        if (! is_numeric($value)) {
            return (string) $value;
        }

        $number = (float) $value;
        if ($percentage) {
            $number = $number >= 0 && $number <= 1 ? $number * 100 : $number;

            return number_format($number, 1, ',', '.').'%';
        }

        $decimals = $number === floor($number) ? 0 : 1;

        return number_format($number, $decimals, ',', '.');
    };

    $analyticsData = (array) ($analytics ?? []);
    $analyticsAvailable = (bool) data_get($analyticsData, 'available', false);
    $analyticsReasonKey = (string) data_get($analyticsData, 'reason_key', '');
    $analyticsProvider = trim((string) (data_get($analyticsData, 'provider') ?? data_get($analyticsData, 'source') ?? 'Google Analytics 4'));
    $analyticsKpiSource = collect((array) data_get($analyticsData, 'kpis', []));
    $analyticsKpiDefinitions = [
        [
            'key' => 'visitors',
            'alternatives' => ['visitors', 'active_users', 'users'],
            'label' => __('Posjetitelji'),
            'icon' => 'fa-light fa-users',
            'tone' => 'bg-amber-50 text-amber-700 ring-amber-200',
        ],
        [
            'key' => 'sessions',
            'alternatives' => ['sessions'],
            'label' => __('Sesije'),
            'icon' => 'fa-light fa-window',
            'tone' => 'bg-sky-50 text-sky-700 ring-sky-200',
        ],
        [
            'key' => 'pageviews',
            'alternatives' => ['pageviews', 'page_views', 'views'],
            'label' => __('Pregledi stranica'),
            'icon' => 'fa-light fa-eye',
            'tone' => 'bg-violet-50 text-violet-700 ring-violet-200',
        ],
        [
            'key' => 'engagement',
            'alternatives' => ['engagement_rate', 'engagement'],
            'label' => __('Stopa angažmana'),
            'icon' => 'fa-light fa-chart-user',
            'tone' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'percentage' => true,
        ],
    ];
    $analyticsKpis = collect($analyticsKpiDefinitions)->map(function (array $definition, int $index) use ($analyticsKpiSource, $analyticsData): array {
        $provided = $analyticsKpiSource->get($definition['key'], $analyticsKpiSource->get($index));
        $providedRow = is_array($provided) || is_object($provided) ? (array) $provided : [];
        $value = $providedRow !== [] ? data_get($providedRow, 'value') : $provided;

        if ($value === null || $value === '') {
            foreach ($definition['alternatives'] as $path) {
                $candidate = data_get($analyticsData, $path);
                if ($candidate === null || $candidate === '') {
                    $candidate = data_get($analyticsData, "totals.{$path}");
                }

                if ($candidate !== null && $candidate !== '') {
                    $value = is_array($candidate) ? data_get($candidate, 'value') : $candidate;
                    break;
                }
            }
        }

        return [
            ...$definition,
            'label' => data_get($providedRow, 'label') ?? $definition['label'],
            'value' => $value,
            'note' => data_get($providedRow, 'note') ?? data_get($providedRow, 'comparison'),
        ];
    });

    $analyticsChart = (array) (data_get($analyticsData, 'chart') ?? data_get($analyticsData, 'charts.trend') ?? []);
    $hasAnalyticsChart = $analyticsChart !== []
        && collect((array) data_get($analyticsChart, 'data.datasets', []))->isNotEmpty();

    $normalizeRankedRows = static function ($rows, string $fallbackLabel): \Illuminate\Support\Collection {
        return collect($rows ?? [])->map(function ($item, $key) use ($fallbackLabel): array {
            if (is_array($item) || is_object($item)) {
                $row = (array) $item;

                return [
                    'label' => data_get($row, 'label')
                        ?? data_get($row, 'name')
                        ?? data_get($row, 'title')
                        ?? data_get($row, 'source')
                        ?? data_get($row, 'path')
                        ?? data_get($row, 'device')
                        ?? data_get($row, 'location')
                        ?? $fallbackLabel,
                    'detail' => data_get($row, 'detail')
                        ?? data_get($row, 'subtitle')
                        ?? data_get($row, 'medium'),
                    'count' => data_get($row, 'count')
                        ?? data_get($row, 'value')
                        ?? data_get($row, 'page_views')
                        ?? data_get($row, 'sessions')
                        ?? data_get($row, 'users'),
                ];
            }

            return [
                'label' => is_string($key) ? $key : $fallbackLabel,
                'detail' => null,
                'count' => $item,
            ];
        })->values()->take(8);
    };

    $analyticsLists = [
        [
            'id' => 'analytics-pages-title',
            'title' => __('Najposjećenije stranice'),
            'subtitle' => __('Broj pregleda po stranici'),
            'icon' => 'fa-light fa-browser',
            'items' => $normalizeRankedRows(data_get($analyticsData, 'top_pages', []), __('Stranica')),
        ],
        [
            'id' => 'analytics-sources-title',
            'title' => __('Izvori prometa'),
            'subtitle' => __('Odakle posjetitelji dolaze'),
            'icon' => 'fa-light fa-share-nodes',
            'items' => $normalizeRankedRows(data_get($analyticsData, 'top_sources', []), __('Izvor')),
        ],
        [
            'id' => 'analytics-devices-title',
            'title' => __('Uređaji'),
            'subtitle' => __('Sesije prema vrsti uređaja'),
            'icon' => 'fa-light fa-laptop-mobile',
            'items' => $normalizeRankedRows(data_get($analyticsData, 'devices', []), __('Uređaj')),
        ],
        [
            'id' => 'analytics-locations-title',
            'title' => __('Lokacije'),
            'subtitle' => __('Gradovi i države publike'),
            'icon' => 'fa-light fa-location-dot',
            'items' => $normalizeRankedRows(data_get($analyticsData, 'locations', []), __('Lokacija')),
        ],
    ];

    $canConfigureAnalytics = auth()->check()
        && \Illuminate\Support\Facades\Route::has('admin.settings.system.store-settings');
@endphp

<div class="space-y-5 sm:space-y-6" wire:init="loadAnalytics">
    <section class="admin-panel overflow-hidden bg-gradient-to-br from-white via-white to-amber-50/60">
        <div class="h-1 bg-gradient-to-r from-amber-500 via-amber-300 to-transparent"></div>
        <div class="p-5 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                    <i class="fa-light fa-chart-network" aria-hidden="true"></i>
                    <span>{{ __('GA4 analitika') }}</span>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">{{ __('Pregled posjećenosti') }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    {{ __('Jasan pregled publike, ponašanja i najuspješnijeg sadržaja na web stranici.') }}
                </p>
                <div class="mt-4 inline-flex flex-wrap items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-600 shadow-sm">
                    <i class="fa-light fa-calendar-range text-amber-600" aria-hidden="true"></i>
                    <span>{{ $formatDate($start ?? null) }}</span>
                    <i class="fa-light fa-arrow-right text-slate-400" aria-hidden="true"></i>
                    <span>{{ $formatDate($end ?? null) }}</span>
                </div>
                </div>

                <div class="w-full lg:w-64">
                    <label for="dashboard-range" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('Razdoblje') }}
                    </label>
                    <div class="relative">
                        <i class="fa-light fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i>
                        <select id="dashboard-range" wire:model.live="rangeDays" wire:loading.attr="disabled" wire:target="loadAnalytics,rangeDays,reloadAnalytics" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-800 disabled:cursor-wait disabled:opacity-60">
                            @foreach ($rangeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p wire:loading.flex wire:target="loadAnalytics,rangeDays,reloadAnalytics" class="mt-2 items-center gap-2 text-xs font-medium text-slate-500" role="status">
                        <i class="fa-light fa-spinner-third animate-spin text-amber-700" aria-hidden="true"></i>
                        {{ __('dashboard.analytics.loading.refreshing') }}
                    </p>
                </div>
            </div>

            <nav class="mt-5 grid gap-2 border-t border-slate-200 pt-5 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Brze akcije') }}">
                <a href="{{ route('admin.content.blog.create') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-amber-300 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:justify-start">
                    <i class="fa-light fa-pen-to-square text-amber-700" aria-hidden="true"></i>
                    <span>{{ __('Novi blog post') }}</span>
                </a>
                <a href="{{ route('admin.content.blog.index') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-amber-300 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:justify-start">
                    <i class="fa-light fa-newspaper text-amber-700" aria-hidden="true"></i>
                    <span>{{ __('Blog objave') }}</span>
                </a>
                <a href="{{ route('admin.settings.system.store-settings') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-amber-300 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:justify-start">
                    <i class="fa-light fa-sliders text-amber-700" aria-hidden="true"></i>
                    <span>{{ __('Postavke stranice') }}</span>
                </a>
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-amber-300 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:justify-start">
                    <i class="fa-light fa-arrow-up-right-from-square text-amber-700" aria-hidden="true"></i>
                    <span>{{ __('Otvori web') }}</span>
                </a>
            </nav>
        </div>
    </section>

    @if (! $analyticsLoaded)
        <section class="admin-panel admin-panel-soft p-6 sm:p-8" data-dashboard-analytics-loading role="status" aria-live="polite">
            <div class="flex min-h-64 flex-col items-center justify-center text-center">
                <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                    <i class="fa-light fa-spinner-third animate-spin text-xl" aria-hidden="true"></i>
                </span>
                <h2 class="mt-5 text-lg font-semibold text-slate-950">{{ __('dashboard.analytics.loading.title') }}</h2>
                <p class="mt-2 max-w-lg text-sm leading-6 text-slate-600">{{ __('dashboard.analytics.loading.description') }}</p>
            </div>
        </section>
    @elseif ($analyticsAvailable)
        <section aria-labelledby="analytics-kpis-title">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 id="analytics-kpis-title" class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Ključni pokazatelji') }}</h2>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ $analyticsProvider !== '' ? $analyticsProvider : __('Aktivno') }}
                </span>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($analyticsKpis as $metric)
                    <article class="admin-panel admin-panel-soft group p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $metric['label'] }}</p>
                                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">
                                    {{ $formatValue($metric['value'], (bool) ($metric['percentage'] ?? false)) }}
                                </p>
                            </div>
                            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl ring-1 {{ $metric['tone'] }}">
                                <i class="{{ $metric['icon'] }} text-lg" aria-hidden="true"></i>
                            </span>
                        </div>
                        <p class="mt-4 min-h-5 text-xs leading-5 text-slate-500">
                            {{ $metric['note'] ?: __('Za odabrano razdoblje') }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="admin-panel admin-panel-soft p-5 sm:p-6" aria-labelledby="analytics-trend-title">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-white">
                        <i class="fa-light fa-chart-line" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2 id="analytics-trend-title" class="text-base font-semibold text-slate-950">{{ __('Posjećenost kroz vrijeme') }}</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Dnevni trend posjetitelja i sesija') }}</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">
                    {{ trans_choice(':count dan|:count dana', (int) ($days ?? 7), ['count' => (int) ($days ?? 7)]) }}
                </span>
            </div>

            @if ($hasAnalyticsChart)
                <div class="mt-6 h-72 sm:h-80">
                    <canvas
                        data-dashboard-chart
                        data-chart-key="analytics_trend"
                        data-chart-payload='@json($analyticsChart)'
                        aria-label="{{ __('Graf posjećenosti kroz vrijeme') }}"
                        role="img"
                    ></canvas>
                    <p data-dashboard-chart-error class="mt-4 hidden text-center text-sm font-medium text-rose-700" role="alert">
                        {{ __('dashboard.analytics.chart.render_error') }}
                    </p>
                </div>
            @else
                <div class="mt-6 flex min-h-64 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-6 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm ring-1 ring-slate-200">
                        <i class="fa-light fa-chart-line-down" aria-hidden="true"></i>
                    </span>
                    <p class="mt-4 text-sm font-semibold text-slate-800">{{ __('Nema podataka za trend') }}</p>
                    <p class="mt-1 max-w-md text-xs leading-5 text-slate-500">{{ __('Google Analytics nije vratio dnevne podatke za odabrano razdoblje.') }}</p>
                </div>
            @endif
        </section>

        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($analyticsLists as $list)
                <section class="admin-panel admin-panel-soft overflow-hidden" aria-labelledby="{{ $list['id'] }}">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-5 sm:px-6">
                        <div>
                            <h2 id="{{ $list['id'] }}" class="text-base font-semibold text-slate-950">{{ $list['title'] }}</h2>
                            <p class="mt-1 text-xs text-slate-500">{{ $list['subtitle'] }}</p>
                        </div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                            <i class="{{ $list['icon'] }}" aria-hidden="true"></i>
                        </span>
                    </div>

                    @if ($list['items']->isNotEmpty())
                        <ol class="divide-y divide-slate-100 px-5 sm:px-6">
                            @foreach ($list['items'] as $index => $item)
                                <li class="flex items-center gap-3 py-3.5">
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">{{ $index + 1 }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-slate-800">{{ data_get($item, 'label', '—') }}</p>
                                        @if (data_get($item, 'detail'))
                                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ data_get($item, 'detail') }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $formatValue(data_get($item, 'count')) }}
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="flex min-h-48 flex-col items-center justify-center px-6 py-10 text-center">
                            <i class="{{ $list['icon'] }} text-2xl text-slate-300" aria-hidden="true"></i>
                            <p class="mt-3 text-sm text-slate-500">{{ __('Nema podataka za odabrano razdoblje.') }}</p>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    @else
        <section class="admin-panel admin-panel-soft overflow-hidden" aria-labelledby="analytics-empty-title">
            <div class="p-5 sm:p-6">
                <div class="relative overflow-hidden rounded-3xl border border-dashed border-amber-300 bg-gradient-to-br from-amber-50/80 via-white to-white px-6 py-10 sm:px-10 sm:py-12">
                    <div class="relative mx-auto max-w-2xl text-center">
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-amber-700 shadow-sm ring-1 ring-amber-200">
                            <i class="fa-light fa-chart-line-up text-xl" aria-hidden="true"></i>
                        </span>
                        <h2 id="analytics-empty-title" class="mt-5 text-lg font-semibold text-slate-950">
                            {{ $analyticsReasonKey === 'report_request_failed'
                                ? __('dashboard.analytics.loading.error_title')
                                : __('Analitika posjeta još nije povezana') }}
                        </h2>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">
                            {{ data_get($analyticsData, 'reason') ?: __('Trenutno ne prikazujemo posjetitelje, uređaje ni lokacije jer nemamo pouzdan izvor podataka. Na taj način izbjegavamo prikaz lažnih nula.') }}
                        </p>

                        <div class="mt-6 flex flex-col justify-center gap-2 sm:flex-row" x-data>
                            @if ($analyticsReasonKey === 'report_request_failed')
                                <button type="button" wire:click="reloadAnalytics" wire:loading.attr="disabled" wire:target="reloadAnalytics" class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60">
                                    <i class="fa-light fa-rotate" wire:loading.class="animate-spin" wire:target="reloadAnalytics" aria-hidden="true"></i>
                                    {{ __('dashboard.analytics.loading.retry') }}
                                </button>
                            @endif
                            <button type="button" x-on:click="$dispatch('open-modal', 'ga4-setup-instructions')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                                <i class="fa-light fa-list-check" aria-hidden="true"></i>
                                {{ __('Kako povezati GA4') }}
                            </button>
                            @if ($canConfigureAnalytics)
                                <a href="{{ route('admin.settings.system.store-settings') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-amber-300 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                                <i class="fa-light fa-sliders" aria-hidden="true"></i>
                                {{ __('Otvori postavke analitike') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <x-modal name="ga4-setup-instructions" maxWidth="2xl" focusable>
            <section role="dialog" aria-modal="true" aria-labelledby="ga4-setup-title" class="max-h-[85vh] overflow-y-auto">
                <header class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 sm:px-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-amber-700">{{ __('Google Analytics 4') }}</p>
                        <h2 id="ga4-setup-title" class="mt-1 text-lg font-semibold text-slate-950">{{ __('Kako povezati GA4 dashboard') }}</h2>
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ __('Jednokratno povežite mjerenje javne stranice i siguran pristup izvještajima.') }}</p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close-modal', 'ga4-setup-instructions')" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="{{ __('Zatvori upute') }}">
                        <i class="fa-light fa-xmark" aria-hidden="true"></i>
                    </button>
                </header>

                <ol class="space-y-4 px-5 py-5 text-sm leading-6 text-slate-600 sm:px-6">
                    <li class="flex gap-3 rounded-2xl border border-slate-200 p-4">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-50 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">1</span>
                        <div>
                            <strong class="font-semibold text-slate-900">{{ __('Uključite mjerenje stranice') }}</strong>
                            <p class="mt-1">
                                {{ __('U Postavke stranice → Integracije uključite GA4 i kao Measurement ID unesite') }}
                                <code class="rounded bg-slate-100 px-1.5 py-0.5 font-semibold text-slate-900">G-YCD72KQJTC</code>.
                                {{ __('Nemojte unositi GTM-P898Q4XG jer je to Tag Manager container ID.') }}
                            </p>
                        </div>
                    </li>
                    <li class="flex gap-3 rounded-2xl border border-slate-200 p-4">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-50 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">2</span>
                        <div>
                            <strong class="font-semibold text-slate-900">{{ __('Napravite service account') }}</strong>
                            <p class="mt-1">{{ __('U Google Cloudu uključite Google Analytics Data API, napravite service account i preuzmite njegov JSON ključ.') }}</p>
                            <a href="https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1.5 font-semibold text-amber-800 underline decoration-amber-300 underline-offset-2 hover:text-amber-950">
                                {{ __('Otvori Google Cloud') }}
                                <i class="fa-light fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                    <li class="flex gap-3 rounded-2xl border border-slate-200 p-4">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-50 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">3</span>
                        <div>
                            <strong class="font-semibold text-slate-900">{{ __('Dajte pristup GA4 propertyju') }}</strong>
                            <p class="mt-1">{{ __('U Google Analyticsu otvorite Admin → Property access management, dodajte e-mail service accounta i dodijelite mu ulogu Viewer.') }}</p>
                            <a href="https://support.google.com/analytics/answer/9305788" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1.5 font-semibold text-amber-800 underline decoration-amber-300 underline-offset-2 hover:text-amber-950">
                                {{ __('Googleove upute') }}
                                <i class="fa-light fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                    <li class="flex gap-3 rounded-2xl border border-slate-200 p-4">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-50 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">4</span>
                        <div class="min-w-0 flex-1">
                            <strong class="font-semibold text-slate-900">{{ __('Postavite server varijable') }}</strong>
                            <div class="mt-2 space-y-1 rounded-xl bg-slate-950 p-3 font-mono text-[0.68rem] leading-5 text-slate-100">
                                <code class="block break-all">GA4_PROPERTY_ID=123456789</code>
                                <code class="block break-all">GA4_SERVICE_ACCOUNT_CREDENTIALS_PATH=/secure/path/ga4-service-account.json</code>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ __('Property ID je brojčani ID iz GA4. JSON ključ ne stavljajte u Git, javni direktorij ili sadržaj admina.') }}</p>
                        </div>
                    </li>
                </ol>

                <footer class="sticky bottom-0 flex flex-col-reverse gap-2 border-t border-slate-200 bg-white px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" x-on:click="$dispatch('close-modal', 'ga4-setup-instructions')" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        {{ __('Zatvori') }}
                    </button>
                    @if ($canConfigureAnalytics)
                        <a href="{{ route('admin.settings.system.store-settings') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400">
                            <i class="fa-light fa-sliders" aria-hidden="true"></i>
                            {{ __('Otvori postavke analitike') }}
                        </a>
                    @endif
                </footer>
            </section>
        </x-modal>
    @endif
</div>
