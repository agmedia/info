@php
    $isBlogGridThree = ($form['type'] ?? '') === 'blog_grid_3';
    $blockType = (string) ($form['type'] ?? '');
    $isHomeHero = $blockType === 'home_hero';
    $isHomeStats = $blockType === 'home_stats';
    $isHomeServices = $blockType === 'home_services';
@endphp

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Content / Blocks v2') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $this->isEdit ? __('Edit Block') : __('Create Block') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('Simple builder: choose type, set slot, pick items, edit Blade template, publish.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="admin-chip">{{ __('Locale:') }} {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Back to List') }}</button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title">{{ __('Core') }}</p>

            <div class="mt-4 grid gap-3 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Code') }}</label>
                    <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono" />
                    @error('form.code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Name') }}</label>
                    <input type="text" wire:model="form.name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Type') }}</label>
                    <select wire:model.live="form.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($types as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}" @selected(($form['type'] ?? '') === $typeKey)>{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                    @error('form.type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.locale') }}</label>
                    <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                        @foreach ($adminLocaleOptions as $localeOption)
                            <option value="{{ $localeOption }}" @selected(($form['locale'] ?? '') === $localeOption)>{{ $localeOption }}</option>
                        @endforeach
                    </select>
                    @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        wire:click="$toggle('form.is_active')"
                        class="admin-switch"
                        data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
                        role="switch"
                        aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
                        aria-label="{{ __('Toggle block active state') }}"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label">{{ $form['is_active'] ? __('admin.common.active') : __('admin.common.inactive') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title">{{ __('Slot (Placement)') }}</p>

            <div class="mt-4 grid gap-3 md:grid-cols-5">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Placement') }}</label>
                    <select wire:model="form.slot_placement" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($placements as $placementKey => $placementLabel)
                            <option value="{{ $placementKey }}" @selected(($form['slot_placement'] ?? '') === $placementKey)>{{ $placementLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Surface') }}</label>
                    <select wire:model="form.slot_frontend_variant" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($frontendVariants as $frontendVariantKey => $frontendVariantLabel)
                            <option value="{{ $frontendVariantKey }}" @selected(($form['slot_frontend_variant'] ?? 'all') === $frontendVariantKey)>{{ $frontendVariantLabel }}</option>
                        @endforeach
                    </select>
                    @error('form.slot_frontend_variant') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Target Type') }}</label>
                    <select wire:model="form.slot_target_type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($targetTypes as $targetTypeKey => $targetTypeLabel)
                            <option value="{{ $targetTypeKey }}" @selected((string) ($form['slot_target_type'] ?? '') === (string) $targetTypeKey)>{{ $targetTypeLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Target Ref') }}</label>
                    <input type="text" wire:model="form.slot_target_ref" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('slug or id') }}" />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sort Order') }}</label>
                    <input type="number" min="0" wire:model="form.slot_sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
            </div>

            <div class="mt-3 grid gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Starts At') }}</label>
                    <input type="datetime-local" wire:model="form.slot_starts_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Ends At') }}</label>
                    <input type="datetime-local" wire:model="form.slot_ends_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div class="flex items-end">
                    <button
                        type="button"
                        wire:click="$toggle('form.slot_is_active')"
                        class="admin-switch"
                        data-state="{{ $form['slot_is_active'] ? 'on' : 'off' }}"
                        role="switch"
                        aria-checked="{{ $form['slot_is_active'] ? 'true' : 'false' }}"
                        aria-label="{{ __('Toggle slot active state') }}"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label">{{ $form['slot_is_active'] ? __('Slot Active') : __('Slot Inactive') }}</span>
                    </button>
                </div>
            </div>
        </div>

        @if ($isBlogGridThree)
            <div class="admin-panel admin-form-panel p-3 sm:p-4">
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Content') }}
                    </button>
                    <button type="button" wire:click="setTab('sources')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'sources' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Sources') }}
                    </button>
                    <button type="button" wire:click="setTab('template')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'template' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Template') }}
                    </button>
                    <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Media') }}
                    </button>
                </div>
            </div>
        @endif

        @if (! $isBlogGridThree || $activeTab === 'content')
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Content') }}</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subtitle') }}</label>
                            <input type="text" wire:model="form.subtitle" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA Label') }}</label>
                            <input type="text" wire:model="form.cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA URL') }}</label>
                            <input type="text" wire:model="form.cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('/contact or https://...') }}" />
                        </div>
                    </div>

                    @if ($isHomeHero)
                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Browser / SEO Title') }}</label>
                                <input type="text" wire:model="form.home_page_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                @error('form.home_page_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                                <input type="text" wire:model="form.home_kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                @error('form.home_kicker') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Secondary CTA Label') }}</label>
                                <input type="text" wire:model="form.secondary_cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                @error('form.secondary_cta_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Secondary CTA URL') }}</label>
                                <input type="text" wire:model="form.secondary_cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('/kontakt or https://...') }}" />
                                @error('form.secondary_cta_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    @if ($isHomeStats)
                        <div class="mt-5">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Homepage Stats') }}</p>
                                <button type="button" wire:click="addHomeStat" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Add Stat') }}</button>
                            </div>
                            <div class="mt-3 space-y-3">
                                @foreach (($form['home_stats'] ?? []) as $index => $stat)
                                    <div wire:key="home-stat-{{ $index }}" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Stat') }} {{ $index + 1 }}</p>
                                            <button type="button" wire:click="removeHomeStat({{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <div class="grid gap-3 md:grid-cols-[1fr_0.5fr_2fr]">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Number') }}</label>
                                                <input type="text" wire:model="form.home_stats.{{ $index }}.value" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Suffix') }}</label>
                                                <input type="text" wire:model="form.home_stats.{{ $index }}.suffix" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                                <input type="text" wire:model="form.home_stats.{{ $index }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Page Stats') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Separate figures used by the shared locations section on the contact page.') }}</p>
                                </div>
                                <button type="button" wire:click="addContactStat" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Add Stat') }}</button>
                            </div>
                            <div class="mt-3 space-y-3">
                                @foreach (($form['contact_stats'] ?? []) as $index => $stat)
                                    <div wire:key="contact-stat-{{ $index }}" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Contact Stat') }} {{ $index + 1 }}</p>
                                            <button type="button" wire:click="removeContactStat({{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <div class="grid gap-3 md:grid-cols-[1fr_0.5fr_2fr]">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Number') }}</label>
                                                <input type="text" wire:model="form.contact_stats.{{ $index }}.value" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Suffix') }}</label>
                                                <input type="text" wire:model="form.contact_stats.{{ $index }}.suffix" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                                <input type="text" wire:model="form.contact_stats.{{ $index }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Page Copy') }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ __('Localized contact intro and form copy shared with the contact page.') }}</p>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Page Title') }}</label>
                                    <input type="text" wire:model="form.home_contact_page.page_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Title') }}</label>
                                    <input type="text" wire:model="form.home_contact_page.form_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Page Intro') }}</label>
                                    <textarea rows="4" wire:model="form.home_contact_page.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Intro') }}</label>
                                    <textarea rows="4" wire:model="form.home_contact_page.form_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-slate-200 pt-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Labels and Action') }}</p>
                                <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                    @foreach ([
                                        'name_label' => __('Name Label'),
                                        'email_label' => __('Email Label'),
                                        'phone_label' => __('Phone Label'),
                                        'subject_label' => __('Subject Label'),
                                        'message_label' => __('Message Label'),
                                        'submit_label' => __('Submit Button Label'),
                                    ] as $field => $label)
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</label>
                                            <input type="text" wire:model="form.home_contact_page.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Consent Label') }}</label>
                                    <textarea rows="3" wire:model="form.home_contact_page.consent_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-slate-200 pt-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Contact Sidebar') }}</p>
                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sidebar Title') }}</label>
                                        <input type="text" wire:model="form.home_contact_page.direct_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Response Time Fallback') }}</label>
                                        <input type="text" wire:model="form.home_contact_page.direct_response_fallback" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sidebar Intro') }}</label>
                                    <textarea rows="3" wire:model="form.home_contact_page.direct_body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Email') }}</label>
                                        <input
                                            type="email"
                                            wire:key="home-contact-direct-email-{{ $form['locale'] ?? 'default' }}"
                                            wire:model="form.home_contact_page.direct_email"
                                            value="{{ (string) ($form['home_contact_page']['direct_email'] ?? '') }}"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Phone') }}</label>
                                        <input type="text" wire:model="form.home_contact_page.direct_phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>
                                <div class="mt-3 grid gap-3 md:grid-cols-3">
                                    @foreach ([
                                        'direct_email_label' => __('Email Label'),
                                        'direct_phone_label' => __('Phone Label'),
                                        'direct_response_time_label' => __('Response Time Label'),
                                    ] as $field => $label)
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</label>
                                            <input type="text" wire:model="form.home_contact_page.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-5 border-t border-slate-200 pt-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Card and Success Message') }}</p>
                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Title') }}</label>
                                        <input type="text" wire:model="form.home_contact_page.help_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Success Message') }}</label>
                                        <input type="text" wire:model="form.home_contact_page.sent_status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Body') }}</label>
                                    <textarea rows="3" wire:model="form.home_contact_page.help_body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Locations Copy') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ __('Locale-aware labels, image alternative text and accessibility text shared by the homepage and contact page.') }}</p>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                                    <input type="text" wire:model="form.home_locations.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro Emphasis') }}</label>
                                    <input type="text" wire:model="form.home_locations.intro_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro Text') }}</label>
                                <textarea rows="3" wire:model="form.home_locations.intro_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Hero Locations ARIA Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.hero_aria_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Map ARIA Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.map_aria_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Map Image Alt Text') }}</label>
                                    <input type="text" wire:model="form.home_locations.map_image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Stats ARIA Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.stats_aria_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Map Link Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.map_link_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Map Region Label') }}</label>
                                    <input type="text" wire:model="form.home_locations.region_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Office Items') }}</p>
                                <button type="button" wire:click="addHomeLocation" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Add Office') }}</button>
                            </div>
                            <div class="mt-3 space-y-3">
                                @foreach (($form['home_locations']['items'] ?? []) as $index => $location)
                                    <div wire:key="home-location-{{ $form['locale'] ?? 'default' }}-{{ $index }}" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Office') }} {{ $index + 1 }}</p>
                                            <button type="button" wire:click="removeHomeLocation({{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <div class="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Entity Key') }}</label>
                                                <select wire:model="form.home_locations.items.{{ $index }}.entity_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                                    <option value="">{{ __('Select entity') }}</option>
                                                    <option value="alpha-capitalis">alpha-capitalis</option>
                                                    <option value="alpha-capitalis-timia">alpha-capitalis-timia</option>
                                                    <option value="alpha-capitalis-east">alpha-capitalis-east</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Display Number') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.number" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('City Label') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.city" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Short City Label') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.short_city" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Office Label') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.office_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company Name') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Localized Address') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.address" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Map Search Address') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.map_query" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Office Email') }}</label>
                                                <input
                                                    type="email"
                                                    wire:model="form.home_locations.items.{{ $index }}.email"
                                                    value="{{ (string) ($location['email'] ?? '') }}"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                                                />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Office Phone') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Coordinates Label') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.coordinates_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Map Marker ARIA Label') }}</label>
                                                <input type="text" wire:model="form.home_locations.items.{{ $index }}.marker_aria_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($isHomeServices)
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title Accent') }}</label>
                            <input type="text" wire:model="form.title_accent" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.title_accent') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-5">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Service Cards') }}</p>
                                <button type="button" wire:click="addHomeService" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Add Service') }}</button>
                            </div>
                            <div class="mt-3 space-y-3">
                                @foreach (($form['home_services'] ?? []) as $index => $service)
                                    <div wire:key="home-service-{{ $index }}" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Service') }} {{ $index + 1 }}</p>
                                            <button type="button" wire:click="removeHomeService({{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <div class="grid gap-3 md:grid-cols-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Service Key') }}</label>
                                                <select wire:model="form.home_services.{{ $index }}.key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                                    <option value="">{{ __('Select service') }}</option>
                                                    <option value="audit">audit</option>
                                                    <option value="accounting">accounting</option>
                                                    <option value="advisory">advisory</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                                <input type="text" wire:model="form.home_services.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subtitle') }}</label>
                                                <input type="text" wire:model="form.home_services.{{ $index }}.subtitle" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                                            <textarea rows="3" wire:model="form.home_services.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                        </div>
                                        <div class="mt-3">
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Bullets') }}</label>
                                            <textarea rows="4" wire:model="form.home_services.{{ $index }}.bullets_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('One item per line') }}"></textarea>
                                        </div>
                                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Image Alt Text') }}</label>
                                                <input type="text" wire:model="form.home_services.{{ $index }}.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                                <input type="text" wire:model="form.home_services.{{ $index }}.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Action Label') }}</label>
                                                <input type="text" wire:model="form.home_services.{{ $index }}.action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Homepage Values') }}</p>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                                    <input type="text" wire:model="form.home_values.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Introduction') }}</label>
                                    <textarea rows="3" wire:model="form.home_values.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Value Items') }}</p>
                                <button type="button" wire:click="addHomeValue" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Add Value') }}</button>
                            </div>
                            <div class="mt-3 space-y-3">
                                @foreach (($form['home_values']['items'] ?? []) as $index => $value)
                                    <div wire:key="home-value-{{ $index }}" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Value') }} {{ $index + 1 }}</p>
                                            <button type="button" wire:click="removeHomeValue({{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <div class="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                                <input type="text" wire:model="form.home_values.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                                                <textarea rows="3" wire:model="form.home_values.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Homepage Process') }}</p>
                                <button type="button" wire:click="addHomeProcessStep" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Add Process Step') }}</button>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                                <input type="text" wire:model="form.home_process.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div class="mt-3 space-y-3">
                                @foreach (($form['home_process']['items'] ?? []) as $index => $step)
                                    <div wire:key="home-process-{{ $index }}" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Step') }} {{ $index + 1 }}</p>
                                            <button type="button" wire:click="removeHomeProcessStep({{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <div class="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                                <input type="text" wire:model="form.home_process.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                                                <textarea rows="3" wire:model="form.home_process.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Homepage News') }}</p>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                @foreach ([
                                    'title' => __('Section Title'),
                                    'all_posts_label' => __('All Posts Label'),
                                    'all_posts_url' => __('All Posts URL'),
                                    'post_action_label' => __('Post Action Label'),
                                    'category_fallback' => __('Category Fallback'),
                                    'excerpt_fallback' => __('Excerpt Fallback'),
                                ] as $field => $label)
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</label>
                                        @if ($field === 'excerpt_fallback')
                                            <textarea rows="3" wire:model="form.home_news.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                        @else
                                            <input type="text" wire:model="form.home_news.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Homepage Contact CTA') }}</p>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                @foreach ([
                                    'title' => __('Section Title'),
                                    'card_title' => __('Card Title'),
                                    'text' => __('Text'),
                                    'button_label' => __('Button Label'),
                                    'button_url' => __('Button URL'),
                                    'status' => __('Status Text'),
                                ] as $field => $label)
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</label>
                                        @if ($field === 'text')
                                            <textarea rows="3" wire:model="form.home_contact_cta.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                        @else
                                            <input type="text" wire:model="form.home_contact_cta.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (($form['type'] ?? '') === 'five_star_reviews_carousel' || ($form['type'] ?? '') === 'blogs_carousel')
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                                {{ ($form['type'] ?? '') === 'blogs_carousel' ? __('Number of blog posts to show') : __('Number of comments to show') }}
                            </label>
                            <input type="number" min="1" max="50" wire:model="form.items_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[220px]" />
                            @error('form.items_limit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                            @if (($form['type'] ?? '') === 'blogs_carousel')
                                <div class="mt-2 md:max-w-[220px]">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog source') }}</label>
                                    <select wire:model="form.blog_source" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <option value="latest">{{ __('Latest') }}</option>
                                        <option value="featured">{{ __('Featured only') }}</option>
                                    </select>
                                </div>
                            @else
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" wire:model="form.reviews_featured_only" class="h-4 w-4 border-slate-300 text-slate-900 focus:ring-0">
                                    <span class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-600">{{ __('Featured comments only') }}</span>
                                </label>
                            @endif
                        </div>
                    @endif

                    <p class="mt-3 text-xs text-slate-500">
                        {{ $isBlogGridThree ? __('Main markup/content is edited in the Template tab.') : __('Main markup/content is edited in the Blade Template section below (Ace).') }}
                    </p>
                </div>

                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Style & Background') }}</p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Custom Classes') }}</label>
                        <input type="text" wire:model="form.custom_classes" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('extra utility classes') }}" />
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Background CSS') }}</label>
                        <textarea rows="4" wire:model="form.bg_css" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs" placeholder="{{ __('background-color:#0f172a; color:white;') }}"></textarea>
                        <p class="mt-1 text-xs text-slate-500">{{ __('If a background image is uploaded, it is applied first, then this CSS is appended.') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($isBlogGridThree && $activeTab === 'sources')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Sources') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Pick the blog category and query settings for the article cards shown on the selected page.') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog Category') }}</label>
                        <select wire:model="form.blog_category_id" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="">{{ __('Select category...') }}</option>
                            @foreach ($this->blogCategoryOptions as $row)
                                <option value="{{ $row['id'] }}">{{ $row['label'] }}</option>
                            @endforeach
                        </select>
                        @error('form.blog_category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Number of blog posts to show') }}</label>
                        <input type="number" min="1" max="50" wire:model="form.items_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.items_limit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sort Posts') }}</label>
                        <select wire:model="form.blog_sort" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="newest">{{ __('Newest first') }}</option>
                            <option value="featured">{{ __('Featured first') }}</option>
                            <option value="title">{{ __('Title A-Z') }}</option>
                        </select>
                        @error('form.blog_sort') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="mt-3 text-xs text-slate-500">{{ __('If CTA URL is left empty, the block can still work without a button. Use page targeting above to place this block on one specific page.') }}</p>
            </div>
        @endif

        @if ($this->isItemBlock && (! $isBlogGridThree || $activeTab === 'content'))
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Selected Items') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Choose items and order them. No JSON IDs needed.') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Available') }}</label>
                        <select wire:model="pickerItemId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="">{{ __('Select item...') }}</option>
                            @foreach ($this->itemOptions as $option)
                                <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" wire:click="addSelectedItem" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">{{ __('Add Item') }}</button>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse ($this->selectedItems as $row)
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <div class="text-sm text-slate-800">{{ $row['label'] }}</div>
                            <div class="inline-flex items-center gap-1">
                                <button type="button" wire:click="moveSelectedItemUp({{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                <button type="button" wire:click="moveSelectedItemDown({{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                <button type="button" wire:click="removeSelectedItem({{ $row['id'] }})" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('No items selected.') }}</div>
                    @endforelse
                    @error('form.selected_item_ids') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @if (! $isBlogGridThree || $activeTab === 'template')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Blade Template (Per Block File)') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Saved to') }} <code>resources/views/front/content-blocks/instances/{{ $form['code'] ?: 'block-code' }}.blade.php</code>. {{ __('This block only.') }}</p>

                <div class="mt-3 mb-2 flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="loadTemplatePreset" class="rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-semibold text-cyan-800 hover:bg-cyan-100">{{ __('Load Default For Type') }}</button>
                    <button
                        type="button"
                        data-ace-open
                        data-ace-target="content-block-template-blade"
                        data-ace-label="Content Block Blade Template"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                    >
                        {{ __('Open in Ace') }}
                    </button>
                </div>

                <textarea id="content-block-template-blade" rows="16" wire:model="form.template_body" data-ace-inline class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                @error('form.template_body') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if ($isBlogGridThree && $activeTab === 'media' && ! $blockId)
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Media') }}</p>
                <p class="text-sm text-slate-600">{{ __('Save the block first to manage media assets for this section.') }}</p>
            </div>
        @endif

        @if ($blockId && (! $isBlogGridThree || $activeTab === 'media'))
            <livewire:admin.media.manager
                :model-class="\App\Models\Content\ContentBlock::class"
                :model-id="$blockId"
                :locale="$form['locale']"
                :wire:key="'content-block-media-manager-'.($blockId ?? 'new').'-'.$form['locale']"
            />
        @endif

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ $this->isEdit ? __('Update Block') : __('Create Block') }}
            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Cancel') }}
            </button>
        </div>
    </form>
</div>
