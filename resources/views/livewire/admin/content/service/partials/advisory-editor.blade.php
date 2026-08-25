@php
    $advisoryDetailSections = [
        'financial' => [
            'id' => 'advisory-financial-admin',
            'number' => 5,
            'title' => 'Financijsko savjetovanje',
            'route' => 'advisory.finance.show',
        ],
        'bank_loans' => [
            'id' => 'advisory-bank-loans-admin',
            'number' => 7,
            'title' => 'Bankovni krediti',
            'route' => 'advisory.bank-loans.show',
        ],
        'zopu' => [
            'id' => 'advisory-zopu-admin',
            'number' => 8,
            'title' => 'Zakon o poticanju ulaganja',
            'route' => 'advisory.investment-incentives.show',
        ],
        'ma' => [
            'id' => 'advisory-ma-admin',
            'number' => 9,
            'title' => 'Prodaja i kupnja poduzeća (M&A)',
            'route' => 'advisory.ma.show',
        ],
        'due_diligence' => [
            'id' => 'advisory-due-diligence-admin',
            'number' => 10,
            'title' => 'Dubinska snimanja (Due Diligence)',
            'route' => 'advisory.due-diligence.show',
        ],
        'valuations' => [
            'id' => 'advisory-valuations-admin',
            'number' => 11,
            'title' => 'Procjena vrijednosti društva',
            'route' => 'advisory.valuations.show',
        ],
        'tax' => [
            'id' => 'advisory-tax-admin',
            'number' => 12,
            'title' => 'Porezno savjetovanje',
            'route' => 'advisory.tax.show',
        ],
    ];

    $advisoryHeroUpload = $advisoryHeroImageUpload ?? null;
    $advisoryHeroPreviewUrl = $advisoryHeroUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
        ? $advisoryHeroUpload->temporaryUrl()
        : (string) ($advisoryHeroImage['url'] ?? '');
    $advisoryLogoUpload = $advisoryPandeaLogoUpload ?? null;
    $advisoryLogoPreviewUrl = $advisoryLogoUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
        ? $advisoryLogoUpload->temporaryUrl()
        : (string) ($advisoryPandeaLogo['url'] ?? '');
    $advisoryEditorPages = [
        'main' => ['title' => 'Savjetovanje', 'route' => 'advisory.show'],
        'funding' => ['title' => 'Pribavljanje financiranja', 'route' => 'advisory.funding.show'],
        ...collect($advisoryDetailSections)->map(fn (array $section): array => [
            'title' => $section['title'],
            'route' => $section['route'],
        ])->all(),
    ];
    $currentAdvisoryEditor = $advisoryEditorPages[$contentSection] ?? $advisoryEditorPages['main'];
@endphp

<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">Sadržaj s fronta</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">{{ $currentAdvisoryEditor['title'] }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Ovdje uređujete samo sadržaj koji se prikazuje na ovoj konkretnoj front stranici.
            </p>
        </div>

        <a href="{{ route($currentAdvisoryEditor['route']) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50">
            Otvori ovu stranicu
            <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>
</div>

@if ($contentSection === 'main')
<div id="advisory-main-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. Glavna stranica</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">Hero i uvod stranice Savjetovanje</h2>
            <p class="mt-1 text-sm text-slate-600">Glavna slika koristi se i na podstranicama; svaka podstranica ima vlastiti alternativni opis slike.</p>
        </div>
        <a href="{{ route('advisory.show') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-800 hover:text-cyan-950">
            Otvori front <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naziv usluge u hero sekciji</label>
                <input type="text" wire:model="form.translation_payload.hero.subtitle_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                @error('form.translation_payload.hero.subtitle_lead') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavna hero poruka</label>
                <textarea rows="5" wire:model="form.translation_payload.hero.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
                @error('form.translation_payload.hero.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis glavne slike</label>
                <input type="text" wire:model="form.translation_payload.hero.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                @error('form.translation_payload.hero.image_alt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                @if ($advisoryHeroPreviewUrl !== '')
                    <img src="{{ $advisoryHeroPreviewUrl }}" alt="" class="aspect-video w-full object-cover" />
                @endif
            </div>
            <div class="mt-3 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Zajednička hero slika</label>
                <span class="admin-chip">{{ ($advisoryHeroImage['is_custom'] ?? false) ? 'Vlastita slika' : 'Zadana slika' }}</span>
            </div>
            <input type="file" wire:model="advisoryHeroImageUpload" accept="{{ $serviceImageAccept }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200" />
            <p class="mt-1 text-xs text-slate-500">Preporučeni omjer 16:9. Promjena vrijedi na svim stranicama Savjetovanja.</p>
            @error('advisoryHeroImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            @if (($advisoryHeroImage['is_custom'] ?? false) && ! $advisoryHeroUpload)
                <button type="button" wire:click="removeAdvisoryHeroImage" wire:confirm="Ukloniti vlastitu hero sliku i vratiti zadanu?" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700">Vrati zadanu sliku</button>
            @endif
        </div>
    </div>

    <div class="mt-6 border-t border-slate-200 pt-5">
        <h3 class="text-base font-semibold text-slate-900">Uvodna 50/50 sekcija</h3>
        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst uvodne sekcije</label>
            <textarea
                rows="10"
                wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"
                data-quill-editor
                data-quill-profile="service-text"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
            ></textarea>
            <p class="mt-1 text-xs text-slate-500">Sve odlomke uređujete u jednom editoru. Zadnji odlomak ostaje vizualno istaknut na frontu.</p>
        </div>
    </div>
</div>

<div id="advisory-pandea-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. Pandea</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Pandea Global M&amp;A blok</h2>
        <p class="mt-1 text-sm text-slate-600">Ovaj sadržaj vrijedi samo za glavnu stranicu Savjetovanja. M&amp;A podstranica ima vlastiti Pandea editor.</p>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.translation_payload.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst Pandea bloka</label>
                <textarea
                    rows="12"
                    wire:model.live.debounce.300ms="form.translation_payload.pandea.body_html"
                    data-quill-editor
                    data-quill-profile="service-text"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                ></textarea>
            </div>
        </div>

        <div>
            <div class="flex min-h-48 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-6">
                @if ($advisoryLogoPreviewUrl !== '')
                    <img src="{{ $advisoryLogoPreviewUrl }}" alt="" class="max-h-32 max-w-full object-contain" />
                @endif
            </div>
            <div class="mt-3 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Pandea logotip</label>
                <span class="admin-chip">{{ ($advisoryPandeaLogo['is_custom'] ?? false) ? 'Vlastiti logotip' : 'Zadani logotip' }}</span>
            </div>
            <input type="file" wire:model="advisoryPandeaLogoUpload" accept="{{ $serviceImageAccept }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200" />
            @error('advisoryPandeaLogoUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            @if (($advisoryPandeaLogo['is_custom'] ?? false) && ! $advisoryLogoUpload)
                <button type="button" wire:click="removeAdvisoryPandeaLogo" wire:confirm="Ukloniti vlastiti logotip i vratiti zadani?" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700">Vrati zadani logotip</button>
            @endif
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis logotipa</label>
                <input type="text" wire:model="form.translation_payload.pandea.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
    </div>
</div>

<div id="advisory-services-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">3. Kartice usluga</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Usluge prikazane na glavnoj stranici</h2>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.translation_payload.services_intro.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod sekcije</label>
            <textarea rows="3" wire:model="form.translation_payload.services_intro.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis poveznice na kartici</label>
            <input type="text" wire:model="form.translation_payload.services_intro.card_action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        @foreach (($translationPayload['service_cards'] ?? []) as $index => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica usluge {{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('service_cards', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                </div>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                        <input type="text" wire:model="form.translation_payload.service_cards.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Poveznica</label>
                        <input type="text" wire:model="form.translation_payload.service_cards.{{ $index }}.url" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                    </div>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label>
                    <textarea rows="5" wire:model="form.translation_payload.service_cards.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" wire:click="addTranslationListItem('service_cards', 'service_card')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj karticu usluge</button>
</div>

<div id="advisory-approach-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">4. Naš pristup</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Istaknuta sekcija glavne stranice</h2>
        <p class="mt-1 text-sm text-slate-600">Ovaj sadržaj vrijedi samo za glavnu stranicu Savjetovanja. Pribavljanje financiranja ima vlastiti editor pristupa.</p>
    </div>
    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
        <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst pristupa</label>
        <textarea
            rows="10"
            wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"
            data-quill-editor
            data-quill-profile="service-text"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
        ></textarea>
    </div>
</div>
@endif

@if ($contentSection === 'financial')
@include('livewire.admin.content.service.partials.advisory-detail-editor', [
    'detailKey' => 'financial',
    'detailConfig' => $advisoryDetailSections['financial'],
    'detail' => (array) ($translationPayload['financial'] ?? []),
])
@endif

@if ($contentSection === 'funding')
<div id="advisory-funding-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">6. Podstranica</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">Pribavljanje financiranja</h2>
            <p class="mt-1 text-sm text-slate-600">Ovaj blok uređuje samo sadržaj podstranice Pribavljanje financiranja.</p>
        </div>
        <a href="{{ route('advisory.funding.show') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-800 hover:text-cyan-950">Otvori front <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov podstranice i uvodne sekcije</label>
            <input type="text" wire:model="form.translation_payload.funding.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis hero slike</label>
            <input type="text" wire:model="form.translation_payload.funding.hero_image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="xl:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni tekst</label>
            <textarea rows="5" wire:model="form.translation_payload.funding.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div class="xl:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Poruka u hero sekciji</label>
            <textarea rows="4" wire:model="form.translation_payload.funding.hero_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov kartica izvora financiranja</label>
            <input type="text" wire:model="form.translation_payload.source_modules.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod iznad kartica</label>
            <textarea rows="3" wire:model="form.translation_payload.source_modules.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach (($translationPayload['funding']['cards'] ?? []) as $index => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica izvora {{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('funding.cards', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                </div>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                <input type="text" wire:model="form.translation_payload.funding.cards.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label>
                <textarea rows="5" wire:model="form.translation_payload.funding.cards.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Poveznica</label>
                <input type="text" wire:model="form.translation_payload.funding.cards.{{ $index }}.url" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
            </div>
        @endforeach
    </div>
    <button type="button" wire:click="addTranslationListItem('funding.cards', 'service_card')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj karticu izvora</button>

    <div class="mt-6 border-t border-slate-200 pt-5">
        <h3 class="text-base font-semibold text-slate-900">Naš pristup</h3>
        <div class="mt-4"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.funding.approach_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst pristupa</label>
            <textarea
                rows="10"
                wire:model.live.debounce.300ms="form.translation_payload.funding.approach_body_html"
                data-quill-editor
                data-quill-profile="service-text"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
            ></textarea>
        </div>
    </div>
</div>
@endif

@foreach ($advisoryDetailSections as $detailKey => $detailConfig)
    @continue($detailKey === 'financial' || $detailKey !== $contentSection)
    @php $detail = (array) ($translationPayload[$detailKey] ?? []); @endphp
    <div id="{{ $detailConfig['id'] }}" class="admin-panel admin-form-panel scroll-mt-24 p-6">
        <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">{{ $detailConfig['number'] }}. Podstranica</p>
                <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $detailConfig['title'] }}</h2>
                <p class="mt-1 text-sm text-slate-600">Zaseban CMS: promjene u ovom bloku vrijede samo na ovoj podstranici.</p>
            </div>
            <a href="{{ route($detailConfig['route']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-800 hover:text-cyan-950">Otvori front <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
        </div>

        <div class="mt-5 grid gap-4 xl:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov podstranice</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis hero slike</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.hero_image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Poruka u hero sekciji</label>
            <textarea rows="4" wire:model="form.translation_payload.{{ $detailKey }}.hero_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>

        @if ($detailKey === 'ma')
            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <button type="button" wire:click="$toggle('form.translation_payload.ma.show_pandea')" class="admin-switch" data-state="{{ data_get($translationPayload, 'ma.show_pandea') ? 'on' : 'off' }}" role="switch" aria-checked="{{ data_get($translationPayload, 'ma.show_pandea') ? 'true' : 'false' }}" aria-label="Uključi ili isključi Pandea blok na M&amp;A stranici">
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label">{{ data_get($translationPayload, 'ma.show_pandea') ? 'Pandea blok je prikazan' : 'Pandea blok je skriven' }}</span>
                </button>
            </div>

            <div class="mt-5 rounded-2xl border border-slate-200 p-5">
                <h3 class="text-base font-semibold text-slate-900">Pandea Global M&amp;A blok</h3>
                <div class="mt-4 grid gap-4 xl:grid-cols-2">
                    <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.ma.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
                    <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis logotipa</label><input type="text" wire:model="form.translation_payload.ma.pandea.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
                </div>
                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst Pandea bloka</label>
                    <textarea
                        rows="12"
                        wire:model.live.debounce.300ms="form.translation_payload.ma.pandea.body_html"
                        data-quill-editor
                        data-quill-profile="service-text"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                    ></textarea>
                </div>
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-slate-200 p-5">
            <h3 class="text-base font-semibold text-slate-900">Uvodna sekcija</h3>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst uvodne sekcije</label>
                <textarea
                    rows="10"
                    wire:model.live.debounce.300ms="form.translation_payload.{{ $detailKey }}.overview_body_html"
                    data-quill-editor
                    data-quill-profile="service-text"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                ></textarea>
            </div>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 p-5">
            <h3 class="text-base font-semibold text-slate-900">Naše usluge</h3>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni tekst usluga</label>
                <textarea
                    rows="10"
                    wire:model.live.debounce.300ms="form.translation_payload.{{ $detailKey }}.services_body_html"
                    data-quill-editor
                    data-quill-profile="service-text"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                ></textarea>
            </div>

            <div class="mt-6 border-t border-slate-200 pt-5">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartice usluga</label>
                <textarea rows="10" wire:model="form.translation_payload.{{ $detailKey }}.help_items_text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                <p class="mt-1 text-xs text-slate-500">Upišite jednu karticu po retku. Redoslijed redaka određuje redoslijed kartica na frontu.</p>
            </div>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 p-5">
            <h3 class="text-base font-semibold text-slate-900">Naš pristup</h3>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
                <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.approach_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst pristupa</label>
                <textarea
                    rows="10"
                    wire:model.live.debounce.300ms="form.translation_payload.{{ $detailKey }}.approach_body_html"
                    data-quill-editor
                    data-quill-profile="service-text"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                ></textarea>
            </div>
        </div>
    </div>
@endforeach

@if ($contentSection === 'main')
<div id="advisory-blog-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">13. Stručne objave</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Tekstovi blog sekcije</h2>
        <p class="mt-1 text-sm text-slate-600">Kategorija i objave biraju se u kartici Izvori. Ovi natpisi vrijede samo na glavnoj stranici Savjetovanja.</p>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis poveznice svih objava</label>
            <input type="text" wire:model="form.translation_payload.blog_section.all_posts_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis na kartici objave</label>
            <input type="text" wire:model="form.translation_payload.blog_section.post_action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
</div>
@endif

@if ($contentSection === 'main')
<div id="advisory-meeting-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">14. Kontaktni poziv</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Završna kontaktna sekcija</h2>
        <p class="mt-1 text-sm text-slate-600">Ovaj kontaktni sadržaj vrijedi samo na glavnoj stranici Savjetovanja.</p>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov</label>
            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov kontaktne kartice</label>
            <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="xl:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kontaktni tekst</label>
            <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis gumba</label>
            <input type="text" wire:model="form.translation_payload.meeting.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Napomena uz termin</label>
            <input type="text" wire:model="form.translation_payload.meeting.status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
</div>
@endif

@if ($contentSection !== 'main')
    @include('livewire.admin.content.service.partials.advisory-page-seo-editor', [
        'pageKey' => $contentSection,
    ])

    @include('livewire.admin.content.service.partials.advisory-page-ending-editor', [
        'pageKey' => $contentSection,
    ])
@endif
