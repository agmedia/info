<div id="eu-funds-laws-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">8. Zakoni i olakšice</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Porezne olakšice, zakoni i uredbe</h2></div>
    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov sekcije</label><input type="text" wire:model="form.translation_payload.laws.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
        <div><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Uvod sekcije</label><textarea rows="4" wire:model="form.translation_payload.laws.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea></div>
    </div>
    <div class="mt-6 space-y-5">
        @foreach (($translationPayload['laws']['cards'] ?? []) as $cardIndex => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-cyan-700">Kartica zakona {{ $cardIndex + 1 }}</p>
                <div class="mt-3"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov</label><input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" /></div>
                <div class="mt-3"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Sažetak</label><textarea rows="4" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.summary" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea></div>
                @foreach (($card['lists'] ?? []) as $listIndex => $list)
                    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Naslov popisa {{ $listIndex + 1 }}</label>
                        <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <div class="mt-3 space-y-3">
                            @foreach (($list['items'] ?? []) as $itemIndex => $item)
                                <div><div class="flex items-center justify-between gap-3"><label class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Stavka {{ $itemIndex + 1 }}</label><button type="button" wire:click="removeTranslationListItem('laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.items', {{ $itemIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Ukloni</button></div><input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.items.{{ $itemIndex }}" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" /></div>
                            @endforeach
                        </div>
                        <button type="button" wire:click="addTranslationListItem('laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.items')" class="mt-3 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Dodaj stavku</button>
                    </div>
                @endforeach
                <div class="mt-4"><label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Istaknuta napomena</label><textarea rows="3" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.note" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea></div>
                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    @include('livewire.admin.content.service.partials.eu-funds-link-editor', ['heading' => 'Glavna poveznica', 'basePath' => 'laws.cards.'.$cardIndex.'.primary_link', 'link' => (array) ($card['primary_link'] ?? [])])
                    @include('livewire.admin.content.service.partials.eu-funds-link-editor', ['heading' => 'Dodatna poveznica', 'basePath' => 'laws.cards.'.$cardIndex.'.secondary_link', 'link' => (array) ($card['secondary_link'] ?? [])])
                </div>
            </div>
        @endforeach
    </div>
</div>
