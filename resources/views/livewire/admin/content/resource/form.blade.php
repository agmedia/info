<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('admin.content.resources.form.eyebrow') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $isEdit ? __('admin.content.resources.form.edit_title') : __('admin.content.resources.form.create_title') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('admin.content.resources.form.subtitle') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">{{ __('Locale:') }} {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('admin.content.resources.form.buttons.back') }}</button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('admin.content.resources.form.tabs.content') }}
                </button>
                <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('admin.content.resources.form.tabs.media') }}
                </button>
                <button type="button" wire:click="setTab('seo')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('admin.content.resources.form.tabs.seo') }}
                </button>
            </div>
        </div>

        @if ($activeTab === 'content')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('admin.content.resources.form.sections.core') }}</p>

                <div class="mt-4 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Code') }}</label>
                        <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono" />
                        @error('form.code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.group') }}</label>
                        <select wire:model="form.group_code" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            @foreach ($groupOptions as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.group_code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Published At') }}</label>
                        <input type="datetime-local" wire:model="form.published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.published_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sort Order') }}</label>
                        <input type="number" min="0" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.locale') }}</label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}" @selected(($form['locale'] ?? '') === $localeOption)>{{ $localeOption }}</option>
                            @endforeach
                        </select>
                        @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        wire:click="$toggle('form.is_active')"
                        class="admin-switch"
                        data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
                        role="switch"
                        aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
                        aria-label="{{ __('admin.content.resources.form.fields.toggle_active') }}"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label">{{ $form['is_active'] ? __('admin.common.active') : __('admin.common.inactive') }}</span>
                    </button>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.title') }}</label>
                        <input type="text" wire:model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                            <button type="button" wire:click="generateSlug" class="text-xs font-semibold text-slate-600 hover:text-slate-900">{{ __('Generate') }}</button>
                        </div>
                        <input type="text" wire:model="form.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        @error('form.slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.excerpt') }}</label>
                    <textarea rows="4" wire:model="form.excerpt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        @endif

        @if ($activeTab === 'media')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('admin.content.resources.form.sections.media') }}</p>

                <div class="mt-4 grid gap-6 lg:grid-cols-2">
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.download_url') }}</label>
                            <input type="url" wire:model="form.download_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.download_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            @if ($form['download_url'])
                                <a href="{{ $form['download_url'] }}" target="_blank" rel="noreferrer" class="mt-2 inline-flex text-xs font-semibold text-cyan-700 hover:text-cyan-800">
                                    {{ __('admin.content.resources.form.fields.open_download') }}
                                </a>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.download_upload') }}</label>
                            <input type="file" wire:model="downloadUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('downloadUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            @if ($downloadUpload)
                                <p class="mt-2 text-xs text-slate-500">{{ $downloadUpload->getClientOriginalName() }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.source_url') }}</label>
                            <input type="url" wire:model="form.source_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.source_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.cover_image_url') }}</label>
                            <input type="url" wire:model="form.cover_image_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.cover_image_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.cover_image_upload') }}</label>
                            <input type="file" wire:model="coverImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('coverImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.cover_preview') }}</p>
                            @if ($coverImageUpload)
                                <img src="{{ $coverImageUpload->temporaryUrl() }}" alt="{{ __('admin.content.resources.form.fields.cover_preview') }}" class="h-56 w-full rounded-xl object-cover" />
                            @elseif ($form['cover_image_url'])
                                <img src="{{ $form['cover_image_url'] }}" alt="{{ __('admin.content.resources.form.fields.cover_preview') }}" class="h-56 w-full rounded-xl object-cover" />
                            @else
                                <div class="flex h-56 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-400">
                                    {{ __('admin.common.no_image') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'seo')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('admin.content.resources.form.sections.seo') }}</p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Title') }}</label>
                    <input type="text" wire:model="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Description') }}</label>
                    <textarea rows="3" wire:model="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.payload') }}</label>
                    <textarea rows="6" wire:model="form.payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    @error('form.payload_text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.resources.form.fields.translation_payload') }}</label>
                    <textarea rows="6" wire:model="form.translation_payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    @error('form.translation_payload_text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ $isEdit ? __('admin.content.resources.form.buttons.update') : __('admin.content.resources.form.buttons.create') }}
            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('admin.content.resources.form.buttons.cancel') }}
            </button>
        </div>
    </form>
</div>
