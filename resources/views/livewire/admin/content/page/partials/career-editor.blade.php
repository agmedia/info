@php
    $careerContent = is_array($form['career_content'] ?? null) ? $form['career_content'] : [];
    $careerIntro = is_array($careerContent['intro'] ?? null) ? $careerContent['intro'] : [];
    $careerProcess = is_array($careerContent['process'] ?? null) ? $careerContent['process'] : [];
    $careerStoriesSection = is_array($careerContent['stories_section'] ?? null) ? $careerContent['stories_section'] : [];
    $careerApplication = is_array($careerContent['application'] ?? null) ? $careerContent['application'] : [];
    $careerForm = is_array($careerContent['form'] ?? null) ? $careerContent['form'] : [];
    $careerHeroUpload = $pageHeroImageUpload ?? null;
    $careerHeroPreviewUrl = $careerHeroUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
        ? $careerHeroUpload->temporaryUrl()
        : (string) ($pageHeroImage['url'] ?? '');
@endphp

<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">Sadržaj s fronta</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Stranica Ljudski potencijali</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Sekcije su složene istim redom kao na frontu. Ovdje su samo tekstovi i hero slika koji se stvarno prikazuju na stranici Karijera.
            </p>
        </div>

        @if (trim((string) ($form['slug'] ?? '')) !== '')
            <a href="{{ route('pages.show', ['slug' => $form['slug']]) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50">
                Otvori front
                <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
            </a>
        @endif
    </div>

    <div class="mt-5 flex flex-wrap gap-2" aria-label="Navigacija po sekcijama stranice Karijera">
        <a href="#career-hero-admin" class="admin-chip">1. Uvod i hero</a>
        <a href="#career-development-admin" class="admin-chip">2. Razvoj i pogodnosti</a>
        <a href="#career-stories-admin" class="admin-chip">3. Život u timu</a>
        <a href="#career-applications-admin" class="admin-chip">4. Otvorene pozicije</a>
        <a href="#career-form-admin" class="admin-chip">5. Prijavna forma</a>
        <a href="#info-page-settings-admin" class="admin-chip">6. Postavke stranice</a>
    </div>
</div>

<div id="career-hero-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. Uvod i hero</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Vrh stranice Karijera</h2>
        <p class="mt-1 text-sm text-slate-600">Naslovi, uvodni odlomci, pogodnosti, gumb, fotografija i statistika prikazuju se na vrhu stranice.</p>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-2">
        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov prve sekcije</label>
                <input type="text" wire:model="form.career_content.intro.section_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti tekst prve sekcije</label>
                <input type="text" wire:model="form.career_content.intro.highlight" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni odlomak</label>
                <textarea rows="4" wire:model="form.career_content.intro.body.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka tamne hero sekcije</label>
                    <input type="text" wire:model="form.career_content.intro.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov tamne hero sekcije</label>
                    <input type="text" wire:model="form.career_content.intro.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
            </div>

            @foreach (array_slice((array) ($careerIntro['body'] ?? []), 1, null, true) as $paragraphIndex => $paragraph)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Hero odlomak {{ $paragraphIndex }}</label>
                    <textarea rows="5" wire:model="form.career_content.intro.body.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                </div>
            @endforeach

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naziv popisa pogodnosti</label>
                <input type="text" wire:model="form.career_content.intro.values_label" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach ((array) ($careerContent['values'] ?? []) as $valueIndex => $value)
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Pogodnost {{ $valueIndex + 1 }}</label>
                            <input type="text" wire:model="form.career_content.values.{{ $valueIndex }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst gumba za otvorene pozicije</label>
                <input type="text" wire:model="form.career_content.intro.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>

        <div>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                @if ($careerHeroPreviewUrl !== '')
                    <img src="{{ $careerHeroPreviewUrl }}" alt="" class="h-64 w-full object-cover sm:h-72" />
                @endif
            </div>
            <div class="mt-3 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Hero fotografija</label>
                <span class="admin-chip">{{ ($pageHeroImage['is_custom'] ?? false) ? 'Vlastita slika' : 'Zadana slika' }}</span>
            </div>
            <input type="file" wire:model="pageHeroImageUpload" accept="image/jpeg,image/png,image/webp,image/avif" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200" />
            <p class="mt-1 text-xs text-slate-500">Preporučeni omjer je približno 4:3. Nova slika sprema se zajedno sa stranicom.</p>
            @error('pageHeroImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

            @if (($pageHeroImage['is_custom'] ?? false) && ! $careerHeroUpload)
                <button type="button" wire:click="removePageHeroImage" wire:confirm="Ukloniti vlastitu hero sliku i vratiti zadanu?" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700">
                    Vrati zadanu sliku
                </button>
            @endif

            <div class="mt-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis fotografije</label>
                <input type="text" wire:model="form.career_content.intro.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <p class="mt-1 text-xs text-slate-500">Kratko opišite fotografiju za korisnike čitača ekrana.</p>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Broj</label>
                    <input type="text" wire:model="form.career_content.intro.stat_value" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis broja</label>
                    <textarea rows="4" wire:model="form.career_content.intro.stat_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="career-development-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. Razvoj i pogodnosti</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Razvoj koji nije samo fraza</h2>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov, prvi red</label>
            <input type="text" wire:model="form.career_content.process.title_line_one" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov, drugi red</label>
            <input type="text" wire:model="form.career_content.process.title_line_two" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti naslov</label>
            <input type="text" wire:model="form.career_content.process.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni tekst</label>
        <textarea rows="5" wire:model="form.career_content.process.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        @foreach ((array) ($careerProcess['steps'] ?? []) as $stepIndex => $step)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica {{ $stepIndex + 1 }}</p>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                    <input type="text" wire:model="form.career_content.process.steps.{{ $stepIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label>
                    <textarea rows="4" wire:model="form.career_content.process.steps.{{ $stepIndex }}.description" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="career-stories-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">3. Život u timu</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Naslov sekcije i tri kartice</h2>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.career_content.stories_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti tekst sekcije</label>
            <input type="text" wire:model="form.career_content.stories_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach ((array) ($careerContent['stories'] ?? []) as $storyIndex => $story)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica {{ $storyIndex + 1 }}</p>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka</label>
                    <input type="text" wire:model="form.career_content.stories.{{ $storyIndex }}.kicker" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
                    <input type="text" wire:model="form.career_content.stories.{{ $storyIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst kartice</label>
                    <textarea
                        id="career-story-body-{{ $storyIndex }}"
                        rows="10"
                        wire:model.live.debounce.300ms="form.career_content.stories.{{ $storyIndex }}.body_html"
                        data-quill-editor
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                    ></textarea>
                </div>
                @if ((array) ($story['list'] ?? []) !== [])
                    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Popis u kartici</p>
                        @foreach ((array) ($story['list'] ?? []) as $listIndex => $listItem)
                            <input type="text" wire:model="form.career_content.stories.{{ $storyIndex }}.list.{{ $listIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" aria-label="Stavka {{ $listIndex + 1 }}" />
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div id="career-applications-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">4. Otvorene pozicije</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Poziv na prijavu</h2>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka sekcije</label>
            <input type="text" wire:model="form.career_content.application.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label>
            <input type="text" wire:model="form.career_content.application.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti tekst</label>
            <input type="text" wire:model="form.career_content.application.highlight" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach ((array) ($careerApplication['paragraphs'] ?? []) as $paragraphIndex => $paragraph)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $paragraphIndex + 1 }}</label>
                <textarea rows="5" wire:model="form.career_content.application.paragraphs.{{ $paragraphIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
            </div>
        @endforeach
    </div>
</div>

<div id="career-form-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">5. Prijavna forma</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Tekstovi koji se prikazuju uz polja forme</h2>
        <p class="mt-1 text-sm text-slate-600">Sama polja, slanje prijave i validacija ostaju funkcionalno nepromijenjeni.</p>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov forme</label>
            <input type="text" wire:model="form.career_content.form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod forme</label>
            <textarea rows="3" wire:model="form.career_content.form.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            'first_name' => 'Oznaka polja za ime',
            'last_name' => 'Oznaka polja za prezime',
            'email' => 'Oznaka polja za email',
            'message' => 'Oznaka polja za poruku',
            'cv' => 'Oznaka polja za CV',
            'cv_button' => 'Tekst gumba za datoteku',
            'cv_empty' => 'Tekst bez odabrane datoteke',
            'submit' => 'Tekst gumba za slanje',
        ] as $field => $label)
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</label>
                <input type="text" wire:model="form.career_content.form.{{ $field }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        @endforeach
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Pomoćni tekst uz CV</label>
            <textarea rows="4" wire:model="form.career_content.form.cv_help" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Tekst privole</label>
            <textarea rows="4" wire:model="form.career_content.form.accept_terms" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
    </div>
</div>

@include('livewire.admin.content.page.partials.page-settings', ['settingsStep' => 6])
