<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('admin.messages.contact.manager.title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.messages.contact.manager.subtitle') }}</p>
                <a href="{{ route('contact.create') }}" target="_blank" rel="noreferrer" class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-amber-700 hover:text-amber-800 hover:underline">
                    <i class="fa-regular fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    <span>{{ __('admin.common.open_front_form') }}</span>
                </a>
                <p class="mt-2 text-xs text-slate-500">{{ __('admin.messages.contact.manager.items_per_page') }}: <span class="admin-chip">{{ $perPage }}</span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[48rem] items-end gap-3 md:grid-cols-[minmax(0,1fr)_12rem]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.search') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('admin.messages.contact.manager.search_placeholder') }}"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.state') }}</label>
                        <select wire:model.live="status" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.contact.manager.summary.all') }}</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($totals['all'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.contact.status.new') }}</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">{{ number_format((int) ($totals['new'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.contact.status.read') }}</p>
            <p class="mt-2 text-2xl font-semibold text-sky-700">{{ number_format((int) ($totals['read'] ?? 0)) }}</p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.messages.contact.status.resolved') }}</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format((int) ($totals['resolved'] ?? 0)) }}</p>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.contact.manager.table.contact') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.contact.manager.table.inquiry') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.messages.contact.manager.table.source') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.messages.contact.manager.table.state') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.messages.contact.manager.table.received') }}</th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $payload = (array) ($row->payload ?? []);
                            $sourceUrl = trim((string) ($payload['url'] ?? ''));
                            $sourcePage = trim((string) ($payload['source_page'] ?? ''));
                            $statusClasses = match ($row->status) {
                                'read' => 'bg-sky-100 text-sky-800',
                                'resolved' => 'bg-emerald-100 text-emerald-800',
                                default => 'bg-amber-100 text-amber-800',
                            };
                        @endphp
                        <tr class="{{ $row->status === 'new' ? 'bg-amber-50/40' : '' }}">
                            <td class="px-3 py-3 text-slate-800">
                                <div class="font-semibold text-slate-900">{{ $row->name }}</div>
                                <div class="mt-1 text-sm text-slate-600">
                                    <a href="mailto:{{ $row->email }}" class="hover:text-slate-900 hover:underline">{{ $row->email }}</a>
                                </div>
                                @if ($row->phone)
                                    <div class="mt-1 text-xs text-slate-500">{{ $row->phone }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900">{{ $row->subject }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ \Illuminate\Support\Str::limit((string) $row->message, 180) }}
                                </div>
                                @if (mb_strlen((string) $row->message) > 180)
                                    <details class="mt-2 text-left">
                                        <summary class="cursor-pointer text-xs font-semibold text-amber-700 hover:text-amber-800">
                                            {{ __('admin.common.show_full_text') }}
                                        </summary>
                                        <p class="mt-2 whitespace-pre-line break-words rounded-xl border border-slate-200 bg-white p-3 text-xs leading-5 text-slate-800">{{ $row->message }}</p>
                                    </details>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="text-xs text-slate-500">
                                    {{ __('admin.messages.contact.manager.labels.company') }}:
                                    {{ trim((string) ($payload['company'] ?? '')) !== '' ? $payload['company'] : __('admin.messages.contact.manager.not_provided') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.contact.manager.labels.locale') }}:
                                    {{ strtoupper((string) ($payload['locale'] ?? app()->getLocale())) }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ __('admin.messages.contact.manager.labels.source_page') }}:
                                    @if ($sourcePage !== '')
                                        <span>{{ $sourcePage }}</span>
                                    @elseif ($sourceUrl !== '')
                                        <a href="{{ $sourceUrl }}" target="_blank" rel="noreferrer" class="hover:text-slate-900 hover:underline">
                                            {{ \Illuminate\Support\Str::limit($sourceUrl, 70) }}
                                        </a>
                                    @else
                                        {{ __('admin.messages.contact.manager.not_provided') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ __('admin.messages.contact.status.'.$row->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-xs text-slate-600">
                                {{ $row->created_at?->format('Y-m-d H:i') ?? '-' }}
                                @if ($row->reviewed_at)
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        {{ __('admin.messages.contact.manager.reviewed_by', ['name' => $row->reviewer?->name ?: __('admin.layout.admin')]) }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">{{ $row->reviewed_at->format('Y-m-d H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="markAsNew({{ (int) $row->id }})"
                                        class="rounded-lg border border-amber-200 px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                                    >
                                        {{ __('admin.messages.contact.manager.actions.mark_new') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsRead({{ (int) $row->id }})"
                                        class="rounded-lg border border-sky-200 px-2 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-50"
                                    >
                                        {{ __('admin.messages.contact.manager.actions.mark_read') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsResolved({{ (int) $row->id }})"
                                        class="rounded-lg border border-emerald-200 px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                                    >
                                        {{ __('admin.messages.contact.manager.actions.mark_resolved') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ (int) $row->id }})"
                                        wire:confirm="{{ __('admin.messages.contact.manager.confirm_delete', ['name' => $row->name]) }}"
                                        class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        {{ __('admin.messages.contact.manager.actions.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('admin.messages.contact.manager.empty') }}</td>
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
