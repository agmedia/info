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
        <div class="mt-4 space-y-3">
            @foreach (($detail['overview_body'] ?? []) as $index => $paragraph)
                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $index + 1 }}</label>
                        <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.overview_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                    </div>
                    <textarea rows="5" wire:model="form.translation_payload.{{ $detailKey }}.overview_body.{{ $index }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.overview_body')" class="mt-3 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj uvodni odlomak</button>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 p-5">
        <h3 class="text-base font-semibold text-slate-900">Naše usluge</h3>
        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="mt-4 space-y-3">
            @foreach (($detail['services_body'] ?? []) as $index => $paragraph)
                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvodni odlomak {{ $index + 1 }}</label>
                        <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.services_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                    </div>
                    <textarea rows="5" wire:model="form.translation_payload.{{ $detailKey }}.services_body.{{ $index }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.services_body')" class="mt-3 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj uvodni odlomak</button>

        <div class="mt-6 border-t border-slate-200 pt-5">
            <div class="flex items-center justify-between gap-3">
                <h4 class="text-sm font-semibold text-slate-900">Kartice usluga</h4>
                <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.help_items')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Dodaj karticu</button>
            </div>
            <div class="mt-4 grid gap-3 lg:grid-cols-3">
                @foreach (($detail['help_items'] ?? []) as $index => $item)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <label class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kartica {{ $index + 1 }}</label>
                            <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.help_items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                        </div>
                        <textarea rows="3" wire:model="form.translation_payload.{{ $detailKey }}.help_items.{{ $index }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 p-5">
        <h3 class="text-base font-semibold text-slate-900">Naš pristup</h3>
        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label>
            <input type="text" wire:model="form.translation_payload.{{ $detailKey }}.approach_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="mt-4 space-y-3">
            @foreach (($detail['approach_body'] ?? []) as $index => $paragraph)
                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Odlomak {{ $index + 1 }}</label>
                        <button type="button" wire:click="removeTranslationListItem('{{ $detailKey }}.approach_body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button>
                    </div>
                    <textarea rows="5" wire:model="form.translation_payload.{{ $detailKey }}.approach_body.{{ $index }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addTranslationListItem('{{ $detailKey }}.approach_body')" class="mt-3 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dodaj odlomak pristupa</button>
    </div>
</div>
