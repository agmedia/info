<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('admin.content.job_openings.manager.title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.content.job_openings.manager.subtitle') }}</p>
                <p class="mt-2 text-xs text-slate-500">
                    {{ __('admin.content.job_openings.manager.items_per_page') }}:
                    <span class="admin-chip">{{ $perPage }}</span>
                </p>
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-end xl:max-w-4xl xl:justify-end">
                <div class="grid w-full gap-3 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label for="job-opening-search" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('admin.common.search') }}
                        </label>
                        <input
                            id="job-opening-search"
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('admin.content.job_openings.manager.search_placeholder') }}"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>

                    <div>
                        <label for="job-opening-manager-locale" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('admin.common.locale') }}
                        </label>
                        <select
                            id="job-opening-manager-locale"
                            wire:model.live="locale"
                            data-tom-select
                            data-tom-no-search="1"
                            class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase"
                        >
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($canCreate)
                    <a
                        href="{{ route('admin.content.job-openings.create', ['locale' => $locale]) }}"
                        class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800"
                    >
                        {{ __('admin.common.create') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.job_openings.manager.table.position') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.job_openings.manager.table.locations') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.content.job_openings.manager.table.published_at') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.common.state') }}</th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('admin.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $translation = $row->translations->first();
                            $isScheduled = $row->is_active && $row->published_at?->isFuture();
                        @endphp
                        <tr>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium">{{ $translation?->title ?? __('admin.content.job_openings.manager.missing_title') }}</div>
                                <div class="text-xs text-slate-500">{{ $row->code }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ $translation?->locations ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-center text-slate-700">
                                {{ $row->published_at?->copy()->setTimezone(config('admin_ui.timezone', 'Europe/Zagreb'))->format('d.m.Y. H:i') ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if (! $row->is_active)
                                    <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ __('admin.content.job_openings.manager.states.inactive') }}
                                    </span>
                                @elseif ($isScheduled)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                        {{ __('admin.content.job_openings.manager.states.scheduled') }}
                                    </span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                        {{ __('admin.content.job_openings.manager.states.published') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if ($translation || $canUpdate || $canDelete)
                                    <div class="inline-flex items-center gap-1">
                                        @if ($translation)
                                            <a
                                                href="{{ route('admin.content.job-openings.preview', ['jobOpening' => $row->id, 'locale' => $locale]) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="rounded-lg border border-cyan-300 px-2 py-1 text-xs font-semibold text-cyan-800 hover:bg-cyan-50"
                                            >
                                                {{ __('admin.content.job_openings.manager.preview') }}
                                            </a>
                                        @endif
                                        @if ($canUpdate)
                                            <a
                                                href="{{ route('admin.content.job-openings.edit', ['jobOpening' => $row->id, 'locale' => $locale]) }}"
                                                class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                            >
                                                {{ __('admin.common.edit') }}
                                            </a>
                                        @endif
                                        @if ($canDelete)
                                            <button
                                                type="button"
                                                wire:click="delete({{ (int) $row->id }})"
                                                wire:confirm="{{ __('admin.content.job_openings.manager.confirm_delete', ['name' => $translation?->title ?? $row->code]) }}"
                                                class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                            >
                                                {{ __('admin.common.delete') }}
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">
                                {{ __('admin.content.job_openings.manager.empty') }}
                            </td>
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
