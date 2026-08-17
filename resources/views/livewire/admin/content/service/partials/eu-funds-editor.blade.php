@php
    $euFundsHeroUpload = $euFundsHeroImageUpload ?? null;
    $euFundsHeroPreviewUrl = $euFundsHeroUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
        ? $euFundsHeroUpload->temporaryUrl()
        : (string) ($euFundsHeroImage['url'] ?? '');
@endphp

<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">Sadržaj s fronta</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Stranica EU fondovi</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Sekcije su složene istim redom kao na frontu. Prikazana su samo polja koja se stvarno vide na stranici.</p>
        </div>
        <a href="{{ route('eu-funds.show') }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50">Otvori front <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
    </div>
    <div class="mt-5 flex flex-wrap gap-2" aria-label="Navigacija po sekcijama stranice EU fondovi">
        <a href="#eu-funds-hero-admin" class="admin-chip">1. Hero i slika</a>
        <a href="#eu-funds-overview-admin" class="admin-chip">2. Što su EU fondovi</a>
        <a href="#eu-funds-process-admin" class="admin-chip">3. Naše usluge</a>
        <a href="#eu-funds-approach-admin" class="admin-chip">4. Naš pristup</a>
        <a href="#eu-funds-sources-admin" class="admin-chip">5. Izvori financiranja</a>
        <a href="#eu-funds-calls-admin" class="admin-chip">6. Natječaji</a>
        <a href="#eu-funds-resources-admin" class="admin-chip">7. Programi i instrumenti</a>
        <a href="#eu-funds-laws-admin" class="admin-chip">8. Zakoni i olakšice</a>
        <a href="#eu-funds-blog-admin" class="admin-chip">9. Stručne objave</a>
        <a href="#eu-funds-meeting-admin" class="admin-chip">10. Kontaktni poziv</a>
        <a href="#eu-funds-settings-admin" class="admin-chip">11. Postavke stranice</a>
    </div>
</div>

<div id="eu-funds-hero-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. Hero i slika</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Vrh stranice EU fondovi</h2></div>
    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naziv usluge</label><input type="text" wire:model="form.translation_payload.hero.subtitle_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
                <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuti dio naziva</label><input type="text" wire:model="form.translation_payload.hero.subtitle_accent" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /><p class="mt-1 text-xs text-slate-500">Ostavite prazno ako cijeli naziv treba biti u jednom retku.</p></div>
            </div>
            <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavna hero poruka</label><textarea rows="6" wire:model="form.translation_payload.hero.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div>
            <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Alternativni opis slike</label><input type="text" wire:model="form.translation_payload.hero.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        </div>
        <div>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">@if ($euFundsHeroPreviewUrl !== '')<img src="{{ $euFundsHeroPreviewUrl }}" alt="" class="aspect-video w-full object-cover" />@endif</div>
            <div class="mt-3 flex items-center justify-between gap-3"><label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Hero slika</label><span class="admin-chip">{{ ($euFundsHeroImage['is_custom'] ?? false) ? 'Vlastita slika' : 'Zadana slika' }}</span></div>
            <input type="file" wire:model="euFundsHeroImageUpload" accept="image/jpeg,image/png,image/webp,image/avif,image/svg+xml" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200" />
            <p class="mt-1 text-xs text-slate-500">Preporučeni omjer je 16:9.</p>
            @error('euFundsHeroImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            @if (($euFundsHeroImage['is_custom'] ?? false) && ! $euFundsHeroUpload)<button type="button" wire:click="removeEuFundsHeroImage" wire:confirm="Ukloniti vlastitu hero sliku i vratiti zadanu?" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700">Vrati zadanu sliku</button>@endif
        </div>
    </div>
</div>

<div id="eu-funds-overview-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. Što su EU fondovi</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Uvodna 50/50 sekcija</h2></div>
    <div class="mt-5"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
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

<div id="eu-funds-process-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">3. Naše usluge</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Kartice usluga EU fondova</h2></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.process.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod sekcije</label><textarea rows="3" wire:model="form.translation_payload.process.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div>
    </div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach (($translationPayload['process']['items'] ?? []) as $index => $item)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><div class="flex items-center justify-between gap-3"><p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica usluge {{ $index + 1 }}</p><button type="button" wire:click="removeTranslationListItem('process.items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button></div><label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.process.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /><label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label><textarea rows="6" wire:model="form.translation_payload.process.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea></div>
        @endforeach
    </div>
    <button type="button" wire:click="addTranslationListItem('process.items', 'eu_process_item')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj karticu usluge</button>
</div>

<div id="eu-funds-approach-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">4. Naš pristup</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Istaknuta citatna sekcija</h2></div>
    <div class="mt-5"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
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

<div id="eu-funds-sources-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">5. Izvori financiranja</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Navigacijske kartice izvora</h2></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2"><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.source_modules.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod sekcije</label><textarea rows="3" wire:model="form.translation_payload.source_modules.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3">@foreach (($translationPayload['source_modules']['items'] ?? []) as $index => $item)<div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><div class="flex items-center justify-between gap-3"><p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica izvora {{ $index + 1 }}</p><button type="button" wire:click="removeTranslationListItem('source_modules.items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button></div><label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.source_modules.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /><label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Opis</label><textarea rows="5" wire:model="form.translation_payload.source_modules.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea><label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Poveznica</label><input type="text" wire:model="form.translation_payload.source_modules.items.{{ $index }}.url" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>@endforeach</div>
    <button type="button" wire:click="addTranslationListItem('source_modules.items', 'service_card')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj karticu izvora</button>
</div>

<div id="eu-funds-calls-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">6. Natječaji</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Naslovi iznad aktualnih natječaja</h2><p class="mt-1 text-sm text-slate-600">Kartice automatski dolaze iz sadržaja Natječaji. Ovdje uređujete vidljive natpise sekcije.</p></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2"><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Oznaka iznad naslova</label><input type="text" wire:model="form.translation_payload.calls.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.calls.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div class="xl:col-span-2"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod</label><textarea rows="4" wire:model="form.translation_payload.calls.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis za prikaz svih natječaja</label><input type="text" wire:model="form.translation_payload.calls.view_all_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div></div>
</div>

@include('livewire.admin.content.service.partials.eu-funds-resources-editor')
@include('livewire.admin.content.service.partials.eu-funds-laws-editor')

<div id="eu-funds-blog-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">9. Stručne objave</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Vidljivi natpisi blog sekcije</h2><p class="mt-1 text-sm text-slate-600">Kategorija i objave biraju se u kartici Izvori.</p></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-3"><div class="xl:col-span-3"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /><p class="mt-1 text-xs text-slate-500">Možete koristiti <code>:category</code> za automatski naziv blog kategorije.</p></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis poveznice svih objava</label><input type="text" wire:model="form.translation_payload.blog_section.all_posts_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis na kartici objave</label><input type="text" wire:model="form.translation_payload.blog_section.post_action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div></div>
</div>

<div id="eu-funds-meeting-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">10. Kontaktni poziv</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Završna kontaktna sekcija</h2></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2"><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Glavni naslov</label><input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov kontaktne kartice</label><input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div class="xl:col-span-2"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kontaktni tekst</label><textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Natpis gumba</label><input type="text" wire:model="form.translation_payload.meeting.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div><div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Napomena uz termin</label><input type="text" wire:model="form.translation_payload.meeting.status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div></div>
</div>
