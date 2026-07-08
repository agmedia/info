@php
    $rangeOptions = [
        '1' => __('Today'),
        '7' => __('Last 7 Days'),
        '30' => __('Last 30 Days'),
    ];
@endphp

<div class="space-y-4 sm:space-y-6">
    <div class="admin-panel admin-search-panel p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Info Site Overview') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('Operational metrics for users, content, and inbound contact volume.') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('Window') }}: <span class="admin-chip">{{ $start->format('Y-m-d') }} - {{ $end->format('Y-m-d') }}</span></p>
            </div>
            <div class="w-full sm:w-56">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Range') }}</label>
                <select wire:model.live="rangeDays" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                    @foreach ($rangeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            @php
                $delta = $kpi['delta'];
                $direction = $delta['direction'];
                $tone = $direction === 'up' ? 'text-emerald-700' : ($direction === 'down' ? 'text-rose-700' : 'text-slate-600');
            @endphp
            <div class="admin-panel admin-panel-soft p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $kpi['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $kpi['value'] }}</p>
                <p class="mt-2 text-xs {{ $tone }}">
                    @if ($direction === 'up') + @endif{{ number_format($delta['delta'], 2) }}
                    @if ($delta['percent'] !== null)
                        ({{ $delta['percent'] >= 0 ? '+' : '' }}{{ number_format($delta['percent'], 1) }}%)
                    @endif
                </p>
            </div>
        @endforeach
    </div>

    <div class="admin-panel admin-panel-soft p-4 sm:p-5">
        <h2 class="admin-section-title">{{ __('Users & Messages Trend') }}</h2>
        <div class="mt-4 h-64 sm:h-72">
            <canvas
                data-dashboard-chart
                data-chart-key="users_contacts_trend"
                data-chart-payload='@json($dashboardCharts["users_contacts_trend"])'
            ></canvas>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-4 sm:p-5">
        <h2 class="admin-section-title">{{ __('Daily New Users (:days Days)', ['days' => min($days, 30)]) }}</h2>
        <div class="mt-4 space-y-2">
            @foreach ($trendRows as $row)
                <div class="grid items-center gap-2 sm:gap-3" style="grid-template-columns: minmax(4.75rem, 6rem) minmax(0, 1fr) 2.5rem 2.5rem;">
                    <span class="text-xs text-slate-600">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M d') }}</span>
                    <div class="h-2 rounded-full bg-slate-200">
                        <div class="h-2 rounded-full bg-cyan-600" style="width: {{ max(2, (int) $row['bar_width']) }}%;"></div>
                    </div>
                    <span class="text-right text-xs text-slate-700">{{ $row['users'] }}</span>
                    <span class="text-right text-xs text-slate-500">{{ $row['messages'] }}</span>
                </div>
            @endforeach
        </div>
        <p class="mt-3 text-xs text-slate-500">{{ __('Right columns: users / contact messages') }}</p>
    </div>
</div>
