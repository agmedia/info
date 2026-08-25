<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">{{ __('Comments Moderation') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('Create homepage testimonials and moderate any existing comments stored in the system.') }}</p>
                <p class="mt-2 text-xs text-slate-500">{{ __('Items per page') }}: <span class="admin-chip">{{ $perPage }}</span></p>
            </div>

            <div class="flex w-[74rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[66rem] items-end gap-3" style="grid-template-columns: minmax(20rem, 1.4fr) 9rem 10rem 8rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Search') }}</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Body, author, company...') }}" class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Status') }}</label>
                        <select wire:model.live="status" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            @foreach ($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Target') }}</label>
                        <select wire:model.live="target" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            @foreach ($targetOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Locale') }}</label>
                        <select wire:model.live="locale" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase">
                            <option value="all">{{ __('all') }}</option>
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}" @selected(($form['locale'] ?? '') === $localeOption)>{{ $localeOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="button" wire:click="toggleCreateForm" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
            {{ $showCreateForm ? __('Close Form') : __('Add Comment') }}
        </button>
    </div>

    @if ($showCreateForm)
        <div class="admin-panel admin-form-panel p-6">
            <h2 class="admin-section-title">{{ $editingCommentId ? __('Edit Comment') : __('Add Comment') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('Use this form for homepage testimonials or comments tied to a specific page, blog post or FAQ.') }}</p>

            <form wire:submit="createComment" class="mt-4 space-y-4">
                <div class="grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Locale') }}</label>
                        <select wire:model="form.locale" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
                            @endforeach
                        </select>
                        @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Target Type') }}</label>
                        <select wire:model.live="form.target_type" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            @foreach ($formTargetOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.target_type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 7;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Target Item') }}</label>
                        <select
                            wire:model="form.target_id"
                            data-tom-select
                            class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm"
                            @if (($form['target_type'] ?? 'detached') === 'detached') disabled @endif
                        >
                            <option value="">{{ ($form['target_type'] ?? 'detached') === 'detached' ? __('Homepage testimonial has no linked item') : __('Select item') }}</option>
                            @foreach ($targetRecordOptions as $option)
                                <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        @error('form.target_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 4;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Author') }}</label>
                        <input type="text" wire:model="form.author_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.author_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 4;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company') }}</label>
                        <input type="text" wire:model="form.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.company') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Rating') }}</label>
                        <select wire:model="form.rating" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }}/5</option>
                            @endfor
                        </select>
                        @error('form.rating') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 12;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Comment') }}</label>
                        <textarea wire:model="form.body" rows="5" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        @error('form.body') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: span 12;">
                        <button
                            type="button"
                            wire:click="$toggle('form.is_featured')"
                            class="admin-switch"
                            data-state="{{ $form['is_featured'] ? 'on' : 'off' }}"
                            role="switch"
                            aria-checked="{{ $form['is_featured'] ? 'true' : 'false' }}"
                        >
                            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                            <span class="admin-switch-label">{{ $form['is_featured'] ? __('Featured') : __('Not featured') }}</span>
                        </button>
                        @error('form.is_featured') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                        {{ $editingCommentId ? __('Save Changes') : __('Save Comment') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title">{{ __('Items') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('Comment') }}</th>
                        <th class="px-3 py-2 text-left font-semibold">{{ __('Target') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('Rating') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('Status') }}</th>
                        <th class="px-3 py-2 text-center font-semibold">{{ __('Created') }}</th>
                        <th class="px-3 py-2 text-right font-semibold">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        <tr class="{{ $row->trashed() ? 'bg-slate-50/70' : '' }}">
                            <td class="px-3 py-2 text-slate-800">
                                <div class="line-clamp-3">{{ $row->body }}</div>
                                <div class="mt-3">
                                    <div class="text-base font-semibold text-slate-900">
                                        {{ $row->author_name ?: ($row->user?->name ?? __('Anonymous')) }}
                                    </div>
                                    @if ($row->company_label !== '')
                                        <div class="text-sm text-slate-500">{{ $row->company_label }}</div>
                                    @endif
                                    @if ($row->locale)
                                        <div class="mt-1 text-[11px] uppercase tracking-[0.14em] text-slate-400">{{ $row->locale }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2 text-slate-700">{{ $row->target_label }}</td>
                            <td class="px-3 py-2 text-center text-slate-700">{{ $row->rating ?: '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $statusColor = match ($row->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-800',
                                        'rejected' => 'bg-amber-100 text-amber-800',
                                        'spam' => 'bg-rose-100 text-rose-800',
                                        default => 'bg-slate-200 text-slate-700',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center text-xs text-slate-600">
                                {{ $row->created_at?->format('Y-m-d H:i') }}
                                @if ($row->reviewed_at)
                                    <div class="text-[11px] text-slate-500">{{ __('Reviewed:') }} {{ $row->reviewed_at->format('Y-m-d H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if ($row->trashed())
                                    <button type="button" wire:click="restore({{ $row->id }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('Restore') }}
                                    </button>
                                @else
                                    <div class="inline-flex flex-wrap items-center justify-end gap-1">
                                        <button type="button" wire:click="edit({{ $row->id }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                            {{ __('Edit') }}
                                        </button>
                                        <button type="button" wire:click="delete({{ $row->id }})" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                            {{ __('Delete') }}
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">{{ __('No comments for selected filter.') }}</td>
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
