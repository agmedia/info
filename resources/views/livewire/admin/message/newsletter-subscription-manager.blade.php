<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('admin.messages.newsletter.manager.title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.messages.newsletter.manager.subtitle') }}</p>
                <p class="mt-2 text-xs text-slate-500">
                    {{ __('admin.messages.newsletter.manager.items_per_page') }}:
                    <span class="admin-chip">{{ $perPage }}</span>
                </p>
            </div>

            <div class="grid w-full gap-3 sm:grid-cols-2 xl:max-w-[58rem] xl:grid-cols-[minmax(16rem,1fr)_13rem_10rem]">
                <div class="sm:col-span-2 xl:col-span-1">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.common.search') }}
                    </label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('admin.messages.newsletter.manager.search_placeholder') }}"
                        class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.common.state') }}
                    </label>
                    <select wire:model.live="status" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.common.locale') }}
                    </label>
                    <select wire:model.live="locale" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                        @foreach ($localeOptions as $value)
                            <option value="{{ $value }}">{{ $value === 'all' ? __('admin.common.all') : strtoupper($value) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.newsletter.manager.summary.all') }}</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($totals['all'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.newsletter.manager.summary.received') }}</p>
            <p class="mt-2 text-2xl font-semibold text-cyan-700">{{ number_format((int) ($totals['received'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.newsletter.manager.summary.awaiting_confirmation') }}</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format((int) ($totals['awaiting_confirmation'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.newsletter.status.subscribed') }}</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format((int) ($totals['subscribed'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.newsletter.status.failed') }}</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700">{{ number_format((int) ($totals['failed'] ?? 0)) }}</p>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.newsletter.manager.table.subscriber') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.messages.newsletter.manager.table.status') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.newsletter.manager.table.source') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.messages.newsletter.manager.table.activity') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.newsletter.manager.table.error') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $statusClasses = match ($row->status) {
                                \App\Models\Content\Support\NewsletterSubscription::STATUS_RECEIVED => 'bg-cyan-100 text-cyan-800',
                                \App\Models\Content\Support\NewsletterSubscription::STATUS_SUBSCRIBED => 'bg-emerald-100 text-emerald-800',
                                \App\Models\Content\Support\NewsletterSubscription::STATUS_FAILED => 'bg-rose-100 text-rose-800',
                                default => 'bg-amber-100 text-amber-800',
                            };
                            $source = trim((string) data_get($row->payload, 'source', ''));
                            $providerKey = 'admin.messages.newsletter.provider.'.(string) $row->provider;
                            $providerLabel = __($providerKey);
                            if ($providerLabel === $providerKey) {
                                $providerLabel = ucfirst((string) $row->provider);
                            }
                        @endphp
                        <tr class="{{ $row->status === \App\Models\Content\Support\NewsletterSubscription::STATUS_FAILED ? 'bg-rose-50/30' : '' }}">
                            <td class="px-3 py-3 text-slate-800">
                                <a href="mailto:{{ $row->email }}" class="font-semibold text-slate-900 hover:underline">{{ $row->email }}</a>
                                @if ($row->provider_member_id)
                                    <div class="mt-1 break-all text-[11px] text-slate-500">
                                        {{ __('admin.messages.newsletter.manager.labels.member_id') }}: {{ $row->provider_member_id }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ __('admin.messages.newsletter.status.'.$row->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900">{{ strtoupper((string) $row->locale) }} · {{ $providerLabel }}</div>
                                @if ($source !== '')
                                    <div class="mt-1 text-xs text-slate-500">{{ __('admin.messages.newsletter.manager.labels.source') }}: {{ $source }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center text-xs text-slate-600">
                                <div>{{ __('admin.messages.newsletter.manager.labels.attempts') }}: {{ number_format((int) $row->attempts) }}</div>
                                <div class="mt-1">{{ __('admin.messages.newsletter.manager.labels.last_attempt') }}: {{ $row->last_attempt_at?->format('Y-m-d H:i') ?? '-' }}</div>
                                @if ($row->subscribed_at)
                                    <div class="mt-1 text-emerald-700">{{ __('admin.messages.newsletter.manager.labels.subscribed_at') }}: {{ $row->subscribed_at->format('Y-m-d H:i') }}</div>
                                @endif
                            </td>
                            <td class="max-w-sm px-3 py-3 text-slate-700">
                                @if ($row->error_code || $row->error_message)
                                    @if ($row->error_code)
                                        <div class="font-medium text-rose-800">{{ $row->error_code }}</div>
                                    @endif
                                    @if ($row->error_message)
                                        <div class="mt-1 break-words text-xs leading-5 text-slate-600">{{ \Illuminate\Support\Str::limit((string) $row->error_message, 240) }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('admin.messages.newsletter.manager.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>
</div>
