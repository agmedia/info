@php
    $jobOpeningPreviewUrl = $isEdit && trim((string) ($form['slug'] ?? '')) !== ''
        ? route('admin.content.job-openings.preview', ['jobOpening' => $jobOpeningId, 'locale' => $form['locale']])
        : null;
@endphp

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    {{ __('admin.content.job_openings.form.eyebrow') }}
                </p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                    {{ $isEdit ? __('admin.content.job_openings.form.edit_title') : __('admin.content.job_openings.form.create_title') }}
                </h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('admin.content.job_openings.form.subtitle') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">
                    {{ __('admin.common.locale') }}: {{ $form['locale'] }}
                </span>
                <button
                    type="button"
                    wire:click="backToList"
                    data-admin-leave
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                >
                    {{ __('admin.content.job_openings.form.back_to_list') }}
                </button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6" data-admin-dirty-form>
        @include('livewire.admin.partials.form-error-summary')

        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="setTab('content')"
                    class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}"
                >
                    {{ __('admin.content.job_openings.form.tabs.content') }}
                </button>
                <button
                    type="button"
                    wire:click="setTab('seo')"
                    class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}"
                >
                    {{ __('admin.content.job_openings.form.tabs.seo') }}
                </button>
            </div>
        </div>

        @if ($activeTab === 'content')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('admin.content.job_openings.form.content_section') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-12">
                    <div class="md:col-span-5">
                        <label for="job-opening-title" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('admin.content.job_openings.form.fields.title') }}
                        </label>
                        <input
                            id="job-opening-title"
                            type="text"
                            wire:model.live.debounce.250ms="form.title"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                        />
                        @error('form.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-5">
                        <div class="flex items-center justify-between gap-3">
                            <label for="job-opening-slug" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                                {{ __('admin.content.job_openings.form.fields.slug') }}
                            </label>
                            <button type="button" wire:click="generateSlug" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                {{ __('admin.content.job_openings.form.generate_slug') }}
                            </button>
                        </div>
                        <input
                            id="job-opening-slug"
                            type="text"
                            wire:model="form.slug"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase"
                        />
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.job_openings.form.slug_help') }}</p>
                        @error('form.slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="job-opening-locale" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('admin.common.locale') }}
                        </label>
                        <select
                            id="job-opening-locale"
                            wire:model.live="form.locale"
                            data-tom-select
                            data-tom-no-search="1"
                            class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase"
                        >
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
                            @endforeach
                        </select>
                        @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <label for="job-opening-locations" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('admin.content.job_openings.form.fields.locations') }}
                        </label>
                        <input
                            id="job-opening-locations"
                            type="text"
                            wire:model="form.locations"
                            placeholder="{{ __('admin.content.job_openings.form.locations_placeholder') }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                        />
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.job_openings.form.locations_help') }}</p>
                        @error('form.locations') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-5">
                        <label for="job-opening-published-at" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('admin.content.job_openings.form.fields.published_at') }} ({{ config('admin_ui.timezone', 'Europe/Zagreb') }})
                        </label>
                        <input
                            id="job-opening-published-at"
                            type="datetime-local"
                            step="60"
                            wire:model="form.published_at"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                        />
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.job_openings.form.published_at_help') }}</p>
                        @error('form.published_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button
                        type="button"
                        wire:click="$toggle('form.is_active')"
                        class="admin-switch"
                        data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
                        role="switch"
                        aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
                        aria-label="{{ __('admin.content.job_openings.form.toggle_active') }}"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label">
                            {{ $form['is_active'] ? __('admin.common.active') : __('admin.common.inactive') }}
                        </span>
                    </button>
                </div>

                <div class="mt-5">
                    <label for="job-opening-excerpt" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.content.job_openings.form.fields.excerpt') }}
                    </label>
                    <textarea
                        id="job-opening-excerpt"
                        rows="3"
                        wire:model.live.debounce.250ms="form.excerpt"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    ></textarea>
                    <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.job_openings.form.excerpt_help') }}</p>
                    @error('form.excerpt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-5" wire:key="job-opening-body-{{ $jobOpeningId ?? 'new' }}-{{ $form['locale'] }}">
                    <label for="job-opening-body-html" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.content.job_openings.form.fields.body') }}
                    </label>
                    <textarea
                        id="job-opening-body-html"
                        rows="18"
                        wire:model.live.debounce.300ms="form.body_html"
                        data-quill-editor
                        data-quill-profile="service-text"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    ></textarea>
                    <p class="mt-2 text-xs text-slate-500">{{ __('admin.content.job_openings.form.body_help') }}</p>
                    @error('form.body_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @if ($activeTab === 'seo')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('admin.content.job_openings.form.seo_section') }}</p>

                <div class="mt-4">
                    <label for="job-opening-meta-title" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.content.job_openings.form.fields.meta_title') }}
                    </label>
                    <input
                        id="job-opening-meta-title"
                        type="text"
                        wire:model.live.debounce.250ms="form.meta_title"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    />
                    <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.job_openings.form.meta_title_help') }}</p>
                    @error('form.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="job-opening-meta-description" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                        {{ __('admin.content.job_openings.form.fields.meta_description') }}
                    </label>
                    <textarea
                        id="job-opening-meta-description"
                        rows="4"
                        wire:model.live.debounce.250ms="form.meta_description"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    ></textarea>
                    <p class="mt-1 text-xs text-slate-500">{{ __('admin.content.job_openings.form.meta_description_help') }}</p>
                    @error('form.meta_description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @include('livewire.admin.partials.form-actions', [
            'previewUrl' => $jobOpeningPreviewUrl,
            'submitLabel' => $isEdit
                ? __('admin.content.job_openings.form.buttons.update')
                : __('admin.content.job_openings.form.buttons.create'),
        ])
    </form>
</div>
