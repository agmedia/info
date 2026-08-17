@php
    $aboutContent = is_array($form['about_content'] ?? null) ? $form['about_content'] : [];
    $aboutHero = is_array($aboutContent['hero'] ?? null) ? $aboutContent['hero'] : [];
    $aboutStory = is_array($aboutContent['story'] ?? null) ? $aboutContent['story'] : [];
    $aboutValues = is_array($aboutContent['values'] ?? null) ? $aboutContent['values'] : [];
    $aboutWhy = is_array($aboutContent['why'] ?? null) ? $aboutContent['why'] : [];
    $aboutTeam = is_array($aboutContent['team'] ?? null) ? $aboutContent['team'] : [];
    $aboutCulture = is_array($aboutContent['culture'] ?? null) ? $aboutContent['culture'] : [];
    $aboutResponsibility = is_array($aboutContent['responsibility'] ?? null) ? $aboutContent['responsibility'] : [];
    $aboutReferences = is_array($aboutContent['references'] ?? null) ? $aboutContent['references'] : [];
    $aboutHeroUpload = $pageHeroImageUpload ?? null;
    $aboutHeroPreviewUrl = $aboutHeroUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
        ? $aboutHeroUpload->temporaryUrl()
        : (string) ($pageHeroImage['url'] ?? '');
@endphp

<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">Sadržaj s fronta</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Stranica O nama</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Sekcije su složene istim redom kao na frontu. Ovdje su samo tekstovi i hero slika koji se stvarno prikazuju na stranici O nama.
            </p>
        </div>

        @if (trim((string) ($form['slug'] ?? '')) !== '')
            <a href="{{ route('pages.show', ['slug' => $form['slug']]) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50">
                Otvori front
                <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
            </a>
        @endif
    </div>

    <div class="mt-5 flex flex-wrap gap-2" aria-label="Navigacija po sekcijama stranice O nama">
        <a href="#about-story-admin" class="admin-chip">1. Uvod i naša priča</a>
        <a href="#about-values-admin" class="admin-chip">2. Naše vrijednosti</a>
        <a href="#about-why-admin" class="admin-chip">3. Zašto postojimo</a>
        <a href="#about-team-admin" class="admin-chip">4. Naš tim</a>
        <a href="#about-culture-admin" class="admin-chip">5. Naša kultura</a>
        <a href="#about-responsibility-admin" class="admin-chip">6. Društvena odgovornost</a>
        <a href="#about-cta-admin" class="admin-chip">7. Kontaktni poziv</a>
        <a href="#about-references-admin" class="admin-chip">8. Reference</a>
        <a href="#info-page-settings-admin" class="admin-chip">9. Postavke stranice</a>
    </div>
</div>

<div id="about-story-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. Uvod i naša priča</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Vrh stranice O nama</h2>
        <p class="mt-1 text-sm text-slate-600">Naslov, priča, hero fotografija i statistika prikazuju se na samom vrhu stranice.</p>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-2">
        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov</label>
                <input type="text" wire:model="form.about_content.hero.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti naslov uz fotografiju</label>
                <textarea rows="3" wire:model="form.about_content.hero.lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
            </div>

            <div class="space-y-3">
                @foreach ((array) ($aboutStory['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ $paragraphIndex === 0 ? 'Uvodni odlomak uz glavni naslov' : 'Odlomak priče '.$paragraphIndex }}
                        </label>
                        <textarea rows="5" wire:model="form.about_content.story.paragraphs.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                @if ($aboutHeroPreviewUrl !== '')
                    <img src="{{ $aboutHeroPreviewUrl }}" alt="" class="h-64 w-full object-cover sm:h-72" />
                @endif
            </div>

            <div class="mt-3 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Hero fotografija</label>
                <span class="admin-chip">{{ ($pageHeroImage['is_custom'] ?? false) ? 'Vlastita slika' : 'Zadana slika' }}</span>
            </div>
            <input type="file" wire:model="pageHeroImageUpload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200" />
            <p class="mt-1 text-xs text-slate-500">Preporučeni omjer je približno 4:3. Nova slika sprema se zajedno sa stranicom.</p>
            @error('pageHeroImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

            @if (($pageHeroImage['is_custom'] ?? false) && ! $aboutHeroUpload)
                <button type="button" wire:click="removePageHeroImage" wire:confirm="Ukloniti vlastitu hero sliku i vratiti zadanu?" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700">
                    Vrati zadanu sliku
                </button>
            @endif

            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis fotografije</label>
                <input type="text" wire:model="form.about_content.hero.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <p class="mt-1 text-xs text-slate-500">Kratko opišite fotografiju za korisnike čitača ekrana.</p>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Broj</label>
                    <input type="text" wire:model="form.about_content.hero.stat_value" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis broja</label>
                    <textarea rows="3" wire:model="form.about_content.hero.stat_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="about-values-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. Naše vrijednosti</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Uvod i tri kartice vrijednosti</h2>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.about_content.values.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="lg:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.about_content.values.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni tekst</label>
        <textarea rows="5" wire:model="form.about_content.values.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach ((array) ($aboutValues['items'] ?? []) as $itemIndex => $item)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Vrijednost {{ $itemIndex + 1 }}</p>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                    <input type="text" wire:model="form.about_content.values.items.{{ $itemIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti tekst</label>
                    <textarea rows="3" wire:model="form.about_content.values.items.{{ $itemIndex }}.lead" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"></textarea>
                </div>
                @foreach ((array) ($item['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $paragraphIndex + 1 }}</label>
                        <textarea rows="5" wire:model="form.about_content.values.items.{{ $itemIndex }}.paragraphs.{{ $paragraphIndex }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<div id="about-why-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">3. Zašto postojimo</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Misija i poslovna podrška</h2>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.about_content.why.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.about_content.why.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti citat</label>
        <textarea rows="4" wire:model="form.about_content.why.quote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>
    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        @foreach ((array) ($aboutWhy['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $paragraphIndex + 1 }}</label>
                <textarea rows="5" wire:model="form.about_content.why.paragraphs.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
            </div>
        @endforeach
    </div>
</div>

<div id="about-team-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">4. Naš tim</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">Uvod, statistike i gumb prema cijelom timu</h2>
            <p class="mt-1 text-sm text-slate-600">Imena, funkcije i fotografije članova uređuju se odvojeno u CMS-u za tim.</p>
        </div>
        <a href="{{ route('admin.content.team.index', ['locale' => $form['locale']]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            Uredi članove tima
            <i class="fa-light fa-arrow-right" aria-hidden="true"></i>
        </a>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.about_content.team.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.about_content.team.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni tekst</label>
            <textarea rows="5" wire:model="form.about_content.team.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Dodatni tekst</label>
            <textarea rows="5" wire:model="form.about_content.team.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ((array) ($aboutTeam['stats'] ?? []) as $statIndex => $stat)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Statistika {{ $statIndex + 1 }}</p>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Vrijednost</label>
                    <input type="text" wire:model="form.about_content.team.stats.{{ $statIndex }}.value" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label>
                    <textarea rows="3" wire:model="form.about_content.team.stats.{{ $statIndex }}.label" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4 max-w-xl">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst gumba za cijeli tim</label>
        <input type="text" wire:model="form.about_content.team.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>
</div>

<div id="about-culture-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">5. Naša kultura</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Kultura i odnosi u timu</h2>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.about_content.culture.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.about_content.culture.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti citat</label>
        <textarea rows="4" wire:model="form.about_content.culture.quote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>
    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        @foreach ((array) ($aboutCulture['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $paragraphIndex + 1 }}</label>
                <textarea rows="5" wire:model="form.about_content.culture.paragraphs.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
            </div>
        @endforeach
    </div>
</div>

<div id="about-responsibility-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">6. Društvena odgovornost</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">AUXILIUM CAPITALIS</h2>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.about_content.responsibility.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.about_content.responsibility.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti citat</label>
        <textarea rows="4" wire:model="form.about_content.responsibility.quote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>
    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        @foreach ((array) ($aboutResponsibility['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $paragraphIndex + 1 }}</label>
                <textarea rows="5" wire:model="form.about_content.responsibility.paragraphs.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
            </div>
        @endforeach
    </div>
</div>

<div id="about-cta-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">7. Kontaktni poziv</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Poziv na partnerstvo</h2>
        <p class="mt-1 text-sm text-slate-600">Gumb uvijek vodi na stranicu Kontakt.</p>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov</label>
            <input type="text" wire:model="form.about_content.responsibility.cta_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov kartice</label>
            <input type="text" wire:model="form.about_content.responsibility.cta_card_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst kartice</label>
        <textarea rows="5" wire:model="form.about_content.responsibility.cta_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst gumba</label>
            <input type="text" wire:model="form.about_content.responsibility.cta_button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Status ispod gumba</label>
            <input type="text" wire:model="form.about_content.responsibility.cta_status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
</div>

<div id="about-references-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">8. Reference</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Uvod u reference i poveznica</h2>
        <p class="mt-1 text-sm text-slate-600">Logotipi se automatski povlače sa zasebne stranice Reference; ovdje se uređuju tekstovi sekcije.</p>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.about_content.references.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.about_content.references.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        @foreach ((array) ($aboutReferences['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $paragraphIndex + 1 }}</label>
                <textarea rows="5" wire:model="form.about_content.references.paragraphs.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
            </div>
        @endforeach
    </div>
    <div class="mt-4 max-w-xl">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst gumba za sve reference</label>
        <input type="text" wire:model="form.about_content.references.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>
</div>

@include('livewire.admin.content.page.partials.page-settings', ['settingsStep' => 9])
