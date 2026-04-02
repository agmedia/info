@php
    $translationPayload = $form['translation_payload'] ?? [];
    $pagePayload = $form['page_payload'] ?? [];
    $currentTemplateKey = $form['template_key'] ?? \App\Support\Content\ServicePageTemplateRegistry::FAMILY_BUSINESS;
    $currentTemplateLabel = $templateOptions[$currentTemplateKey] ?? $currentTemplateKey;
    $isFinanceTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::FINANCE;
    $isAccountingTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::ACCOUNTING;
    $isAuditTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::AUDIT;
    $isTaxTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::TAX;
    $isEuFundsTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::EU_FUNDS;
    $accountingEditorSections = [
        'accounting-intro-admin' => __('Overview'),
        'accounting-editorial-admin' => __('Editorial'),
        'accounting-details-admin' => __('Sections 1-7'),
        'accounting-meeting-admin' => __('Meeting'),
        'accounting-videos-admin' => __('Videos'),
        'accounting-blog-admin' => __('Blog'),
    ];
    $financeEditorSections = [
        'finance-pandea' => __('Pandea'),
        'finance-services-intro' => __('Services Intro'),
        'finance-ma' => __('M&A'),
        'finance-due-diligence' => __('Due Diligence'),
        'finance-valuations' => __('Valuations'),
        'finance-capital-raising' => __('Capital Raising'),
        'finance-restructuring' => __('Restructuring'),
        'finance-meeting' => __('Meeting'),
    ];
    $auditEditorSections = [
        'audit-overview-admin' => __('Overview'),
        'audit-obligors-admin' => __('Obligors'),
        'audit-services-admin' => __('Services'),
        'audit-value-admin' => __('Value'),
        'audit-approach-admin' => __('Approach'),
        'audit-meeting-admin' => __('Meeting'),
    ];
    $taxEditorSections = [
        'tax-overview-admin' => __('Overview'),
        'tax-services-admin' => __('Services'),
        'tax-compliance-admin' => __('Compliance'),
        'tax-review-admin' => __('Tax Review'),
        'tax-optimization-admin' => __('Optimization'),
        'tax-due-diligence-admin' => __('Due Diligence'),
        'tax-transfer-pricing-admin' => __('Transfer Pricing'),
        'tax-meeting-admin' => __('Meeting'),
    ];
    $euFundsEditorSections = [
        'eu-funds-about' => 'About',
        'eu-funds-overview' => 'Overview',
        'eu-funds-chart' => 'Funding Chart',
        'eu-funds-process' => 'Process',
        'eu-funds-calls' => 'Calls',
        'eu-funds-resources' => 'Resources',
        'eu-funds-laws' => 'Laws',
        'eu-funds-testimonials' => 'Testimonials',
        'eu-funds-blog' => 'Blog',
        'eu-funds-meeting' => 'Meeting',
    ];
    $blogAutoCategoryLabel = $isAccountingTemplate
        ? __('Auto (current accounting category)')
        : ($isAuditTemplate
            ? __('Auto (current audit category)')
            : ($isTaxTemplate
                ? __('Auto (current tax category)')
                : ($isEuFundsTemplate
                    ? __('Auto (current EU funds category)')
                    : __('Auto (current family-business category)'))));
@endphp

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Content / Services') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $isEdit ? __('Edit Service Page') : __('Create Service Page') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('Template-driven service landing page with locale content, sources, and media.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">{{ __('Locale:') }} {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Back to List') }}</button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('Content') }}
                </button>
                @if ($templateSupportsSources)
                    <button type="button" wire:click="setTab('sources')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'sources' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Sources') }}
                    </button>
                @endif
                <button type="button" wire:click="setTab('seo')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('SEO') }}
                </button>
                <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('Media') }}
                </button>
            </div>
        </div>

        @if ($activeTab === 'content')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Core Data') }}</p>

                <div class="mt-4 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Code') }}</label>
                        <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono" />
                        @error('form.code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Template') }}</label>
                        @if ($isEdit)
                            <input type="text" value="{{ $currentTemplateLabel }}" readonly class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700" />
                        @else
                            <select wire:model.live="form.template_key" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                @foreach ($templateOptions as $optionKey => $templateLabel)
                                    <option value="{{ $optionKey }}" @selected($currentTemplateKey === $optionKey)>{{ $templateLabel }}</option>
                                @endforeach
                            </select>
                        @endif
                        @if ($isEdit)
                            <p class="mt-1 text-xs text-slate-500">{{ __('Template is locked after creation so block structure stays stable.') }}</p>
                        @endif
                        @error('form.template_key') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
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
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Locale') }}</label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
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
                        aria-label="{{ __('Toggle service page active state') }}"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label">{{ $form['is_active'] ? __('Active') : __('Inactive') }}</span>
                    </button>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
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
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Hero') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Brand Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.hero.brand_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subtitle Lead') }}</label>
                        <input type="text" wire:model="form.translation_payload.hero.subtitle_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subtitle Accent') }}</label>
                        <input type="text" wire:model="form.translation_payload.hero.subtitle_accent" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                    <textarea rows="5" wire:model="form.translation_payload.hero.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.hero.cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA URL') }}</label>
                        <input type="text" wire:model="form.translation_payload.hero.cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            @if ($isFinanceTemplate)
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Finance Navigator') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($financeEditorSections as $sectionId => $sectionLabel)
                            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
                        @endforeach
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        {{ __('Top-level finance sections are fixed by the frontend template. You can add or remove paragraphs, list rows, and sale phases below, but adding a completely new section still requires a template/code change.') }}
                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="finance-pandea" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Pandea Network') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['pandea']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('pandea.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="5" wire:model="form.translation_payload.pandea.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('pandea.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Logo Alt') }}</label>
                            <input type="text" wire:model="form.translation_payload.pandea.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div id="finance-services-intro" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Services Intro') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.services_intro.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.services_intro.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="6" wire:model="form.translation_payload.services_intro.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div id="finance-ma" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('M&A Section') }}</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.ma.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sale Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.ma.sale.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Intro') }}</label>
                        <textarea rows="5" wire:model="form.translation_payload.ma.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sale Body') }}</label>
                        <textarea rows="6" wire:model="form.translation_payload.ma.sale.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sale Process Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.ma.sale.process_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach (($translationPayload['ma']['sale']['phases'] ?? []) as $phaseIndex => $phase)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phase Block') }} #{{ $phaseIndex + 1 }}</p>
                                    <button type="button" wire:click="removeTranslationListItem('ma.sale.phases', {{ $phaseIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove Phase') }}</button>
                                </div>

                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phase') }} #{{ $phaseIndex + 1 }}</label>
                                        <input type="text" wire:model="form.translation_payload.ma.sale.phases.{{ $phaseIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phase Label') }}</label>
                                        <input type="text" wire:model="form.translation_payload.ma.sale.phases.{{ $phaseIndex }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                                    @foreach (($phase['items'] ?? []) as $itemIndex => $item)
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item') }} #{{ $itemIndex + 1 }}</label>
                                                <button type="button" wire:click="removeTranslationListItem('ma.sale.phases.{{ $phaseIndex }}.items', {{ $itemIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.ma.sale.phases.{{ $phaseIndex }}.items.{{ $itemIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <button type="button" wire:click="addTranslationListItem('ma.sale.phases.{{ $phaseIndex }}.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('Add Item') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="button" wire:click="addTranslationListItem('ma.sale.phases', 'phase')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            {{ __('Add Phase') }}
                        </button>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Acquisition Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.ma.acquisition.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div></div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Acquisition Body') }}</label>
                        <textarea rows="5" wire:model="form.translation_payload.ma.acquisition.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="finance-due-diligence" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Due Diligence') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="6" wire:model="form.translation_payload.due_diligence.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.help_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach (($translationPayload['due_diligence']['help_items'] ?? []) as $index => $item)
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Item') }} #{{ $index + 1 }}</label>
                                        <button type="button" wire:click="removeTranslationListItem('due_diligence.help_items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                    </div>
                                    <input type="text" wire:model="form.translation_payload.due_diligence.help_items.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('due_diligence.help_items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Help Item') }}
                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Closing Text') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.due_diligence.closing" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="finance-valuations" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Valuations') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.valuations.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['valuations']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('valuations.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.valuations.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('valuations.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Methods Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.valuations.methods_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach (($translationPayload['valuations']['methods'] ?? []) as $index => $method)
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Method') }} #{{ $index + 1 }}</label>
                                        <button type="button" wire:click="removeTranslationListItem('valuations.methods', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                    </div>
                                    <input type="text" wire:model="form.translation_payload.valuations.methods.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('valuations.methods')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Method') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div id="finance-capital-raising" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('Capital Raising') }}</p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.capital_raising.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div class="space-y-3">
                            @foreach (($translationPayload['capital_raising']['body'] ?? []) as $index => $paragraph)
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                        <button type="button" wire:click="removeTranslationListItem('capital_raising.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                    </div>
                                    <textarea rows="4" wire:model="form.translation_payload.capital_raising.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            @endforeach

                            <div class="pt-1">
                                <button type="button" wire:click="addTranslationListItem('capital_raising.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    {{ __('Add Paragraph') }}
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sources Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.capital_raising.sources_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                            <div class="mt-4 space-y-3">
                                @foreach (($translationPayload['capital_raising']['sources'] ?? []) as $index => $source)
                                    <div>
                                        <div class="mb-1 flex items-center justify-between gap-3">
                                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Source') }} #{{ $index + 1 }}</label>
                                            <button type="button" wire:click="removeTranslationListItem('capital_raising.sources', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                        </div>
                                        <input type="text" wire:model="form.translation_payload.capital_raising.sources.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <button type="button" wire:click="addTranslationListItem('capital_raising.sources')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    {{ __('Add Source') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="finance-restructuring" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('Financial Restructuring') }}</p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.restructuring.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div class="space-y-3">
                            @foreach (($translationPayload['restructuring']['body'] ?? []) as $index => $paragraph)
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                        <button type="button" wire:click="removeTranslationListItem('restructuring.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                    </div>
                                    <textarea rows="4" wire:model="form.translation_payload.restructuring.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            @endforeach

                            <div class="pt-1">
                                <button type="button" wire:click="addTranslationListItem('restructuring.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    {{ __('Add Paragraph') }}
                                </button>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Pre-bankruptcy Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.restructuring.prebankruptcy_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Pre-bankruptcy Text') }}</label>
                                <textarea rows="4" wire:model="form.translation_payload.restructuring.prebankruptcy_body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Options Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.restructuring.options_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                <div class="mt-3 space-y-3">
                                    @foreach (($translationPayload['restructuring']['options'] ?? []) as $index => $item)
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Option') }} #{{ $index + 1 }}</label>
                                                <button type="button" wire:click="removeTranslationListItem('restructuring.options', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.restructuring.options.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="button" wire:click="addTranslationListItem('restructuring.options')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('Add Option') }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Reasons Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.restructuring.reasons_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                <div class="mt-3 space-y-3">
                                    @foreach (($translationPayload['restructuring']['reasons'] ?? []) as $index => $item)
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Reason') }} #{{ $index + 1 }}</label>
                                                <button type="button" wire:click="removeTranslationListItem('restructuring.reasons', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.restructuring.reasons.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="button" wire:click="addTranslationListItem('restructuring.reasons')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('Add Reason') }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Team Services Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.restructuring.team_services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                <div class="mt-3 space-y-3">
                                    @foreach (($translationPayload['restructuring']['team_services'] ?? []) as $index => $item)
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Team Service') }} #{{ $index + 1 }}</label>
                                                <button type="button" wire:click="removeTranslationListItem('restructuring.team_services', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.restructuring.team_services.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="button" wire:click="addTranslationListItem('restructuring.team_services')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        {{ __('Add Team Service') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="finance-meeting" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('Meeting Section') }}</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                        <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 1') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 2') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Submit Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Phone Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Email Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Labels') }}</p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('First Name') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Last Name') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subject Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Message Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>
                </div>
            @elseif ($isAuditTemplate)
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Audit Navigator') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($auditEditorSections as $sectionId => $sectionLabel)
                            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
                        @endforeach
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        {{ __('Revizija koristi fiksni landing layout. Ovdje uređujete copy sekcija, liste i završne blokove za kontakt i blog.') }}
                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="audit-overview-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Overview Block') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.overview.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlight Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.highlight_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['overview']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('overview.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.overview.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>
                    </div>

                    <div id="audit-obligors-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Obligors & Thresholds') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.obligors.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.obligors.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.obligors.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Primary Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.obligors.primary_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['obligors']['primary_items'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Primary Item') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('obligors.primary_items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.obligors.primary_items.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('obligors.primary_items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Primary Item') }}
                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Thresholds Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.obligors.thresholds_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Thresholds Intro') }}</label>
                            <textarea rows="3" wire:model="form.translation_payload.obligors.thresholds_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        @foreach (($translationPayload['obligors']['thresholds'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Threshold') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('obligors.thresholds', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.obligors.thresholds.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('obligors.thresholds')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Threshold') }}
                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Note') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.obligors.note" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div id="audit-services-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('Audit Services') }}</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.services.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.services.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                        <textarea rows="4" wire:model="form.translation_payload.services.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-6 grid gap-4 xl:grid-cols-3">
                        @foreach (($translationPayload['services']['items'] ?? []) as $index => $item)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Service Item') }} #{{ $index + 1 }}</p>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                    <input type="text" wire:model="form.translation_payload.services.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                                    <textarea rows="5" wire:model="form.translation_payload.services.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="audit-value-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Value Section') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.value.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.value.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.value.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        @foreach (($translationPayload['value']['benefits'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Benefit') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('value.benefits', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.value.benefits.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('value.benefits')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Benefit') }}
                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Conclusion') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.value.conclusion" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="audit-approach-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Approach Section') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.approach.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.approach.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Principles Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.approach.principles_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['approach']['principles'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Principle') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('approach.principles', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.approach.principles.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('approach.principles')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Principle') }}
                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Reasons Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.approach.reasons_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['approach']['reasons'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Reason') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('approach.reasons', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.approach.reasons.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('approach.reasons')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Reason') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Blog Section') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500">{{ __('Use :category placeholder if you want the current blog category name inserted automatically.') }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="audit-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Meeting Section') }}</p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 1') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 2') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Submit Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Phone Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Email Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Labels') }}</p>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('First Name') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Last Name') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subject Label') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Message Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($isTaxTemplate)
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Tax Navigator') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($taxEditorSections as $sectionId => $sectionLabel)
                            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
                        @endforeach
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        {{ __('Porezi koristi fiksni landing layout. Ovdje uređujete copy, liste, compliance blokove i završne kontakt/blog sekcije.') }}
                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="tax-overview-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Overview Block') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.overview.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlight Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.highlight_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['overview']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('overview.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.overview.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>
                    </div>

                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Overview Highlights') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlights Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.overview.highlights_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['overview']['highlights'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlight') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('overview.highlights', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.overview.highlights.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('overview.highlights')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Highlight') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div id="tax-services-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('Services Grid') }}</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.services.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.services.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                        <textarea rows="4" wire:model="form.translation_payload.services.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-6 grid gap-4 xl:grid-cols-3">
                        @foreach (($translationPayload['services']['items'] ?? []) as $index => $item)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Service Item') }} #{{ $index + 1 }}</p>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                    <input type="text" wire:model="form.translation_payload.services.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                                    <textarea rows="5" wire:model="form.translation_payload.services.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="tax-compliance-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title">{{ __('Compliance Block') }}</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.compliance.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.compliance.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                        <textarea rows="4" wire:model="form.translation_payload.compliance.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-6 grid gap-6 xl:grid-cols-2">
                        <div class="space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Corporate') }}</p>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.compliance.corporate.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                                <textarea rows="4" wire:model="form.translation_payload.compliance.corporate.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            @foreach (($translationPayload['compliance']['corporate']['groups'] ?? []) as $groupIndex => $group)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Group Title') }} #{{ $groupIndex + 1 }}</label>
                                    <input type="text" wire:model="form.translation_payload.compliance.corporate.groups.{{ $groupIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                    @foreach (($group['items'] ?? []) as $itemIndex => $item)
                                        <div class="mt-3">
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item') }} #{{ $itemIndex + 1 }}</label>
                                                <button type="button" wire:click="removeTranslationListItem('compliance.corporate.groups.{{ $groupIndex }}.items', {{ $itemIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.compliance.corporate.groups.{{ $groupIndex }}.items.{{ $itemIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    @endforeach

                                    <div class="mt-3">
                                        <button type="button" wire:click="addTranslationListItem('compliance.corporate.groups.{{ $groupIndex }}.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                            {{ __('Add Item') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Individual') }}</p>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.compliance.individual.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                                <textarea rows="4" wire:model="form.translation_payload.compliance.individual.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            @foreach (($translationPayload['compliance']['individual']['items'] ?? []) as $index => $item)
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item') }} #{{ $index + 1 }}</label>
                                        <button type="button" wire:click="removeTranslationListItem('compliance.individual.items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                    </div>
                                    <input type="text" wire:model="form.translation_payload.compliance.individual.items.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            @endforeach

                            <div class="mt-3">
                                <button type="button" wire:click="addTranslationListItem('compliance.individual.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    {{ __('Add Item') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="tax-review-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Tax Review') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.review.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.review.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.review.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        @foreach (($translationPayload['review']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('review.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.review.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('review.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlights Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.review.highlights_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['review']['highlights'] ?? []) as $index => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlight') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('review.highlights', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.review.highlights.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('review.highlights')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Highlight') }}
                            </button>
                        </div>
                    </div>

                    <div id="tax-optimization-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Optimization') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.optimization.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.optimization.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.optimization.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        @foreach (($translationPayload['optimization']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('optimization.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.optimization.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('optimization.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="tax-due-diligence-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Due Diligence') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.due_diligence.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        @foreach (($translationPayload['due_diligence']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('due_diligence.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.due_diligence.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('due_diligence.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>
                    </div>

                    <div id="tax-transfer-pricing-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Transfer Pricing') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.transfer_pricing.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.transfer_pricing.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.transfer_pricing.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        @foreach (($translationPayload['transfer_pricing']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('transfer_pricing.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.transfer_pricing.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('transfer_pricing.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Blog Section') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500">{{ __('Use :category placeholder if you want the current blog category name inserted automatically.') }}</p>
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="tax-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Meeting Section') }}</p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 1') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 2') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Submit Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Phone Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Email Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Labels') }}</p>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('First Name') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Last Name') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subject Label') }}</label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Message Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($isAccountingTemplate)
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Accounting Navigator') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($accountingEditorSections as $sectionId => $sectionLabel)
                            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
                        @endforeach
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        {{ __('Računovodstvo sada koristi puni landing template s overview blokom, editorial sekcijom, 7 detaljnih service sekcija, video blokom, kontaktom i blogom. Ovdje uređujete sav tekstualni sadržaj tih blokova.') }}
                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="accounting-intro-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Overview Block') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.intro_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.intro_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        @foreach (($translationPayload['intro_section']['body'] ?? []) as $index => $paragraph)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('intro_section.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.intro_section.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('intro_section.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Paragraph') }}
                            </button>
                        </div>

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            {{ __('Popis usluga u overview bloku automatski povlači naslove iz sekcija 1-7 niže u editoru.') }}
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Video Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.intro_section.video_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('YouTube URL') }}</label>
                            <input type="text" wire:model="form.translation_payload.intro_section.video_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div id="accounting-editorial-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24 xl:col-span-2">
                        <p class="admin-section-title">{{ __('Editorial Block') }}</p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Eyebrow') }}</label>
                                <input type="text" wire:model="form.translation_payload.editorial_section.eyebrow" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.editorial_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subtitle') }}</label>
                            <textarea rows="3" wire:model="form.translation_payload.editorial_section.subtitle" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-6 grid gap-4 xl:grid-cols-2">
                            @foreach (($translationPayload['editorial_section']['cards'] ?? []) as $cardIndex => $card)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card') }} #{{ $cardIndex + 1 }}</p>

                                    <div class="mt-3">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                        <input type="text" wire:model="form.translation_payload.editorial_section.cards.{{ $cardIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>

                                    <div class="mt-3">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Body') }}</label>
                                        <textarea rows="6" wire:model="form.translation_payload.editorial_section.cards.{{ $cardIndex }}.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="accounting-details-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24 xl:col-span-2">
                        <p class="admin-section-title">{{ __('Service Sections 1-7') }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ __('Naslovi ovih sekcija automatski pune gornji popis Usluge računovodstva na stranici.') }}</p>

                        <div class="mt-6 space-y-5">
                            @foreach (($translationPayload['detail_sections'] ?? []) as $sectionIndex => $section)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section') }} #{{ $sectionIndex + 1 }}</p>
                                        <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-500">{{ $section['slug'] ?? ('section-'.$sectionIndex) }}</span>
                                    </div>

                                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('List Title') }}</label>
                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.list_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                                        <textarea rows="4" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div class="mt-5">
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('List Items') }}</p>
                                        <div class="mt-3 space-y-3">
                                            @foreach (($section['items'] ?? []) as $itemIndex => $item)
                                                <div>
                                                    <div class="mb-1 flex items-center justify-between gap-3">
                                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item') }} #{{ $itemIndex + 1 }}</label>
                                                        <button type="button" wire:click="removeTranslationListItem('detail_sections.{{ $sectionIndex }}.items', {{ $itemIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                                    </div>
                                                    <textarea rows="3" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.items.{{ $itemIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" wire:click="addTranslationListItem('detail_sections.{{ $sectionIndex }}.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                {{ __('Add Item') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('After List Paragraphs') }}</p>
                                        <div class="mt-3 space-y-3">
                                            @foreach (($section['after_list'] ?? []) as $paragraphIndex => $paragraph)
                                                <div>
                                                    <div class="mb-1 flex items-center justify-between gap-3">
                                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $paragraphIndex + 1 }}</label>
                                                        <button type="button" wire:click="removeTranslationListItem('detail_sections.{{ $sectionIndex }}.after_list', {{ $paragraphIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                                    </div>
                                                    <textarea rows="4" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.after_list.{{ $paragraphIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" wire:click="addTranslationListItem('detail_sections.{{ $sectionIndex }}.after_list')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                {{ __('Add Paragraph') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Downloads') }}</p>
                                        <div class="mt-3 space-y-3">
                                            @foreach (($section['downloads'] ?? []) as $downloadIndex => $download)
                                                <div class="rounded-xl border border-slate-200 bg-white p-3">
                                                    <div class="grid gap-3 lg:grid-cols-[1fr_1.2fr_180px_auto] lg:items-end">
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.downloads.{{ $downloadIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.downloads.{{ $downloadIndex }}.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Button Label') }}</label>
                                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.downloads.{{ $downloadIndex }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                                        </div>
                                                        <div class="flex justify-end">
                                                            <button type="button" wire:click="removeTranslationListItem('detail_sections.{{ $sectionIndex }}.downloads', {{ $downloadIndex }})" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" wire:click="addTranslationListItem('detail_sections.{{ $sectionIndex }}.downloads', 'download_item')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                {{ __('Add Download') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Quote') }}</label>
                                        <textarea rows="3" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.quote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div class="mt-4">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA Text') }}</label>
                                        <textarea rows="2" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.cta_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA Label') }}</label>
                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('CTA URL') }}</label>
                                            <input type="text" wire:model="form.translation_payload.detail_sections.{{ $sectionIndex }}.cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="accounting-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Meeting Block') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Title') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        @foreach (($translationPayload['meeting']['visit_lines'] ?? []) as $index => $line)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line') }} #{{ $index + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('meeting.visit_lines', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('meeting.visit_lines')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add Visit Line') }}
                            </button>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Submit Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('First Name Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Last Name Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subject Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Message Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div id="accounting-videos-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Video Block') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.video_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('form.translation_payload.video_section.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.video_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            @error('form.translation_payload.video_section.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            {{ __('Same video list and YouTube source links are managed under the Sources tab.') }}
                        </div>
                    </div>

                    <div id="accounting-blog-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title">{{ __('Blog Block') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Empty State') }}</label>
                            <textarea rows="3" wire:model="form.translation_payload.blog_section.empty" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </div>
            @elseif ($isEuFundsTemplate)
                @include('livewire.admin.content.service.partials.eu-funds-editor')
            @else
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Audience & FFI') }}</p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Audience Headline') }}</label>
                    <textarea rows="3" wire:model="form.translation_payload.audience.headline" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach (($translationPayload['audience']['cards'] ?? []) as $index => $card)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="grid gap-3 md:grid-cols-[1fr_220px]">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card Title') }} #{{ $index + 1 }}</label>
                                    <input type="text" wire:model="form.translation_payload.audience.cards.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Icon') }}</label>
                                    <select wire:model="form.translation_payload.audience.cards.{{ $index }}.icon" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        @foreach ($audienceIconOptions as $iconKey => $iconLabel)
                                            <option value="{{ $iconKey }}">{{ $iconLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card Text') }}</label>
                                <textarea rows="5" wire:model="form.translation_payload.audience.cards.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('FFI Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.ffi.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('FFI Logo Alt') }}</label>
                        <input type="text" wire:model="form.translation_payload.ffi.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('FFI Body') }}</label>
                    <textarea rows="4" wire:model="form.translation_payload.ffi.body.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('FAQ Intro Block') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                        <input type="text" wire:model="form.translation_payload.what_we_do.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.what_we_do.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                    <textarea rows="5" wire:model="form.translation_payload.what_we_do.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Advisory Approach') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                        <input type="text" wire:model="form.translation_payload.advisory.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Box Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.advisory.box_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                    <input type="text" wire:model="form.translation_payload.advisory.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                    <textarea rows="5" wire:model="form.translation_payload.advisory.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach (($translationPayload['advisory']['items'] ?? []) as $index => $item)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Advisory Item') }} #{{ $index + 1 }}</p>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Lead') }}</label>
                                <input type="text" wire:model="form.translation_payload.advisory.items.{{ $index }}.lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Body') }}</label>
                                <textarea rows="4" wire:model="form.translation_payload.advisory.items.{{ $index }}.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Capability Sections') }}</p>

                <div class="mt-4 space-y-5">
                    @foreach (($translationPayload['capabilities'] ?? []) as $sectionIndex => $capability)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="grid gap-3 md:grid-cols-[1fr_220px]">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }} #{{ $sectionIndex + 1 }}</label>
                                    <input type="text" wire:model="form.translation_payload.capabilities.{{ $sectionIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Icon') }}</label>
                                    <select wire:model="form.translation_payload.capabilities.{{ $sectionIndex }}.icon" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        @foreach ($capabilityIconOptions as $iconKey => $iconLabel)
                                            <option value="{{ $iconKey }}">{{ $iconLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                                <textarea rows="4" wire:model="form.translation_payload.capabilities.{{ $sectionIndex }}.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Text') }}</label>
                                <textarea rows="4" wire:model="form.translation_payload.capabilities.{{ $sectionIndex }}.help" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                                @foreach (($capability['items'] ?? []) as $itemIndex => $item)
                                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item Title') }} #{{ $itemIndex + 1 }}</label>
                                        <input type="text" wire:model="form.translation_payload.capabilities.{{ $sectionIndex }}.items.{{ $itemIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item Text') }}</label>
                                        <textarea rows="5" wire:model="form.translation_payload.capabilities.{{ $sectionIndex }}.items.{{ $itemIndex }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Capability CTA') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                        <input type="text" wire:model="form.translation_payload.capability_cta.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Button Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.capability_cta.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Team Section') }}</p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                        <input type="text" wire:model="form.translation_payload.team_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.team_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                        <textarea rows="4" wire:model="form.translation_payload.team_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Blog Section') }}</p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                        <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Use :category placeholder if you want the current blog category name inserted automatically.') }}</p>
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                        <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Meeting Section') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
                    <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 1') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Visit Line 2') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Submit Label') }}</label>
                    <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Phone Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Direct Email Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Form Labels') }}</p>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('First Name') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Last Name') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Company') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subject Label') }}</label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Message Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>
            @endif
        @endif

        @if ($activeTab === 'sources' && $templateSupportsSources)
            <div class="grid gap-6 {{ $templateSupportsFaqSource ? 'xl:grid-cols-2' : '' }}">
                @if ($templateSupportsBlogSource)
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Blog Feed') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Mode') }}</label>
                            <select wire:model.live="form.page_payload.blog_source.mode" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="auto_category">
                                    {{ $blogAutoCategoryLabel }}
                                </option>
                                <option value="category">{{ __('Specific blog category') }}</option>
                                <option value="manual">{{ __('Manual post selection') }}</option>
                            </select>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Limit') }}</label>
                            <input type="number" min="1" max="24" wire:model="form.page_payload.blog_source.limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[12rem]" />
                        </div>

                        @if (($pagePayload['blog_source']['mode'] ?? 'auto_category') === 'category')
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog Category') }}</label>
                                <select wire:model="form.page_payload.blog_source.category_id" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="">{{ __('Select category') }}</option>
                                    @foreach ($this->blogCategoryOptions as $row)
                                        <option value="{{ $row['id'] }}">{{ $row['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if (($pagePayload['blog_source']['mode'] ?? 'auto_category') === 'manual')
                            <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Available Posts') }}</label>
                                    <select wire:model="blogPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <option value="">{{ __('Select post...') }}</option>
                                        @foreach ($this->blogPostOptions as $row)
                                            <option value="{{ $row['id'] }}">{{ $row['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" wire:click="addManualItem('blog_posts', {{ (int) ($blogPickerId ?? 0) }})" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                    {{ __('Add') }}
                                </button>
                            </div>

                            <div class="mt-4 space-y-2">
                                @forelse ($this->selectedBlogPosts as $row)
                                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                        <div class="text-sm text-slate-800">{{ $row['label'] }}</div>
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button" wire:click="moveManualItemUp('blog_posts', {{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                            <button type="button" wire:click="moveManualItemDown('blog_posts', {{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                            <button type="button" wire:click="removeManualItem('blog_posts', {{ $row['id'] }})" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('No blog posts selected.') }}</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @endif

                @if ($templateSupportsFaqSource)
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('FAQ Feed') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Mode') }}</label>
                            <select wire:model.live="form.page_payload.faq_source.mode" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="auto_group">{{ __('Auto (family-business FAQ group)') }}</option>
                                <option value="group">{{ __('Specific FAQ group') }}</option>
                                <option value="manual">{{ __('Manual FAQ selection') }}</option>
                            </select>
                        </div>

                        @if (($pagePayload['faq_source']['mode'] ?? 'auto_group') === 'group')
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('FAQ Group') }}</label>
                                <select wire:model="form.page_payload.faq_source.group_code" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="">{{ __('Select group') }}</option>
                                    @foreach ($this->faqGroupOptions as $row)
                                        <option value="{{ $row['id'] }}">{{ $row['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if (($pagePayload['faq_source']['mode'] ?? 'auto_group') === 'manual')
                            <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Available FAQs') }}</label>
                                    <select wire:model="faqPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <option value="">{{ __('Select FAQ...') }}</option>
                                        @foreach ($this->faqOptions as $row)
                                            <option value="{{ $row['id'] }}">{{ $row['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" wire:click="addManualItem('faqs', {{ (int) ($faqPickerId ?? 0) }})" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                    {{ __('Add') }}
                                </button>
                            </div>

                            <div class="mt-4 space-y-2">
                                @forelse ($this->selectedFaqs as $row)
                                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                        <div class="text-sm text-slate-800">{{ $row['label'] }}</div>
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button" wire:click="moveManualItemUp('faqs', {{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                            <button type="button" wire:click="moveManualItemDown('faqs', {{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                            <button type="button" wire:click="removeManualItem('faqs', {{ $row['id'] }})" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('No FAQs selected.') }}</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="admin-panel admin-form-panel p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="admin-section-title">{{ __('Video sadržaj') }}</p>
                        <button type="button" wire:click="addVideoSource" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                            {{ __('Dodaj video') }}
                        </button>
                    </div>

                    <p class="mt-2 text-sm text-slate-500">{{ __('Unesi naslov videa i YouTube link. Redoslijed ovdje je isti kao na stranici, a block se prikazuje prije kontakt forme ako postoji barem jedan video.') }}</p>

                    <div class="mt-4 space-y-4">
                        @forelse ((array) ($pagePayload['video_source']['items'] ?? []) as $index => $video)
                            <div wire:key="service-video-row-{{ $index }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="grid gap-3 md:grid-cols-[1fr_1.2fr_auto] md:items-start">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov videa') }}</label>
                                        <input type="text" wire:model="form.page_payload.video_source.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @error('form.page_payload.video_source.items.'.$index.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('YouTube URL') }}</label>
                                        <input type="text" wire:model="form.page_payload.video_source.items.{{ $index }}.youtube_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="https://www.youtube.com/watch?v=..." />
                                        @error('form.page_payload.video_source.items.'.$index.'.youtube_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex flex-wrap gap-2 md:justify-end">
                                        <button type="button" wire:click="moveVideoSourceUp({{ $index }})" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                        <button type="button" wire:click="moveVideoSourceDown({{ $index }})" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                        <button type="button" wire:click="removeVideoSource({{ $index }})" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('Nema unesenih videa.') }}</div>
                        @endforelse
                    </div>
                </div>

                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title">{{ __('Copy Sekcije Videa') }}</p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.video_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500">{{ __('Ako ostane prazno, frontend prikazuje samo videe bez naslova.') }}</p>
                        @error('form.translation_payload.video_section.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Intro') }}</label>
                        <textarea rows="4" wire:model="form.translation_payload.video_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        @error('form.translation_payload.video_section.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            @if ($templateSupportsTeamSource || $templateSupportsBrochure)
                <div class="grid gap-6 xl:grid-cols-2">
                    @if ($templateSupportsTeamSource)
                        <div class="admin-panel admin-form-panel p-6">
                            <p class="admin-section-title">{{ __('Team Feed') }}</p>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Mode') }}</label>
                                <select wire:model.live="form.page_payload.team_source.mode" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="auto">{{ __('Auto (existing family-business logic)') }}</option>
                                    <option value="manual">{{ __('Manual team selection') }}</option>
                                </select>
                            </div>

                            @if (($pagePayload['team_source']['mode'] ?? 'auto') === 'manual')
                                <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Available Team Members') }}</label>
                                        <select wire:model="teamPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                            <option value="">{{ __('Select team member...') }}</option>
                                            @foreach ($this->teamOptions as $row)
                                                <option value="{{ $row['id'] }}">{{ $row['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" wire:click="addManualItem('team_members', {{ (int) ($teamPickerId ?? 0) }})" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                        {{ __('Add') }}
                                    </button>
                                </div>

                                <div class="mt-4 space-y-2">
                                    @forelse ($this->selectedTeamMembers as $row)
                                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                            <div class="text-sm text-slate-800">{{ $row['label'] }}</div>
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button" wire:click="moveManualItemUp('team_members', {{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                                <button type="button" wire:click="moveManualItemDown('team_members', {{ $row['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                                <button type="button" wire:click="removeManualItem('team_members', {{ $row['id'] }})" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('No team members selected.') }}</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($templateSupportsBrochure)
                        <div class="admin-panel admin-form-panel p-6">
                            <p class="admin-section-title">{{ __('Assets & Links') }}</p>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Brochure Button Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.brochure_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Brochure URL Override') }}</label>
                                <input type="text" wire:model="form.page_payload.brochure_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('Leave empty to keep the current brochure asset.') }}" />
                                <p class="mt-1 text-xs text-slate-500">{{ __('This can be a relative public path or a full URL.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        @if ($activeTab === 'seo')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('SEO') }}</p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Title') }}</label>
                    <input type="text" wire:model="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Description') }}</label>
                    <textarea rows="4" wire:model="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        @endif

        @if ($activeTab === 'media')
            <livewire:admin.media.manager
                :model-class="\App\Models\Content\Service\ServicePage::class"
                :model-id="$servicePageId"
                :locale="$form['locale']"
                :wire:key="'service-page-media-manager-'.($servicePageId ?? 'new').'-'.$form['locale']"
            />
        @endif

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ $isEdit ? __('Update Service Page') : __('Create Service Page') }}
            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Cancel') }}
            </button>
        </div>
    </form>
</div>
