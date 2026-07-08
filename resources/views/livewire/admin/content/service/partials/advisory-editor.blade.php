@php
    $advisoryEditorSections = [
        'advisory-overview-admin' => 'Overview',
        'advisory-services-admin' => 'Services',
        'advisory-pandea-admin' => 'Pandea',
        'advisory-funding-admin' => 'Pribavljanje financiranja',
        'advisory-bank-loans-admin' => 'Bankovni krediti',
        'advisory-zopu-admin' => 'Zakon o poticanju ulaganja',
        'advisory-ma-admin' => 'M&A',
        'advisory-due-diligence-admin' => 'Due Diligence',
        'advisory-valuations-admin' => 'Procjene vrijednosti',
        'advisory-tax-admin' => 'Porezno savjetovanje',
        'advisory-approach-admin' => 'Approach',
        'advisory-meeting-admin' => 'Meeting',
    ];

    $advisoryDetailSections = [
        'bank_loans' => [
            'id' => 'advisory-bank-loans-admin',
            'label' => 'Bankovni krediti',
        ],
        'zopu' => [
            'id' => 'advisory-zopu-admin',
            'label' => 'Zakon o poticanju ulaganja',
        ],
        'ma' => [
            'id' => 'advisory-ma-admin',
            'label' => 'Prodaja i kupnja poduzeca (M&A)',
        ],
        'due_diligence' => [
            'id' => 'advisory-due-diligence-admin',
            'label' => 'Dubinska snimanja (Due Diligence)',
        ],
        'valuations' => [
            'id' => 'advisory-valuations-admin',
            'label' => 'Procjena vrijednosti drustva',
        ],
        'tax' => [
            'id' => 'advisory-tax-admin',
            'label' => 'Porezno savjetovanje',
        ],
    ];
@endphp

<div class="admin-panel admin-form-panel p-6">
    <p class="admin-section-title">{{ __('Advisory Navigator') }}</p>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ($advisoryEditorSections as $sectionId => $sectionLabel)
            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
        @endforeach
    </div>
    <p class="mt-4 text-sm text-slate-600">
        {{ __('Savjetovanje je krovna usluga. Podstranice su prikazane ispod i uređuju isti zapis, ali svaki blok odgovara jednoj front ruti.') }}
    </p>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="advisory-overview-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Overview') }}</p>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>

        @foreach (($translationPayload['overview']['body'] ?? []) as $index => $paragraph)
            <div class="mt-3">
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                    <button type="button" wire:click="removeTranslationListItem('overview.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <textarea rows="5" wire:model="form.translation_payload.overview.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Add Paragraph') }}
            </button>
        </div>
    </div>

    <div id="advisory-services-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Services Intro') }}</p>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                <input type="text" wire:model="form.translation_payload.services_intro.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.services_intro.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="5" wire:model="form.translation_payload.services_intro.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div>

<div class="admin-panel admin-form-panel p-6">
    <div class="flex items-center justify-between gap-3">
        <p class="admin-section-title">{{ __('Service Cards') }}</p>
        <button type="button" wire:click="addTranslationListItem('service_cards', 'service_card')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            {{ __('Add Card') }}
        </button>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @foreach (($translationPayload['service_cards'] ?? []) as $index => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card') }} #{{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('service_cards', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <div class="mt-3 grid gap-3 md:grid-cols-[1fr_1fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.service_cards.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                        <input type="text" wire:model="form.translation_payload.service_cards.{{ $index }}.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                    <textarea rows="4" wire:model="form.translation_payload.service_cards.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="advisory-pandea-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Pandea') }}</p>
    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Logo Alt') }}</label>
            <input type="text" wire:model="form.translation_payload.pandea.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
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
</div>

<div id="advisory-funding-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Pribavljanje financiranja') }}</p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.funding.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Title') }}</label>
            <input type="text" wire:model="form.translation_payload.funding.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
        <textarea rows="4" wire:model="form.translation_payload.funding.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    @foreach (($translationPayload['funding']['overview_body'] ?? []) as $index => $paragraph)
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Paragraph') }} #{{ $index + 1 }}</label>
                <button type="button" wire:click="removeTranslationListItem('funding.overview_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
            </div>
            <textarea rows="4" wire:model="form.translation_payload.funding.overview_body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    @endforeach

    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('funding.overview_body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            {{ __('Add Overview Paragraph') }}
        </button>
    </div>

    <div class="mt-6 flex items-center justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Funding Cards') }}</p>
        <button type="button" wire:click="addTranslationListItem('funding.cards', 'service_card')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            {{ __('Add Card') }}
        </button>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-3">
        @foreach (($translationPayload['funding']['cards'] ?? []) as $index => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card') }} #{{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('funding.cards', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.funding.cards.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                <textarea rows="4" wire:model="form.translation_payload.funding.cards.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                <input type="text" wire:model="form.translation_payload.funding.cards.{{ $index }}.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Services Title') }}</label>
        <input type="text" wire:model="form.translation_payload.funding.services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @foreach (($translationPayload['funding']['services'] ?? []) as $index => $item)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Funding Service') }} #{{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('funding.services', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.funding.services.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                <textarea rows="4" wire:model="form.translation_payload.funding.services.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach
    </div>
    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('funding.services', 'title_text')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            {{ __('Add Service') }}
        </button>
    </div>
</div>

@foreach ($advisoryDetailSections as $detailKey => $detailConfig)
    @php $detail = (array) ($translationPayload[$detailKey] ?? []); @endphp
    <div id="{{ $detailConfig['id'] }}" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="admin-section-title">{{ $detailConfig['label'] }}</p>
            <span class="admin-chip">{{ $detailKey }}</span>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Title') }}</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>

        @if ($detailKey === 'ma')
            <div class="mt-4">
                <button
                    type="button"
                    wire:click="$toggle('form.translation_payload.ma.show_pandea')"
                    class="admin-switch"
                    data-state="{{ data_get($translationPayload, 'ma.show_pandea') ? 'on' : 'off' }}"
                    role="switch"
                    aria-checked="{{ data_get($translationPayload, 'ma.show_pandea') ? 'true' : 'false' }}"
                    aria-label="{{ __('Toggle Pandea block on M&A page') }}"
                >
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label">{{ data_get($translationPayload, 'ma.show_pandea') ? __('Pandea visible') : __('Pandea hidden') }}</span>
                </button>
            </div>
        @endif

        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Paragraphs') }}</p>
                <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.overview_body')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    {{ __('Add Paragraph') }}
                </button>
            </div>
            <div class="space-y-3">
                @foreach (($detail['overview_body'] ?? []) as $index => $paragraph)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                            <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.overview_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.{{ $detailKey }}.overview_body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Services Title') }}</label>
            <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Services Paragraphs') }}</p>
                <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.services_body')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    {{ __('Add Paragraph') }}
                </button>
            </div>
            <div class="space-y-3">
                @foreach (($detail['services_body'] ?? []) as $index => $paragraph)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                            <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.services_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.{{ $detailKey }}.services_body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Title') }}</label>
            <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.help_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Items') }}</p>
                <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.help_items')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    {{ __('Add Item') }}
                </button>
            </div>
            <div class="grid gap-3 lg:grid-cols-3">
                @foreach (($detail['help_items'] ?? []) as $index => $item)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Item') }} #{{ $index + 1 }}</label>
                            <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.help_items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                        </div>
                        <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.help_items.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Approach Title') }}</label>
            <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.approach_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Approach Paragraphs') }}</p>
                <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.approach_body')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    {{ __('Add Paragraph') }}
                </button>
            </div>
            <div class="space-y-3">
                @foreach (($detail['approach_body'] ?? []) as $index => $paragraph)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                            <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.approach_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.{{ $detailKey }}.approach_body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

<div class="grid gap-6 xl:grid-cols-2">
    <div id="advisory-approach-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Approach') }}</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                <input type="text" wire:model="form.translation_payload.approach.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        @foreach (($translationPayload['approach']['body'] ?? []) as $index => $paragraph)
            <div class="mt-3">
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                    <button type="button" wire:click="removeTranslationListItem('approach.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <textarea rows="4" wire:model="form.translation_payload.approach.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach
        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('approach.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Add Paragraph') }}
            </button>
        </div>
    </div>

    <div id="advisory-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Meeting / Blog') }}</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meeting Kicker') }}</label>
                <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meeting Title') }}</label>
                <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meeting Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact Title') }}</label>
            <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-6 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog Kicker') }}</label>
                <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog Title') }}</label>
                <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div>
