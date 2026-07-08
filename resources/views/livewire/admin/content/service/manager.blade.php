<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('admin.content.services.manager.title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('admin.content.services.manager.subtitle') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('admin.content.services.manager.items_per_page') }}: <span class="admin-chip">{{ $perPage }}</span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: minmax(26rem, 1fr) 8rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.search') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('admin.content.services.manager.search_placeholder') }}"
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
                <a href="{{ route('admin.content.services.create', ['locale' => $locale]) }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    {{ __('admin.common.create') }}
                </a>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('admin.common.items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.services.manager.table.service') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('admin.content.services.manager.table.slug') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.content.services.manager.table.template') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('admin.content.services.manager.table.state') }}</th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('admin.content.services.manager.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        @php
                            $tr = $row->translations->first();
                            $adminPage = (array) ($adminPageTree[$row->template_key] ?? []);
                            $childPages = (array) ($adminPage['children'] ?? []);
                            $editUrl = route('admin.content.services.edit', ['servicePage' => $row->id, 'locale' => $locale]);
                            $frontRoute = (string) ($adminPage['route'] ?? '');
                            $frontUrl = $frontRoute !== '' && \Illuminate\Support\Facades\Route::has($frontRoute) ? route($frontRoute) : '';
                            $isPrimaryService = in_array($row->template_key, $primaryServiceTemplateKeys ?? [], true);
                            $isServicesIndex = $row->template_key === \App\Support\Content\ServicePageTemplateRegistry::SERVICES_INDEX;
                        @endphp
                        <tr>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium">{{ $tr?->title ?? __('admin.content.services.manager.missing_title') }}</span>
                                    @if ($isPrimaryService)
                                        <span class="rounded-full bg-cyan-50 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-cyan-800">{{ __('Osnovna usluga') }}</span>
                                    @elseif ($isServicesIndex)
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-slate-600">{{ __('Landing') }}</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-slate-600">{{ __('Front stranica') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span>{{ $row->code }}</span>
                                    @if ($frontUrl !== '')
                                        <a href="{{ $frontUrl }}" target="_blank" rel="noopener" class="font-semibold text-cyan-700 hover:text-cyan-900">{{ __('Front') }}</a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700">{{ $tr?->slug ?? '-' }}</td>
                            <td class="px-3 py-2 text-center text-slate-700">{{ $templateLabels[$row->template_key] ?? $row->template_key }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $row->is_active ? __('admin.common.active') : __('admin.common.inactive') }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ $editUrl }}" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('admin.common.edit') }}
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete({{ (int) $row->id }})"
                                        wire:confirm="{{ __('admin.content.services.manager.confirm_delete', ['name' => $tr?->title ?? $row->code]) }}"
                                        class="rounded-lg border border-rose-300 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        {{ __('admin.common.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @foreach ($childPages as $childPage)
                            @php
                                $childRoute = (string) ($childPage['route'] ?? '');
                                $childFrontUrl = $childRoute !== '' && \Illuminate\Support\Facades\Route::has($childRoute) ? route($childRoute) : '';
                                $childAnchor = (string) ($childPage['admin_anchor'] ?? '');
                                $childTemplateKey = (string) ($childPage['template_key'] ?? '');
                                $childTargetRow = $childTemplateKey !== '' ? $servicePagesByTemplate->get($childTemplateKey) : $row;
                                $childEditUrl = $childTargetRow
                                    ? route('admin.content.services.edit', ['servicePage' => $childTargetRow->id, 'locale' => $locale]).$childAnchor
                                    : '#';
                                $grandchildPages = (array) ($childPage['children'] ?? []);
                            @endphp
                            <tr class="bg-slate-50/60">
                                <td class="px-3 py-2 text-slate-700">
                                    <div class="ml-4 border-l border-slate-200 pl-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">{{ __('Podstranica') }}</span>
                                            <span class="font-medium">{{ $childPage['title'] ?? '' }}</span>
                                        </div>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                            <span>{{ $tr?->title ?? ($templateLabels[$row->template_key] ?? $row->template_key) }}</span>
                                            @if ($childFrontUrl !== '')
                                                <a href="{{ $childFrontUrl }}" target="_blank" rel="noopener" class="font-semibold text-cyan-700 hover:text-cyan-900">{{ __('Front') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 font-mono text-xs text-slate-700">{{ $childFrontUrl !== '' ? parse_url($childFrontUrl, PHP_URL_PATH) : '-' }}</td>
                                <td class="px-3 py-2 text-center text-slate-700">
                                    {{ $childTemplateKey !== '' ? ($templateLabels[$childTemplateKey] ?? $childTemplateKey) : __('Pod') . ' ' . ($templateLabels[$row->template_key] ?? $row->template_key) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($childTargetRow?->is_active ?? $row->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ ($childTargetRow?->is_active ?? $row->is_active) ? __('admin.common.active') : __('admin.common.inactive') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ $childEditUrl }}" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                            {{ __('admin.common.edit') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($grandchildPages as $grandchildPage)
                                @php
                                    $grandchildRoute = (string) ($grandchildPage['route'] ?? '');
                                    $grandchildFrontUrl = $grandchildRoute !== '' && \Illuminate\Support\Facades\Route::has($grandchildRoute) ? route($grandchildRoute) : '';
                                    $grandchildAnchor = (string) ($grandchildPage['admin_anchor'] ?? '');
                                    $grandchildTemplateKey = (string) ($grandchildPage['template_key'] ?? '');
                                    $grandchildTargetRow = $grandchildTemplateKey !== '' ? $servicePagesByTemplate->get($grandchildTemplateKey) : $row;
                                    $grandchildEditUrl = $grandchildTargetRow
                                        ? route('admin.content.services.edit', ['servicePage' => $grandchildTargetRow->id, 'locale' => $locale]).$grandchildAnchor
                                        : '#';
                                @endphp
                                <tr class="bg-slate-50/80">
                                    <td class="px-3 py-2 text-slate-700">
                                        <div class="ml-10 border-l border-slate-200 pl-4">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">{{ __('Podstranica') }}</span>
                                                <span class="font-medium">{{ $grandchildPage['title'] ?? '' }}</span>
                                            </div>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                <span>{{ $childPage['title'] ?? ($tr?->title ?? '') }}</span>
                                                @if ($grandchildFrontUrl !== '')
                                                    <a href="{{ $grandchildFrontUrl }}" target="_blank" rel="noopener" class="font-semibold text-cyan-700 hover:text-cyan-900">{{ __('Front') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 font-mono text-xs text-slate-700">{{ $grandchildFrontUrl !== '' ? parse_url($grandchildFrontUrl, PHP_URL_PATH) : '-' }}</td>
                                    <td class="px-3 py-2 text-center text-slate-700">
                                        {{ $grandchildTemplateKey !== '' ? ($templateLabels[$grandchildTemplateKey] ?? $grandchildTemplateKey) : __('Pod') . ' ' . ($templateLabels[$row->template_key] ?? $row->template_key) }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($grandchildTargetRow?->is_active ?? $row->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                            {{ ($grandchildTargetRow?->is_active ?? $row->is_active) ? __('admin.common.active') : __('admin.common.inactive') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ $grandchildEditUrl }}" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                {{ __('admin.common.edit') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('admin.content.services.manager.empty') }}</td>
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
