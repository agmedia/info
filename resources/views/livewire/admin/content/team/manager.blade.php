<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('admin.content.team.manager.title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.content.team.manager.subtitle') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('admin.content.team.manager.items_per_page') }}: <span class="admin-chip">{{ $perPage }}</span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: minmax(26rem, 1fr) 8rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.search') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('admin.content.team.manager.search_placeholder') }}"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.locale') }}</label>
                        <select wire:model.live="locale" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <a href="{{ route('admin.content.team.create', ['locale' => $locale]) }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    {{ __('admin.common.create') }}
                </a>
            </div>
        </div>
    </div>

    <form wire:submit="savePageSettings" class="admin-panel admin-form-panel p-6">
        <div class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">{{ __('admin.content.team.manager.page_settings.eyebrow') }}</p>
                <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('admin.content.team.manager.page_settings.title') }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.content.team.manager.page_settings.subtitle', ['locale' => $locale]) }}</p>
            </div>
            <span class="admin-chip lowercase">{{ $locale }}</span>
        </div>

        <div class="mt-5">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                {{ __('admin.content.team.manager.page_settings.fields.intro') }}
            </label>
            <textarea rows="3" wire:model="pageSettings.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.team.manager.page_settings.intro_hint') }}</p>
            @error('pageSettings.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                    {{ __('admin.content.team.manager.page_settings.fields.meta_title') }}
                </label>
                <input type="text" wire:model="pageSettings.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                @error('pageSettings.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                    {{ __('admin.content.team.manager.page_settings.fields.meta_description') }}
                </label>
                <textarea rows="3" wire:model="pageSettings.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                @error('pageSettings.meta_description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ __('admin.content.team.manager.page_settings.save') }}
            </button>
        </div>
    </form>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.team.manager.table.preview') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.team.manager.table.member') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.team.manager.table.departments') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.team.manager.table.contact') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.content.team.manager.table.sort') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.content.team.manager.table.state') }}</th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('admin.content.team.manager.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $tr = $row->translations->first();
                            $photo = $row->media->firstWhere('collection_name', 'team_photo');
                            $photoUrl = $photo
                                ? ($photo->hasGeneratedConversion('thumb_100x100') ? $photo->getUrl('thumb_100x100') : $photo->getUrl())
                                : null;
                        @endphp
                        <tr>
                            <td class="px-3 py-2 align-top">
                                @if ($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="" class="h-16 w-16 rounded-lg border border-slate-200 bg-slate-100 object-cover" />
                                @else
                                    <div class="flex h-16 w-16 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                        {{ __('admin.common.no_image') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium">{{ $tr?->name ?? __('admin.content.team.manager.missing_name') }}</div>
                                @if (filled($tr?->position))
                                    <div class="text-xs text-slate-500">{{ $tr?->position }}</div>
                                @endif
                                <div class="mt-1 text-xs text-slate-500">{{ $row->code }}</div>
                            </td>
                            <td class="px-3 py-2 text-sm text-slate-700">
                                {{ $tr?->departments ?: '-' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-slate-700">
                                <div>{{ $row->email ?: '-' }}</div>
                                @if ($row->mobile_phone)
                                    <div class="mt-1 text-xs text-slate-500">{{ $row->mobile_phone }}</div>
                                @endif
                                <div class="mt-1 flex flex-wrap gap-1 text-[11px]">
                                    @if ($row->facebook_url)
                                        <span class="admin-chip">Facebook</span>
                                    @endif
                                    @if ($row->twitter_url)
                                        <span class="admin-chip">X</span>
                                    @endif
                                    @if ($row->linkedin_url)
                                        <span class="admin-chip">LinkedIn</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-700">{{ $row->sort_order }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $row->is_active ? __('admin.common.active') : __('admin.common.inactive') }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.content.team.edit', ['member' => $row->id, 'locale' => $locale]) }}" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('admin.common.edit') }}
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $row->id }})"
                                        wire:confirm="{{ __('admin.content.team.manager.confirm_delete', ['name' => $tr?->name ?? $row->code]) }}"
                                        class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        {{ __('admin.common.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('admin.content.team.manager.empty') }}</td>
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
