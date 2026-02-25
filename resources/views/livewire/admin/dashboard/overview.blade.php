@php
    $rangeOptions = [
        '1' => __('Today'),
        '7' => __('Last 7 Days'),
        '30' => __('Last 30 Days'),
    ];
@endphp

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Info Site Overview') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('Operational metrics for users, content, and inbound contact volume.') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('Window') }}: <span class="admin-chip">{{ $start->format('Y-m-d') }} - {{ $end->format('Y-m-d') }}</span></p>
            </div>
            <div class="w-56">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Range') }}</label>
                <select wire:model.live="rangeDays" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                    @foreach ($rangeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid gap-4" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
        @foreach ($kpis as $kpi)
            @php
                $delta = $kpi['delta'];
                $direction = $delta['direction'];
                $tone = $direction === 'up' ? 'text-emerald-700' : ($direction === 'down' ? 'text-rose-700' : 'text-slate-600');
            @endphp
            <div class="admin-panel admin-panel-soft p-4" style="grid-column: span 3;">
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

    <div class="grid gap-4" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
        <div class="admin-panel admin-panel-soft p-5" style="grid-column: span 7;">
            <h2 class="admin-section-title">{{ __('Users & Messages Trend') }}</h2>
            <div class="mt-4" style="height: 16rem;">
                <canvas
                    data-dashboard-chart
                    data-chart-key="users_contacts_trend"
                    data-chart-payload='@json($dashboardCharts["users_contacts_trend"])'
                ></canvas>
            </div>
        </div>

        <div class="admin-panel admin-panel-soft p-5" style="grid-column: span 5;">
            <h2 class="admin-section-title">{{ __('Feature Flags') }}</h2>
            <div class="mt-4 grid gap-2">
                @foreach ($featureFlags as $flag => $enabled)
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                        <span class="text-slate-700">{{ $flag }}</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $enabled ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ $enabled ? __('On') : __('Off') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title">{{ __('Daily New Users (:days Days)', ['days' => min($days, 30)]) }}</h2>
            <div class="mt-4 space-y-2">
                @foreach ($trendRows as $row)
                    <div class="grid items-center gap-3" style="grid-template-columns: 7rem minmax(0, 1fr) 4rem 4rem;">
                        <span class="text-xs text-slate-600">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M d') }}</span>
                        <div class="h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-cyan-600" style="width: {{ max(2, (int) $row['bar_width']) }}%;"></div>
                        </div>
                        <span class="text-xs text-slate-700 text-right">{{ $row['users'] }}</span>
                        <span class="text-xs text-slate-500 text-right">{{ $row['messages'] }}</span>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ __('Right columns: users / contact messages') }}</p>
        </div>

        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title">{{ __('Content & System Snapshot') }}</h2>
            <div class="mt-4 grid gap-2" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                @foreach ($catalogSnapshot as $item)
                    @if (!empty($item['url']))
                        <a href="{{ $item['url'] }}" class="rounded-xl border border-slate-200 bg-white p-3 hover:bg-slate-50">
                            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">{{ $item['label'] }}</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format((int) $item['value']) }}</p>
                        </a>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="text-xs uppercase tracking-[0.12em] text-slate-500">{{ $item['label'] }}</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format((int) $item['value']) }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title">{{ __('Recent Contact Messages') }}</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="admin-items-table min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left">{{ __('Time') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('Name') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('Email') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('Subject') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentContactMessages as $row)
                            <tr>
                                <td class="px-2 py-2">{{ $row->created_at?->format('m-d H:i') ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $row->name ?: '-' }}</td>
                                <td class="px-2 py-2">{{ $row->email ?: '-' }}</td>
                                <td class="px-2 py-2">{{ $row->subject ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-2 py-4 text-center text-slate-500">{{ __('No messages yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title">{{ __('Recent Admin Activity') }}</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="admin-items-table min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left">{{ __('Time') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('Event') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('Causer') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentAdminActivity as $activity)
                            <tr>
                                <td class="px-2 py-2">{{ $activity->created_at?->format('m-d H:i') ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $activity->event ?: $activity->description }}</td>
                                <td class="px-2 py-2">{{ $activity->causer?->name ?: __('System') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-2 py-4 text-center text-slate-500">{{ __('No admin activity.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($trackingEnabled)
        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title">{{ __('Recent Tracking Events') }}</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="admin-items-table min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left">{{ __('Time') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('Event') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('User') }}</th>
                            <th class="px-2 py-2 text-left">{{ __('URL') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTrackingEvents as $row)
                            <tr>
                                <td class="px-2 py-2">{{ optional($row->occurred_at)->format('m-d H:i') ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $row->event }}</td>
                                <td class="px-2 py-2">{{ $row->user?->name ?: '-' }}</td>
                                <td class="px-2 py-2">{{ $row->url ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-2 py-4 text-center text-slate-500">{{ __('No tracking events.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
