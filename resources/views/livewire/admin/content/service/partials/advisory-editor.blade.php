<div class="admin-panel admin-form-panel p-6">
    <p class="admin-section-title">{{ __('Advisory Navigator') }}</p>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ($advisoryEditorSections as $sectionId => $sectionLabel)
            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
        @endforeach
    </div>
    <p class="mt-4 text-sm text-slate-600">
        {{ __('Savjetovanje je krovna usluga za financijsko savjetovanje, porezno savjetovanje i pribavljanje financiranja. Tekst ispod je zadani sadržaj iz redesign briefa i može se uređivati po sekcijama.') }}
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
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                <textarea rows="5" wire:model="form.translation_payload.overview.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach
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
    <p class="admin-section-title">{{ __('Service Cards') }}</p>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @foreach (($translationPayload['service_cards'] ?? []) as $index => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card') }} #{{ $index + 1 }}</p>
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
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
        <input type="text" wire:model="form.translation_payload.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>
    @foreach (($translationPayload['pandea']['body'] ?? []) as $index => $paragraph)
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
            <textarea rows="5" wire:model="form.translation_payload.pandea.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    @endforeach
</div>

<div id="advisory-funding-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Funding') }}</p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
            <input type="text" wire:model="form.translation_payload.funding.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Title') }}</label>
            <input type="text" wire:model="form.translation_payload.funding.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    @foreach (($translationPayload['funding']['overview_body'] ?? []) as $index => $paragraph)
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Paragraph') }} #{{ $index + 1 }}</label>
            <textarea rows="5" wire:model="form.translation_payload.funding.overview_body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    @endforeach

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        @foreach (($translationPayload['funding']['cards'] ?? []) as $index => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Funding Card') }} #{{ $index + 1 }}</p>
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
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Funding Service') }} #{{ $index + 1 }}</p>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                <input type="text" wire:model="form.translation_payload.funding.services.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                <textarea rows="4" wire:model="form.translation_payload.funding.services.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach
    </div>
</div>

<div id="advisory-transactions-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Transactions / Finance Details') }}</p>

    <div class="mt-4 grid gap-6 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Bank Loans Title') }}</label>
            <input type="text" wire:model="form.translation_payload.bank_loans.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @foreach (($translationPayload['bank_loans']['body'] ?? []) as $index => $paragraph)
                <textarea rows="4" wire:model="form.translation_payload.bank_loans.body.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            @endforeach
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('ZoPU Title') }}</label>
            <input type="text" wire:model="form.translation_payload.zopu.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @foreach (($translationPayload['zopu']['body'] ?? []) as $index => $paragraph)
                <textarea rows="4" wire:model="form.translation_payload.zopu.body.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('M&A Title') }}</label>
            <input type="text" wire:model="form.translation_payload.ma.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('M&A Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.ma.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sale Title') }}</label>
            <input type="text" wire:model="form.translation_payload.ma.sale.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <textarea rows="5" wire:model="form.translation_payload.ma.sale.body" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Acquisition Title') }}</label>
            <input type="text" wire:model="form.translation_payload.ma.acquisition.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <textarea rows="5" wire:model="form.translation_payload.ma.acquisition.body" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Due Diligence Title') }}</label>
            <input type="text" wire:model="form.translation_payload.due_diligence.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <textarea rows="5" wire:model="form.translation_payload.due_diligence.intro" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Help Title') }}</label>
            <input type="text" wire:model="form.translation_payload.due_diligence.help_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @foreach (($translationPayload['due_diligence']['help_items'] ?? []) as $index => $item)
                <input type="text" wire:model="form.translation_payload.due_diligence.help_items.{{ $index }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @endforeach
            <textarea rows="4" wire:model="form.translation_payload.due_diligence.closing" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Valuations Title') }}</label>
        <input type="text" wire:model="form.translation_payload.valuations.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        @foreach (($translationPayload['valuations']['body'] ?? []) as $index => $paragraph)
            <textarea rows="4" wire:model="form.translation_payload.valuations.body.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        @endforeach
        <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Methods Title') }}</label>
        <input type="text" wire:model="form.translation_payload.valuations.methods_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        @foreach (($translationPayload['valuations']['methods'] ?? []) as $index => $method)
            <input type="text" wire:model="form.translation_payload.valuations.methods.{{ $index }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        @endforeach
    </div>
</div>

<div id="advisory-tax-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Tax Advisory') }}</p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.tax.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Overview Title') }}</label>
            <input type="text" wire:model="form.translation_payload.tax.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    @foreach (($translationPayload['tax']['overview_body'] ?? []) as $index => $paragraph)
        <textarea rows="5" wire:model="form.translation_payload.tax.overview_body.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    @endforeach

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        @foreach (($translationPayload['tax']['services'] ?? []) as $index => $service)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Tax Service') }} #{{ $index + 1 }}</p>
                <input type="text" wire:model="form.translation_payload.tax.services.{{ $index }}.title" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <textarea rows="4" wire:model="form.translation_payload.tax.services.{{ $index }}.text" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Approach Title') }}</label>
        <input type="text" wire:model="form.translation_payload.tax.approach_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        @foreach (($translationPayload['tax']['approach_body'] ?? []) as $index => $paragraph)
            <textarea rows="4" wire:model="form.translation_payload.tax.approach_body.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        @endforeach
    </div>
</div>

<div id="advisory-modules-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Funding Source Modules') }}</p>
    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <input type="text" wire:model="form.translation_payload.source_modules.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        <input type="text" wire:model="form.translation_payload.source_modules.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>
    <textarea rows="4" wire:model="form.translation_payload.source_modules.intro" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @foreach (($translationPayload['source_modules']['items'] ?? []) as $index => $module)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Module') }} #{{ $index + 1 }}</p>
                <input type="text" wire:model="form.translation_payload.source_modules.items.{{ $index }}.title" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <textarea rows="4" wire:model="form.translation_payload.source_modules.items.{{ $index }}.text" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                <input type="text" wire:model="form.translation_payload.source_modules.items.{{ $index }}.url" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        @endforeach
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div class="admin-panel admin-form-panel p-6">
        <p class="admin-section-title">{{ __('Approach') }}</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <input type="text" wire:model="form.translation_payload.approach.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        @foreach (($translationPayload['approach']['body'] ?? []) as $index => $paragraph)
            <textarea rows="4" wire:model="form.translation_payload.approach.body.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        @endforeach
    </div>

    <div id="advisory-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Meeting / Blog') }}</p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />

        <div class="mt-6 grid gap-3 md:grid-cols-2">
            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>
</div>
