@php
    $accountingEditorPayload = (array) ($translationPayload ?? []);
    $accountingEditorOverview = (array) ($accountingEditorPayload['overview'] ?? []);
    $accountingEditorApproach = (array) ($accountingEditorPayload['approach'] ?? []);
    $accountingEditorOverviewBody = array_values((array) ($accountingEditorOverview['body'] ?? []));
    $accountingEditorLocale = strtolower((string) data_get($form ?? [], 'locale', app()->getLocale()));
    $accountingEditorOverviewLead = str_starts_with($accountingEditorLocale, 'hr')
        ? 'Mirnije poslovanje počinje jasnim i pouzdanim brojkama.'
        : 'Calmer business operations begin with clear and reliable numbers.';
    $accountingEditorParagraphHtml = static function (mixed $paragraph, ?string $strongLead = null): string {
        $paragraph = trim((string) $paragraph);
        if ($paragraph === '') {
            return '';
        }

        if ($strongLead !== null && str_starts_with($paragraph, $strongLead)) {
            return '<p><strong>'.e($strongLead).'</strong>'.nl2br(e(\Illuminate\Support\Str::after($paragraph, $strongLead)), false).'</p>';
        }

        return '<p>'.nl2br(e($paragraph), false).'</p>';
    };
    $accountingEditorParagraphsHtml = static function (array $paragraphs) use ($accountingEditorParagraphHtml): string {
        return collect($paragraphs)
            ->map(static fn ($paragraph): string => $accountingEditorParagraphHtml($paragraph))
            ->filter()
            ->implode('');
    };
    $accountingEditorEnsureLeadStrong = static function (string $html, string $lead): string {
        if ($html === '' || $lead === '') {
            return $html;
        }

        return (string) preg_replace_callback(
            '/<p(\s[^>]*)?>\s*'.preg_quote(e($lead), '/').'/u',
            static fn (array $matches): string => '<p'.($matches[1] ?? '').'><strong>'.e($lead).'</strong>',
            $html,
            1,
        );
    };
    $accountingOverviewBodyEditorHtml = array_key_exists('body_html', $accountingEditorOverview)
        ? trim((string) $accountingEditorOverview['body_html'])
        : $accountingEditorParagraphHtml($accountingEditorOverview['intro'] ?? '')
            .$accountingEditorParagraphHtml($accountingEditorOverviewBody[0] ?? '', $accountingEditorOverviewLead);
    $accountingOverviewBodyEditorHtml = $accountingEditorEnsureLeadStrong(
        $accountingOverviewBodyEditorHtml,
        $accountingEditorOverviewLead,
    );
    $accountingPartnerBodyEditorHtml = array_key_exists('partner_body_html', $accountingEditorOverview)
        ? trim((string) $accountingEditorOverview['partner_body_html'])
        : $accountingEditorParagraphsHtml(array_slice($accountingEditorOverviewBody, 1));
    $accountingEditorApproachBody = array_values(array_filter(
        (array) ($accountingEditorApproach['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $accountingApproachBodyEditorHtml = array_key_exists('body_html', $accountingEditorApproach)
        ? trim((string) $accountingEditorApproach['body_html'])
        : $accountingEditorParagraphsHtml(
            $accountingEditorApproachBody !== []
                ? $accountingEditorApproachBody
                : [$accountingEditorApproach['intro'] ?? ''],
        );
@endphp

<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">Sadržaj s fronta</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Stranica Računovodstvo</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Sekcije su složene istim redom kao na frontu. Ovdje su samo tekstovi i slika koji se stvarno prikazuju na stranici Računovodstvo.
            </p>
        </div>

        <a
            href="{{ route('accounting.show') }}"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50"
        >
            Otvori front
            <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>

    <div class="mt-5 flex flex-wrap gap-2" aria-label="Navigacija po sekcijama stranice">
        <a href="#accounting-hero-admin" class="admin-chip">1. Hero i slika</a>
        <a href="#accounting-overview-admin" class="admin-chip">2. Zašto je računovodstvo bitno</a>
        <a href="#accounting-partner-admin" class="admin-chip">3. Partnerska poruka</a>
        <a href="#accounting-services-admin" class="admin-chip">4. Računovodstvene usluge</a>
        <a href="#accounting-approach-admin" class="admin-chip">5. Naš pristup</a>
        <a href="#accounting-blog-admin" class="admin-chip">6. Stručne objave</a>
        <a href="#accounting-meeting-admin" class="admin-chip">7. Kontaktni poziv</a>
        <a href="#accounting-settings-admin" class="admin-chip">8. Postavke stranice</a>
    </div>
</div>

<div id="accounting-hero-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. Hero i slika</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Vrh stranice Računovodstvo</h2>
        <p class="mt-1 text-sm text-slate-600">Uredite naziv usluge, glavnu poruku, pozadinsku sliku i opis slike.</p>
    </div>

    @php
        $accountingHeroUpload = $accountingHeroImageUpload ?? null;
        $accountingHeroPreviewUrl = $accountingHeroUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
            ? $accountingHeroUpload->temporaryUrl()
            : (string) ($accountingHeroImage['url'] ?? '');
    @endphp

    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naziv usluge</label>
                <input type="text" wire:model="form.translation_payload.hero.subtitle_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                @error('form.translation_payload.hero.subtitle_lead') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavna hero poruka</label>
                <textarea rows="5" wire:model="form.translation_payload.hero.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
                @error('form.translation_payload.hero.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis slike</label>
                <input type="text" wire:model="form.translation_payload.hero.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <p class="mt-1 text-xs text-slate-500">Kratko opišite sliku za korisnike čitača ekrana.</p>
                @error('form.translation_payload.hero.image_alt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                @if ($accountingHeroPreviewUrl !== '')
                    <img src="{{ $accountingHeroPreviewUrl }}" alt="" class="aspect-video w-full object-cover" />
                @endif
            </div>

            <div class="mt-3 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Hero slika</label>
                <span class="admin-chip">{{ ($accountingHeroImage['is_custom'] ?? false) ? 'Vlastita slika' : 'Zadana slika' }}</span>
            </div>
            <input
                type="file"
                wire:model="accountingHeroImageUpload"
                accept="image/jpeg,image/png,image/webp,image/avif,image/svg+xml"
                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
            />
            <p class="mt-1 text-xs text-slate-500">Preporučeni omjer je 16:9. Odabrana slika sprema se zajedno sa stranicom.</p>
            @error('accountingHeroImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

            @if (($accountingHeroImage['is_custom'] ?? false) && ! $accountingHeroUpload)
                <button
                    type="button"
                    wire:click="removeAccountingHeroImage"
                    wire:confirm="Ukloniti vlastitu hero sliku i vratiti zadanu?"
                    class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700"
                >
                    Vrati zadanu sliku
                </button>
            @endif
        </div>
    </div>
</div>

<div id="accounting-overview-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. Zašto je računovodstvo bitno</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Uvodna 50/50 sekcija</h2>
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
        <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        @error('form.translation_payload.overview.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst uvodne sekcije</label>
        <textarea
            id="accounting-overview-body"
            rows="10"
            wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"
            data-quill-editor
            data-quill-profile="service-text"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"
        >{{ $accountingOverviewBodyEditorHtml }}</textarea>
        <p class="mt-1 text-xs text-slate-500">Svi odlomci ostaju zajedno u desnoj polovici uvodne sekcije.</p>
        @error('form.translation_payload.overview.body_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div id="accounting-partner-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">3. Partnerska poruka</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Istaknuti tekst u tamnoj sekciji</h2>
        <p class="mt-1 text-sm text-slate-600">Cijeli sadržaj editora prikazuje se kao istaknuta poruka ispod uvoda.</p>
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst partnerske poruke</label>
        <textarea
            id="accounting-partner-body"
            rows="10"
            wire:model.live.debounce.300ms="form.translation_payload.overview.partner_body_html"
            data-quill-editor
            data-quill-profile="service-text"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"
        >{{ $accountingPartnerBodyEditorHtml }}</textarea>
        @error('form.translation_payload.overview.partner_body_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div id="accounting-services-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">4. Računovodstvene usluge</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Kartice usluga prikazane na stranici</h2>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.translation_payload.services.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.translation_payload.services.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Neobavezni uvod sekcije</label>
            <textarea rows="3" wire:model="form.translation_payload.services.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach (($translationPayload['services']['items'] ?? []) as $index => $item)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica usluge {{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('services.items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                    <input type="text" wire:model="form.translation_payload.services.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                    @error('form.translation_payload.services.items.'.$index.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label>
                    <textarea rows="6" wire:model="form.translation_payload.services.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                    @error('form.translation_payload.services.items.'.$index.'.text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" wire:click="addTranslationListItem('services.items', 'title_text')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
        Dodaj karticu usluge
    </button>
</div>

<div id="accounting-approach-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">5. Naš pristup</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Istaknuta citatna sekcija</h2>
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
        <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        @error('form.translation_payload.approach.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst citata</label>
        <textarea
            id="accounting-approach-body"
            rows="10"
            wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"
            data-quill-editor
            data-quill-profile="service-text"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"
        >{{ $accountingApproachBodyEditorHtml }}</textarea>
        @error('form.translation_payload.approach.body_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div id="accounting-blog-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">6. Stručne objave</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Tekstovi blog sekcije</h2>
        <p class="mt-1 text-sm text-slate-600">Kategorija i objave biraju se u kartici Izvori. Ovdje uređujete vidljive natpise.</p>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <p class="mt-1 text-xs text-slate-500">Možete koristiti <code>:category</code> za automatski naziv trenutačne blog kategorije.</p>
            @error('form.translation_payload.blog_section.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
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

<div id="accounting-meeting-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">7. Kontaktni poziv</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Završna kontaktna sekcija</h2>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov</label>
            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.translation_payload.meeting.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
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
