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
